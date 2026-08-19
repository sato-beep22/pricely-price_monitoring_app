<?php

namespace App\Http\Controllers;

use App\Models\Crop;
use App\Models\Price;
use App\Models\Shop;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ReportController extends Controller
{
    /**
     * Show the price trend reports page.
     */
    public function index(Request $request)
    {
        $crops = Crop::all();
        $selectedCrop = $request->input('crop_id', $crops->first()->id ?? null);
        $period = $request->input('period', '30'); // days

        $startDate = Carbon::now()->subDays($period)->startOfDay();

        $shops = Shop::all();
        $varieties = Price::whereNotNull('specification')->where('specification', '!=', '')->select('specification')->distinct()->pluck('specification');
        $selectedShop = $request->input('shop_id');
        $selectedVariety = $request->input('variety');

        $prices = new LengthAwarePaginator([], 0, 20);
        if ($selectedCrop) {
            $query = Price::where('crop_id', $selectedCrop)
                ->where('recorded_at', '>=', $startDate)
                ->with('shop');

            if ($selectedShop) {
                $query->where('shop_id', $selectedShop);
            }
            if ($selectedVariety) {
                $query->where('specification', $selectedVariety);
            }

            $prices = $query->orderBy('recorded_at', 'desc')->paginate(20)->withQueryString();
        }

        return view('reports.index', compact('crops', 'shops', 'varieties', 'selectedCrop', 'selectedShop', 'selectedVariety', 'period', 'prices'));
    }

    /**
     * Export the price trend reports to CSV.
     */
    public function export(Request $request)
    {
        $crops = Crop::all();
        $selectedCrop = $request->input('crop_id', $crops->first()->id ?? null);
        $period = $request->input('period', '30'); // days

        $startDate = Carbon::now()->subDays($period)->startOfDay();

        $selectedShop = $request->input('shop_id');
        $selectedVariety = $request->input('variety');

        $prices = collect();
        if ($selectedCrop) {
            $query = Price::where('crop_id', $selectedCrop)
                ->where('recorded_at', '>=', $startDate)
                ->with('shop');

            if ($selectedShop) {
                $query->where('shop_id', $selectedShop);
            }
            if ($selectedVariety) {
                $query->where('specification', $selectedVariety);
            }

            $prices = $query->orderBy('recorded_at', 'desc')->get();
        }

        $cropName = $crops->where('id', $selectedCrop)->first()->name ?? 'All_Crops';
        $fileName = 'price_report_'.strtolower(str_replace(' ', '_', $cropName)).'_'.date('Y_m_d').'.csv';

        return response()->streamDownload(function () use ($prices) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'Shop / Buyer', 'Location', 'Variety', 'Price per kg']);

            foreach ($prices as $price) {
                fputcsv($file, [
                    $price->recorded_at->format('M d, Y h:i A'),
                    $price->shop->name ?? 'Unknown',
                    $price->shop->address ?? '-',
                    ucfirst($price->specification ?? ''),
                    $price->price_per_kg,
                ]);
            }
            fclose($file);
        }, $fileName);
    }
}
