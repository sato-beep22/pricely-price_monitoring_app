<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Crop;
use App\Models\Price;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PriceImportController extends Controller
{
    /**
     * Show the admin CSV price import form.
     */
    public function index(): View
    {
        $crops = Crop::orderBy('name')->get();
        $sourceLinks = Setting::where('key', 'like', 'da_price_source_link_%')
            ->pluck('value', 'key')
            ->toArray();

        return view('admin.price-import.index', compact('crops', 'sourceLinks'));
    }

    /**
     * Process and import the uploaded CSV price file.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');

        // Read and discard the header row
        $header = fgetcsv($handle);

        $expectedHeaders = ['crop', 'specification', 'price_per_kg', 'recorded_at'];
        $normalizedHeader = array_map('strtolower', array_map('trim', $header ?? []));

        if ($normalizedHeader !== $expectedHeaders) {
            fclose($handle);

            return back()->withErrors([
                'csv_file' => 'Invalid CSV format. Expected columns: crop, specification, price_per_kg, recorded_at.',
            ]);
        }

        // Cache all crops indexed by lowercase name for fast lookup
        $crops = Crop::all()->keyBy(fn ($c) => strtolower(trim($c->name)));

        $imported = 0;
        $skipped = 0;
        $errors = [];
        $row = 1;

        while (($line = fgetcsv($handle)) !== false) {
            $row++;

            if (count($line) < 4) {
                $errors[] = "Row {$row}: Not enough columns, skipped.";
                $skipped++;

                continue;
            }

            [$cropName, $specification, $pricePerKg, $recordedAt] = array_map('trim', $line);

            $crop = $crops->get(strtolower($cropName));

            if (! $crop) {
                $errors[] = "Row {$row}: Crop \"{$cropName}\" not found in the system.";
                $skipped++;

                continue;
            }

            if (! is_numeric($pricePerKg) || (float) $pricePerKg <= 0) {
                $errors[] = "Row {$row}: Invalid price \"{$pricePerKg}\".";
                $skipped++;

                continue;
            }

            try {
                $date = Carbon::parse($recordedAt)->startOfDay();
            } catch (\Exception) {
                $errors[] = "Row {$row}: Invalid date \"{$recordedAt}\".";
                $skipped++;

                continue;
            }

            Price::create([
                'shop_id' => null,
                'crop_id' => $crop->id,
                'specification' => strtolower($specification),
                'price_per_kg' => (float) $pricePerKg,
                'recorded_at' => $date,
                'source' => 'admin_import',
            ]);

            $imported++;
        }

        fclose($handle);

        $message = "{$imported} price record(s) imported successfully.";
        if ($skipped > 0) {
            $message .= " {$skipped} row(s) were skipped.";
        }

        return back()
            ->with('import_success', $message)
            ->with('import_errors', $errors);
    }

    /**
     * Update the DA Price Source Link.
     */
    public function updateSourceLink(Request $request): RedirectResponse
    {
        $request->validate([
            'source_links' => ['nullable', 'array'],
            'source_links.*' => ['nullable', 'url', 'max:2048'],
        ]);

        $links = $request->input('source_links', []);
        foreach ($links as $cropId => $url) {
            if ($url) {
                Setting::updateOrCreate(
                    ['key' => 'da_price_source_link_'.$cropId],
                    ['value' => $url]
                );
            } else {
                Setting::where('key', 'da_price_source_link_'.$cropId)->delete();
            }
        }

        return back()->with('link_success', 'Source links updated successfully.');
    }
}
