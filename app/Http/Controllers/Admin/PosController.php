<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Inventory;
use App\Models\PosSale;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function index(Request $request)
    {
        $salon = $request->attributes->get('salon');
        $salons = [];
        $ownerId = null;

        if ($salon) {
            $ownerId = $salon->id;
        } else {
            if (Auth::user()->isAdmin()) {
                $salons = User::where('role', 'user')->orderBy('salon_name')->get();
                if ($request->filled('salon_id')) {
                    $ownerId = $request->get('salon_id');
                }
            } else {
                $ownerId = Auth::user()->created_by ?: Auth::id();
            }
        }

        $services = collect();
        $inventories = collect();

        if ($ownerId) {
            $services = Service::where('status', true)->where('user_id', $ownerId)->orderBy('name')->get();
            $inventories = Inventory::where('status', true)->where('user_id', $ownerId)->orderBy('item_name')->get();
        } elseif (Auth::user()->isAdmin() && count($salons) > 0) {
            // If no salon is selected yet by global admin, default to the first salon's items so it doesn't look blank
            $ownerId = $salons->first()->id;
            $services = Service::where('status', true)->where('user_id', $ownerId)->orderBy('name')->get();
            $inventories = Inventory::where('status', true)->where('user_id', $ownerId)->orderBy('item_name')->get();
        }

        return view('admin.pos.index', compact('services', 'inventories', 'salons', 'ownerId', 'salon'));
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'cart_items' => 'required|array',
            'cart_items.*.id' => 'required|integer',
            'cart_items.*.type' => 'required|in:service,product',
            'cart_items.*.quantity' => 'required|integer|min:1',
            'discount' => 'nullable|numeric|min:0',
            'payment_method' => 'required|string|in:Cash,Card,UPI,Pay Later',
        ]);

        $salon = $request->attributes->get('salon');
        $ownerId = null;

        if ($salon) {
            $ownerId = $salon->id;
        } else {
            if (Auth::user()->isAdmin()) {
                $ownerId = $request->get('salon_id');
                if (!$ownerId) {
                    $firstSalon = User::where('role', 'user')->first();
                    $ownerId = $firstSalon ? $firstSalon->id : Auth::id();
                }
            } else {
                $ownerId = Auth::user()->created_by ?: Auth::id();
            }
        }

        $cartItems = $request->input('cart_items', []);
        $processedItems = [];
        $subtotal = 0;
        $tax = 0;

        DB::beginTransaction();

        try {
            foreach ($cartItems as $item) {
                if ($item['type'] === 'service') {
                    $service = Service::findOrFail($item['id']);
                    $price = floatval($service->price);
                    $quantity = intval($item['quantity']);
                    $itemSubtotal = $price * $quantity;
                    $itemTax = $itemSubtotal * 0.03; // 3% service fee

                    $subtotal += $itemSubtotal;
                    $tax += $itemTax;

                    $processedItems[] = [
                        'id' => $service->id,
                        'name' => $service->name,
                        'type' => 'service',
                        'price' => $price,
                        'quantity' => $quantity,
                        'subtotal' => $itemSubtotal,
                        'tax' => $itemTax
                    ];
                } else {
                    $product = Inventory::findOrFail($item['id']);
                    $price = floatval($product->price ?: $product->mrp);
                    $quantity = intval($item['quantity']);

                    // Verify stock availability
                    if ($product->manage_stock && $product->quantity < $quantity) {
                        return response()->json([
                            'success' => false,
                            'message' => "Insufficient stock for product: {$product->item_name}. Only {$product->quantity} left."
                        ], 422);
                    }

                    // Deduct inventory
                    if ($product->manage_stock) {
                        $product->decrement('quantity', $quantity);
                        if ($product->quantity <= 0) {
                            $product->update(['stock_status' => false]);
                        }
                    }

                    $itemSubtotal = $price * $quantity;
                    // Apply product GST percent if defined, else fallback to 3%
                    $gstPercent = floatval($product->gst_percent ?: 3.0);
                    $itemTax = $itemSubtotal * ($gstPercent / 100);

                    $subtotal += $itemSubtotal;
                    $tax += $itemTax;

                    $processedItems[] = [
                        'id' => $product->id,
                        'name' => $product->item_name,
                        'type' => 'product',
                        'price' => $price,
                        'quantity' => $quantity,
                        'subtotal' => $itemSubtotal,
                        'tax' => $itemTax
                    ];
                }
            }

            $discount = floatval($request->input('discount', 0));
            $total = ($subtotal + $tax) - $discount;
            if ($total < 0) {
                $total = 0;
            }

            $invoiceNumber = 'INV-POS-' . date('YmdHis') . rand(10, 99);

            $posSale = PosSale::create([
                'user_id' => $ownerId,
                'invoice_number' => $invoiceNumber,
                'customer_name' => $request->input('customer_name'),
                'customer_phone' => $request->input('customer_phone'),
                'customer_email' => $request->input('customer_email'),
                'items' => $processedItems,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'total' => $total,
                'payment_method' => $request->input('payment_method'),
                'payment_status' => $request->input('payment_method') === 'Pay Later' ? 'Pending' : 'Paid',
            ]);

            DB::commit();

            $invoiceUrl = $salon 
                ? route('admin.pos.invoice', ['salon' => $salon->slug, 'id' => $posSale->id])
                : route('admin.pos.invoice', ['id' => $posSale->id]);

            return response()->json([
                'success' => true,
                'message' => 'Checkout processed successfully.',
                'invoice_url' => $invoiceUrl,
                'sale_id' => $posSale->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing checkout: ' . $e->getMessage()
            ], 500);
        }
    }

    public function history(Request $request)
    {
        $salon = $request->attributes->get('salon');
        $ownerId = null;

        if ($salon) {
            $ownerId = $salon->id;
        } else {
            if (Auth::user()->isAdmin()) {
                // Admin can see everything or filter by salon
                $query = PosSale::query();
                if ($request->filled('salon_id')) {
                    $query->where('user_id', $request->get('salon_id'));
                }
            } else {
                $ownerId = Auth::user()->created_by ?: Auth::id();
            }
        }

        $query = PosSale::with('user');

        if ($ownerId) {
            $query->where('user_id', $ownerId);
        }

        $sales = $query->orderBy('created_at', 'desc')->paginate(15);
        $salons = Auth::user()->isAdmin() ? User::where('role', 'user')->orderBy('salon_name')->get() : [];

        return view('admin.pos.history', compact('sales', 'salons', 'salon'));
    }

    public function showInvoice(Request $request, $id)
    {
        $salon = $request->attributes->get('salon');
        $posSale = PosSale::findOrFail($id);

        if ($salon && $posSale->user_id !== $salon->id) {
            abort(403, 'Unauthorized access to this sale.');
        }

        if (!Auth::user()->isAdmin() && !$salon && $posSale->user_id !== (Auth::user()->created_by ?: Auth::id())) {
            abort(403, 'Unauthorized access to this sale.');
        }

        $salonUser = User::find($posSale->user_id);

        return view('admin.pos.invoice', compact('posSale', 'salonUser', 'salon'));
    }
}
