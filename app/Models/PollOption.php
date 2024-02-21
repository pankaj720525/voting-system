<?php

namespace App\Models;

use App\Helpers\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PollOption extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['option', 'poll_id'];

    protected $appends = ['secret'];


    public function getSecretAttribute()
    {
        return Helper::getEncryptedSecret($this->id);
    }
}
