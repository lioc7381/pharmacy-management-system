<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Represents a medication in the pharmacy's inventory.
 *
 * This model handles the core details of a medication product, including
 * its name, strength, description, price, and stock levels. It also
 * manages relationships with orders and provides scopes for common queries.
 */
class Medication extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'strength_form',
        'description',
        'price',
        'current_quantity',
        'minimum_threshold',
        'category',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'current_quantity' => 'integer',
        'minimum_threshold' => 'integer',
    ];

    // Relationships

    /**
     * Get the order items for this medication.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the orders that include this medication.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_items')
                    ->withPivot('quantity', 'unit_price')
                    ->withTimestamps();
    }

    // Scopes

    /**
     * Scope a query to only include active medications.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include medications with low stock.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLowStock($query)
    {
        return $query->whereColumn('current_quantity', '<=', 'minimum_threshold');
    }
}
