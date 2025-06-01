<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $primaryKey = 'orderID';
    protected $fillable = [
        'cusID',
        'adminID',
        'staID',
        'payID',
        'orderPhoneNumber',
        'shipping_street',
        'shipping_ward',
        'shipping_district',
        'shipping_city',
        'totalPrice'
    ];

    // Liên kết với user là khách hàng
    public function customer()
    {
        return $this->belongsTo(User::class, 'cusID','id');
    }

    // Liên kết với user là admin
    public function admin()
    {
        return $this->belongsTo(User::class, 'adminID','id');
    }

    // Liên kết với status
    public function status()
    {
        return $this->belongsTo(Status::class, 'staID', 'statusID');
    }

    // Các chi tiết đơn hàng
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'orderID', 'orderID');
    }
    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payID', 'paymentID');
    }
}
