<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Order;

class VendorDashboardController extends Controller
{
    public function index()
    {
        $vendorId = Auth::user()->vendor_id;

        // Query orders belonging to this vendor only
        $orders = Order::where('vendor_id', $vendorId)->latest()->take(10)->get();

        // Dashboard summaries
        $dashboardDatas = (object) [
            'Total' => Order::where('vendor_id', $vendorId)->count(),
            'TotalAmount' => Order::where('vendor_id', $vendorId)->sum('total'),
            'TotalOrdered' => Order::where('vendor_id', $vendorId)->where('status', 'ordered')->count(),
            'TotalOrderedAmount' => Order::where('vendor_id', $vendorId)->where('status', 'ordered')->sum('total'),
            'TotalDelivered' => Order::where('vendor_id', $vendorId)->where('status', 'delivered')->count(),
            'TotalDeliveredAmount' => Order::where('vendor_id', $vendorId)->where('status', 'delivered')->sum('total'),
            'TotalCanceled' => Order::where('vendor_id', $vendorId)->where('status', 'canceled')->count(),
            'TotalCanceledAmount' => Order::where('vendor_id', $vendorId)->where('status', 'canceled')->sum('total'),
        ];

        // Monthly revenue data for chart
        $AmountM = $this->monthlyRevenue($vendorId, 'total');
        $OrderedAmountM = $this->monthlyRevenue($vendorId, 'ordered');
        $DeliveredAmountM = $this->monthlyRevenue($vendorId, 'delivered');
        $CanceledAmountM = $this->monthlyRevenue($vendorId, 'canceled');

        $TotalAmount = $dashboardDatas->TotalAmount;
        $TotalOrderedAmount = $dashboardDatas->TotalOrderedAmount;
        $TotalDeliveredAmount = $dashboardDatas->TotalDeliveredAmount;
        $TotalCanceledAmount = $dashboardDatas->TotalCanceledAmount;

        return view('vendor.dashboard', compact(
            'dashboardDatas',
            'orders',
            'AmountM',
            'OrderedAmountM',
            'DeliveredAmountM',
            'CanceledAmountM',
            'TotalAmount',
            'TotalOrderedAmount',
            'TotalDeliveredAmount',
            'TotalCanceledAmount'
        ));
    }

    /**
     * Helper: compute monthly revenue for each month for the logged-in vendor.
     */
    private function monthlyRevenue($vendorId, $status = 'total')
    {
        $query = Order::select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(total) as amount'))
            ->where('vendor_id', $vendorId);

        if ($status !== 'total') {
            $query->where('status', $status);
        }

        $data = $query->groupBy('month')->get();

        // Fill all 12 months with 0 if not available
        $amounts = [];
        for ($i = 1; $i <= 12; $i++) {
            $amounts[] = $data->firstWhere('month', $i)->amount ?? 0;
        }

        return implode(',', $amounts);
    }
}
