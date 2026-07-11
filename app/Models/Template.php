<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'user_id',
        'wish_message_id',
        'template_name'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function views()
    {
        return $this->hasMany(MessageViews::class, 'wish_message_id', 'wish_message_id');
    }

    public function wishMessage()
    {
        return $this->belongsTo(WishMessages::class, 'wish_message_id');
    }

}
