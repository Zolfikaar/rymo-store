<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderDashboardResource;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $orders = Order::query()
            ->with(['items.product'])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Dashboard', [
            'orders' => OrderDashboardResource::collection($orders),
            'stats' => [
                'total_sales' => number_format(
                    (float) Order::query()->where('status', 'completed')->sum('total_price'),
                    2,
                ),
                'pending_orders_count' => Order::query()->where('status', 'pending')->count(),
                'total_products' => Product::query()->count(),
            ],
        ]);
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(['pending', 'completed', 'canceled'])],
        ]);

        $order->update([
            'status' => $validated['status'],
        ]);

        return redirect()->back();
    }
}
