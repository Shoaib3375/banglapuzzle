<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Place a new order (guest or authenticated).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'district' => ['required', 'string', 'max:100'],
            'postcode' => ['nullable', 'string', 'max:20'],
            'landmark' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string', 'max:500'],
            'payment_method' => ['nullable', 'string', 'in:cod,bkash,nagad'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        // Load products from DB — NEVER trust client-provided prices
        $productIds = collect($validated['items'])->pluck('product_id')->unique()->all();
        $products = Product::whereIn('id', $productIds)
            ->where('is_active', true)
            ->get()
            ->keyBy('id');

        // Ensure all products exist and are active
        foreach ($validated['items'] as $item) {
            if (! $products->has($item['product_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'One or more products are no longer available.',
                ], 422);
            }
        }

        // Calculate totals using DB prices
        $subtotal = 0;
        $orderItems = [];

        foreach ($validated['items'] as $item) {
            $product = $products->get($item['product_id']);
            $price = (float) $product->new_price;
            $oldPrice = (float) $product->old_price;
            $qty = (int) $item['quantity'];
            $lineSubtotal = $price * $qty;
            $subtotal += $lineSubtotal;

            $orderItems[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_slug' => $product->slug,
                'product_image' => $product->image,
                'price' => $price,
                'old_price' => $oldPrice,
                'quantity' => $qty,
                'subtotal' => $lineSubtotal,
            ];
        }

        $deliveryCharge = $subtotal >= 2000 ? 0 : 80;
        $total = $subtotal + $deliveryCharge;

        $order = DB::transaction(function () use ($validated, $orderItems, $subtotal, $deliveryCharge, $total) {
            $order = Order::create([
                'user_id' => Auth::id(),
                'order_number' => Order::generateOrderNumber(),
                'full_name' => $validated['full_name'],
                'phone' => $validated['phone'],
                'email' => $validated['email'] ?? null,
                'address' => $validated['address'],
                'city' => $validated['city'],
                'district' => $validated['district'],
                'postcode' => $validated['postcode'] ?? null,
                'landmark' => $validated['landmark'] ?? null,
                'note' => $validated['note'] ?? null,
                'payment_method' => $validated['payment_method'] ?? 'cod',
                'subtotal' => $subtotal,
                'delivery_charge' => $deliveryCharge,
                'total' => $total,
                'status' => Order::STATUS_PENDING,
            ]);

            foreach ($orderItems as $item) {
                $order->items()->create($item);
            }

            return $order;
        });

        return response()->json([
            'success' => true,
            'order_number' => $order->order_number,
            'redirect' => route('orders.show', $order->order_number),
        ]);
    }

    /**
     * Show the order confirmation page.
     */
    public function show(Order $order): View
    {
        // If order belongs to a user, only that user can see it
        if ($order->user_id !== null && Auth::id() !== $order->user_id) {
            abort(403);
        }

        $order->load('items');

        return view('storefront.orders.show', compact('order'));
    }

    /**
     * Show the authenticated user's order history.
     */
    public function myOrders(): View
    {
        $orders = Order::forUser(Auth::id())
            ->with('items')
            ->latest()
            ->paginate(10);

        return view('storefront.orders.index', compact('orders'));
    }
}
