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
                $wasCreated = false;
                
                // Проверяем, существует ли пользователь
                $user = \App\Models\User::where('email', $order->email)->first();
                
                if (!$user) {
                    // Пользователя нет - создаем нового
                    $user = \App\Models\User::create([
                        'email' => $order->email,
                        'name' => $order->name ?? 'Пользователь',
                        'password' => bcrypt($password),
                    ]);
                    $wasCreated = true;
                } else {
                    // Пользователь существует - генерируем новый пароль и обновляем
                    $user->update([
                        'password' => bcrypt($password),
                    ]);
                }
                
                $order->update(['user_id' => $user->id]);
                
                // Сохраняем пароль в notes заказа для отправки после оплаты
                // (чтобы не отправлять пароль до оплаты)
                if (!$order->notes) {
                    $order->notes = '';
                }
                $order->update([
                    'notes' => $order->notes . "\n[PASSWORD_FOR_EMAIL:" . base64_encode($password) . "]"
                ]);
            }

            // Создание записей OrderDownload теперь происходит в CheckoutController
            // после создания всех order items, чтобы избежать проблем с загрузкой отношений
        });
        
        // Отправляем пароль при оплате заказа
        static::updating(function ($order) {
            // Если статус оплаты меняется на 'paid'
            if ($order->isDirty('payment_status') && $order->payment_status === 'paid') {
                // Проверяем, есть ли сохраненный пароль в notes
                if ($order->notes && preg_match('/\[PASSWORD_FOR_EMAIL:([^\]]+)\]/', $order->notes, $matches)) {
                    $password = base64_decode($matches[1]);
                    $user = $order->user;
                    
                    if ($user) {
                        try {
                            \Illuminate\Support\Facades\Mail::to($user->email)->send(
                                new \App\Mail\UserCreatedMail($user, $password)
                            );
                            
                            // Удаляем пароль из notes после отправки
                            $order->notes = preg_replace('/\[PASSWORD_FOR_EMAIL:[^\]]+\]\s*/', '', $order->notes);
                            $order->notes = trim($order->notes);
                            
                            \Illuminate\Support\Facades\Log::info('Password email sent after payment', [
                                'order_id' => $order->id,
                                'email' => $user->email,
                            ]);
                        } catch (\Exception $e) {
                            \Illuminate\Support\Facades\Log::error('Failed to send password email after payment', [
                                'order_id' => $order->id,
                                'email' => $user->email,
                                'error' => $e->getMessage(),
                            ]);
                        }
                    }
                }
            }
        });
    }
}
