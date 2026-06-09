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
    public function unduhQR()
    {
        $qrUrl = \App\Models\Setting::get('qris_image');
        if (!$qrUrl) {
            abort(404);
        }

        try {
            // Context untuk mengabaikan isu SSL (sering terjadi di lokal/XAMPP) dan timeout
            $context = stream_context_create([
                "ssl" => [
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                ],
                "http" => [
                    "timeout" => 15, // 15 detik timeout
                ]
            ]);

            // Mengambil konten gambar dari URL (Cloudinary)
            $content = @file_get_contents($qrUrl, false, $context);
            
            if ($content === false) {
                return redirect()->back()->with('error', 'Gagal menyambung ke server penyimpanan gambar. Pastikan koneksi internet stabil.');
            }

            // Menentukan ekstensi file
            $extension = pathinfo(parse_url($qrUrl, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'png';
            $filename = 'QRIS-Nazfram.' . $extension;

            // Mendapatkan mime type yang sesuai
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->buffer($content) ?: 'image/png';

            return response($content)
                ->header('Content-Type', $mimeType)
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}