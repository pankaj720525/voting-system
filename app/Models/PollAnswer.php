<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class PollAnswer extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = ['poll_id', 'poll_option_id', 'user_id'];

    /**
     * Get the phone associated with the user.
     */
    public function user_detail(): HasOne
    {
        return $this->hasOne(User::class,'id','user_id');
    }
}
