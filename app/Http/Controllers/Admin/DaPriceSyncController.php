<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CeilingPrice;
use App\Models\Crop;
use App\Services\DaPriceSyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DaPriceSyncController extends Controller
{
    public function __construct(private readonly DaPriceSyncService $syncService) {}

    /**
     * Fetch and preview ceiling prices extracted from a DA price monitoring URL.
     */
    public function preview(Request $request): JsonResponse
    {
        $request->validate([
            'url' => ['required', 'url', 'max:2048'],
        ]);

        $result = $this->syncService->syncFromUrl($request->string('url')->toString());

        if (! $result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['error'],
            ], 422);
        }

        if (empty($result['prices'])) {
            return response()->json([
                'success' => false,
                'message' => 'No ceiling price data was found on that page. Try a more specific DA price bulletin URL.',
            ], 422);
        }

        // Enrich with local crop matching status
        $crops = Crop::all()->keyBy(fn ($c) => strtolower(trim($c->name)));
        $enriched = [];

        foreach ($result['prices'] as $row) {
            $matchedCrop = $crops->get(strtolower($row['crop']));
            $enriched[] = [
                'crop' => $row['crop'],
                'specification' => $row['specification'],
                'max_price' => $row['max_price'],
                'matched_crop_id' => $matchedCrop?->id,
                'matched_crop_name' => $matchedCrop?->name,
                'status' => $matchedCrop ? 'matched' : 'unmatched',
            ];
        }

        return response()->json([
            'success' => true,
            'prices' => $enriched,
            'source_url' => $result['source_url'],
            'total' => count($enriched),
            'matched' => collect($enriched)->where('status', 'matched')->count(),
        ]);
    }

    /**
     * Apply the confirmed ceiling prices from the AI sync to the database.
     */
    public function apply(Request $request): JsonResponse
    {
        $request->validate([
            'prices' => ['required', 'array', 'min:1'],
            'prices.*.matched_crop_id' => ['required', 'integer', 'exists:crops,id'],
            'prices.*.specification' => ['required', 'string', 'max:100'],
            'prices.*.max_price' => ['required', 'numeric', 'min:0.01'],
            'source_url' => ['nullable', 'url', 'max:2048'],
            'effective_date' => ['required', 'date'],
        ]);

        $saved = 0;
        $adminId = Auth::id();
        $effectiveDate = $request->date('effective_date');
        $notes = $request->input('source_url')
            ? 'AI synced from: '.$request->input('source_url')
            : 'AI synced from DA price monitoring.';

        foreach ($request->input('prices') as $row) {
            $crop = Crop::find($row['matched_crop_id']);
            if (! $crop) {
                continue;
            }

            $specification = strtolower(trim($row['specification']));

            // Ensure the specification exists on the crop model
            $existingSpecs = array_map('trim', explode(',', $crop->specification ?? ''));
            if (! in_array($specification, $existingSpecs)) {
                $existingSpecs[] = $specification;
                $crop->specification = implode(',', array_filter($existingSpecs));
                $crop->save();
            }

            CeilingPrice::updateOrCreate(
                [
                    'crop_id' => $crop->id,
                    'specification' => $specification,
                ],
                [
                    'admin_id' => $adminId,
                    'max_price' => (float) $row['max_price'],
                    'effective_date' => $effectiveDate,
                    'notes' => $notes,
                ]
            );

            $saved++;
        }

        return response()->json([
            'success' => true,
            'message' => "{$saved} ceiling price(s) updated successfully.",
        ]);
    }
}
