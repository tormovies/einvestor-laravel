<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'number', 'user_id', 'email', 'name', 'phone',
        'total', 'status', 'payment_status', 'payment_method',
        'payment_id', 'notes'
    ];

    protected $casts = [
        'total' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function downloads(): HasMany
    {
        return $this->hasMany(OrderDownload::class);
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (!$order->number) {
                $order->number = 'ORD-' . strtoupper(Str::random(8));
            }
        });

        static::created(function ($order) {
            // Создаем пользователя, если email не зарегистрирован
            if (!$order->user_id) {
                $password = Str::random(16);
                $user = \App\Models\User::firstOrCreate(
                    ['email' => $order->email],
                    [
                        'name' => $order->name ?? 'Пользователь',
                        'password' => bcrypt($password),
                    ]
                );
                $order->update(['user_id' => $user->id]);
                
                // Отправляем пароль на email
                try {
                    \Illuminate\Support\Facades\Mail::to($user->email)->send(
                        new \App\Mail\UserCreatedMail($user, $password)
                    );
                } catch (\Exception $e) {
                    // Логируем ошибку, но не прерываем выполнение
                    \Illuminate\Support\Facades\Log::error('Failed to send user created email', [
                        'email' => $user->email,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            // Создание записей OrderDownload теперь происходит в CheckoutController
            // после создания всех order items, чтобы избежать проблем с загрузкой отношений
        });
    }
}
