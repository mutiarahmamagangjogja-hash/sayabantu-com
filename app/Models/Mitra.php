<?php

namespace App\Models;

use Database\Factories\MitraFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'mitra_id',
    'name',
    'nik',
    'phone',
    'email',
    'kota',
    'area',
    'alamat',
    'pin',
    'keahlian',
    'service',
    'jaminan',
    'avatar',
    'rating',
    'tasks',
    'status',
    'status_label',
    'rejection_reason',
    'can_reapply',
    'penalty_score',
])]
class Mitra extends Model
{
    /** @use HasFactory<MitraFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'rating' => 'float',
            'tasks' => 'integer',
            'can_reapply' => 'boolean',
            'penalty_score' => 'integer',
        ];
    }

    public function isApproved(): bool
    {
        return in_array($this->status, ['approved', 'aktif', 'disetujui'], true);
    }

    public function isPending(): bool
    {
        return in_array($this->status, ['pending_approval', 'verifikasi'], true);
    }

    public function isRejected(): bool
    {
        return in_array($this->status, ['rejected', 'ditolak', 'suspend'], true);
    }

    public function completedOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'mitra_id', 'mitra_id')
            ->where('status', 'selesai');
    }

    public function reviewedOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'mitra_id', 'mitra_id')
            ->whereNotNull('review_rating');
    }
}
