<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function __construct()
    {
    }

    public function generateQR($id)
    {
        $order = Order::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($order->status != 'Menunggu Pembayaran') {
            return redirect()->route('nazfram.riwayat-pesanan')
                ->with('success', 'Pembayaran sudah berhasil diproses!');
        }

        $qrUrl = \App\Models\Setting::get('qris_image', '');
        $payment_method_active = \App\Models\Setting::get('payment_method_active', 'qris');
        $bank_name = \App\Models\Setting::get('bank_name', '');
        $bank_account_number = \App\Models\Setting::get('bank_account_number', '');
        $bank_account_owner = \App\Models\Setting::get('bank_account_owner', '');

        return view('pembayaran', [
            'order' => $order,
            'qrUrl' => $qrUrl,
            'payment_method_active' => $payment_method_active,
            'bank_name' => $bank_name,
            'bank_account_number' => $bank_account_number,
            'bank_account_owner' => $bank_account_owner
        ]);
    }

    public function pembayaran($id)
    {
        return $this->generateQR($id);
    }

    public function konfirmasiPembayaran(Request $request, $id)
    {
        $order = Order::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $request->validate([
            'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240'
        ]);

        if ($request->hasFile('bukti_pembayaran')) {
            $path = $request->file('bukti_pembayaran')->store('bukti_pembayaran', 'cloudinary');
            $order->bukti_pembayaran = \Illuminate\Support\Facades\Storage::disk('cloudinary')->url($path);
        }

        $order->status = 'Menunggu Konfirmasi';
        $order->save();

        return response()->json(['success' => true, 'message' => 'Bukti pembayaran berhasil dikirim. Menunggu konfirmasi admin.']);
    }

    private function restoreToCart($order)
    {
        $userId = auth()->id();
        $cart = Cart::where("user_id", $userId)->first();
        if (!$cart) {
            $cart = Cart::create([
                "user_id" => $userId,
                "session_id" => session()->getId()
            ]);
        }

        $orderItems = OrderItem::where('order_id', $order->id)->get();

        foreach ($orderItems as $item) {
            $cartItem = CartItem::where('cart_id', $cart->id)
                ->where('product_id', $item->product_id)
                ->first();

            if ($cartItem) {
                $cartItem->quantity += $item->quantity;
                $cartItem->save();
            } else {
                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity
                ]);
            }
        }

        $order->status = 'Expired';
        $order->save();
    }
}