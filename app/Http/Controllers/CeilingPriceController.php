<?php

namespace App\Http\Controllers;

use App\Events\CeilingPriceUpdated;
use App\Models\CeilingPrice;
use App\Models\Crop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CeilingPriceController extends Controller
{
    /**
     * Display a listing of ceiling prices.
     */
    public function index()
    {
        $ceilingPrices = CeilingPrice::with(['crop', 'admin'])
            ->orderBy('crop_id')
            ->orderBy('specification')
            ->get();
        $crops = Crop::all();

        return view('admin.ceiling-prices.index', compact('ceilingPrices', 'crops'));
    }

    /**
     * Store a newly created ceiling price.
     */
    public function store(Request $request)
    {
        $request->validate([
            'crop_id' => 'required|exists:crops,id',
            'specification' => 'required|string',
            'manual_specification' => 'nullable|string|required_if:specification,manual',
            'max_price' => 'required|numeric|min:0',
            'effective_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $crop = Crop::findOrFail($request->crop_id);
        $specification = $request->specification;

        if ($specification === 'manual' && $request->filled('manual_specification')) {
            $specification = strtolower(trim($request->manual_specification));

            $existingSpecs = array_map('trim', explode(',', $crop->specification));
            if (! in_array($specification, $existingSpecs)) {
                $existingSpecs[] = $specification;
                $crop->specification = implode(',', array_filter($existingSpecs));
                $crop->save();
            }
        }

        // Capture old price before saving so the SMS can show the change.
        $existing = CeilingPrice::where('crop_id', $crop->id)
            ->where('specification', $specification)
            ->first();

        $oldMaxPrice = $existing?->max_price;

        CeilingPrice::updateOrCreate(
            [
                'crop_id' => $crop->id,
                'specification' => $specification,
            ],
            [
                'admin_id' => Auth::id(),
                'max_price' => $request->max_price,
                'effective_date' => $request->effective_date,
                'notes' => $request->notes,
            ]
        );

        CeilingPriceUpdated::dispatch([
            [
                'crop_name' => $crop->name,
                'specification' => $specification !== 'all' ? $specification : null,
                'old_max_price' => $oldMaxPrice,
                'new_max_price' => $request->max_price,
            ],
        ]);

        return redirect()->route('admin.ceiling-prices.index')->with('status', 'Ceiling price set successfully.');
    }
}
