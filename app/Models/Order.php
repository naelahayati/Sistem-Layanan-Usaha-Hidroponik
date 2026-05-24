<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['user_id', 'total_produk', 'ongkir', 'grand_total', 'metode_pembayaran', 'metode_pengiriman', 'alamat', 'jarak', 'status', 'expires_at', 'midtrans_order_id', 'qr_url', 'is_offline'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
