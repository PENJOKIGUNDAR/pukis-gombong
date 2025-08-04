<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_name',
        'description',
        'quantity',
        'unit',
        'unit_price',
        'reorder_point',
        'is_raw_material',
        'last_restock_date',
        'added_by',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'reorder_point' => 'decimal:2',
        'is_raw_material' => 'boolean',
        'last_restock_date' => 'date',
    ];

    /**
     * Get the user that added the inventory item.
     */
    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}
