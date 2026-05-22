<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = ["cart_id", "product_id", "quantity"];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public static function clearExpiredCarts()
    {
        // 1. Ambil semua item yang expired (1 hari)
        $expiredItems = self::with('product')->where('updated_at', '<', now()->subDay())->get();

        foreach ($expiredItems as $item) {
            if ($item->product) {
                // 2. Kembalikan stok
                $item->product->increment('stock', $item->quantity);
            }
        }

        // 3. Hapus semua sekaligus
        self::where('updated_at', '<', now()->subDay())->delete();
    }
}
