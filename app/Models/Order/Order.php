<?php

namespace App\Models\Order;

use App\Models\User\User;
use Carbon\Carbon;
use App\Models\OrderProduct\OrderProduct;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Adobrovolsky97\LaravelRepositoryServicePattern\Models\BaseModel;

/**
 * Class Order
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $surname
 * @property string $name
 * @property string $patronymic
 * @property string $phone
 * @property string $number
 * @property string $delivery_type
 * @property string $payment_type
 * @property string $comment
 * @property string $city_id
 * @property string $city_name
 * @property string $warehouse_id
 * @property string $warehouse_name
 * @property string $ttn
 * @property float $total_price_usd
 * @property float $total_price_uah
 * @property float $discount_percent
 * @property float $discount_usd
 * @property float $discount_uah
 * @property float $dollar_rate
 * @property boolean $is_crm_synced
 * @property boolean $is_paid
 * @property integer $crm_order_id
 * @property string $sync_error
 * @property string $crm_status
 * @property string $nova_poshta_status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property OrderProduct[]|Collection $products
 * @property User $user
 */
class Order extends BaseModel
{
    const PAYMENT_TYPE_CASH_ON_DELIVERY = 'cash_on_delivery';
    const PAYMENT_BY_REQUISITES = 'by_requisites';
    const PAYMENT_ONLINE = 'online';

    const DELIVERY_TYPE_SELF_PICKUP = 'self-pickup';
    const DELIVERY_TYPE_NEW_POST = 'new-post';

    const STATUS_FINISHED = 'Успішно';
    const STATUS_RESERVED = 'Бронь';
    const STATUS_FISCALIZED = 'Фіскалізація';
    const STATUS_PACKING = 'Комплектація';

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'surname',
        'name',
        'patronymic',
        'number',
        'phone',
        'delivery_type',
        'payment_type',
        'comment',
        'city_id',
        'city_name',
        'warehouse_id',
        'warehouse_name',
        'total_price_usd',
        'total_price_uah',
        'discount_percent',
        'discount_usd',
        'discount_uah',
        'dollar_rate',
        'is_crm_synced',
        'crm_order_id',
        'sync_error',
        'crm_status',
        'nova_poshta_status',
        'ttn',
        'is_paid',
        'created_at',
        'updated_at'
    ];

    /**
     * @var string[]
     */
    protected $casts = ['sync_error' => 'array'];

    /**
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        return "$this->surname $this->name $this->patronymic";
    }

    public function getDeliveryTypeTextAttribute(): string
    {
        return $this->delivery_type === self::DELIVERY_TYPE_SELF_PICKUP ? 'Самовивіз' : 'Нова Пошта';
    }

    public function getPaymentTypeTextAttribute(): string
    {
        return match ($this->payment_type) {
            self::PAYMENT_TYPE_CASH_ON_DELIVERY => 'Розрахунок на пошті при отриманні',
            self::PAYMENT_BY_REQUISITES => 'Оплата за реквізитами IBAN',
            self::PAYMENT_ONLINE => 'Посилання для онлайн оплати картою (+1,5% комісії)',
        };
    }

    public function getPaymentTypeTextForCrmAttribute(): string
    {
        return match ($this->payment_type) {
            self::PAYMENT_TYPE_CASH_ON_DELIVERY => 'Розрахунок на пошті при отриманні',
            self::PAYMENT_BY_REQUISITES => 'Оплата за реквізитами IBAN',
            self::PAYMENT_ONLINE => 'Посилання для онлайн оплати картою',
        };
    }

    /**
     * @return HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(OrderProduct::class, 'order_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
