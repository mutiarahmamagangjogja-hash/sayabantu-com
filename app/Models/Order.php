<?php

namespace App\Models;

use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    protected $fillable = [
        'code',
        'user_id',
        'customer_name',
        'customer_phone',
        'layanan',
        'jadwal',
        'durasi_cari',
        'durasi_menit',
        'alamat',
        'kota',
        'note',
        'biaya',
        'nominal',
        'metode',
        'gender',
        'status',
        'cancellation_reason',
        'cancelled_by',
        'escrow_status',
        'escrow_hold_reason',
        'escrow_released_at',
        'payment_method',
        'payment_proof',
        'payment_submitted_at',
        'payment_verified_at',
        'admin_fee',
        'mitra_fee',
        'refund_amount',
        'compensation_amount',
        'step',
        'mitra_id',
        'mitra_name',
        'mitra_phone',
        'nego_by',
        'agreed_by',
        'agreed_price',
        'review_rating',
        'review_comment',
        'reviewed_at',
    ];

    protected $casts = [
        'nominal' => 'integer',
        'durasi_menit' => 'integer',
        'admin_fee' => 'integer',
        'mitra_fee' => 'integer',
        'step' => 'integer',
        'refund_amount' => 'integer',
        'compensation_amount' => 'integer',
        'review_rating' => 'integer',
        'reviewed_at' => 'datetime',
        'escrow_released_at' => 'datetime',
        'payment_submitted_at' => 'datetime',
        'payment_verified_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
