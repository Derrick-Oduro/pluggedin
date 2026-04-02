<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_super_admin',
        'points_balance',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function wishlistItems(): HasMany
    {
        return $this->hasMany(WishlistItem::class);
    }

    public function uploadedProducts(): HasMany
    {
        return $this->hasMany(Product::class, 'uploaded_by');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function referralLinks(): HasMany
    {
        return $this->hasMany(ReferralLink::class);
    }

    public function pointsTransactions(): HasMany
    {
        return $this->hasMany(PointsTransaction::class);
    }

    public function monthlyUploadLimit(): int
    {
        return (int) config('marketplace.upload_limit_per_month', 5);
    }

    public function monthlyProductUploadCount(?Carbon $now = null): int
    {
        $now = $now ?: now();

        return $this->uploadedProducts()
            ->whereBetween('created_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])
            ->count();
    }

    public function remainingMonthlyUploads(?Carbon $now = null): int
    {
        return max(0, $this->monthlyUploadLimit() - $this->monthlyProductUploadCount($now));
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(['admin', 'super-admin']);
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super-admin');
    }
}
