<?php

namespace App\Models;

use App\Helpers\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Poll extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = ['question', 'close', 'user_id'];

    public function getSecretAttribute()
    {
        return Helper::getEncryptedSecret($this->id);
    }

    public function getAnswerArrayAttribute()
    {
        $poll_collect   = collect($this->poll_answer);
        $array = $poll_collect->pluck('poll_option_id','user_id')->toArray();
        $total_count = $poll_collect->count();

        $poll_option_count = [];
        $poll_percentage = [];
        foreach ($this->poll_options as $key => $value) {
            $poll_option = $poll_collect->where('poll_option_id',$value->id)->count('id');
            $poll_per = ($total_count > 0)? ($poll_option / $total_count) * 100 : 0;

            $poll_option_count[$value->id] = $poll_option;
            $poll_percentage[$value->id] = $poll_per;
        }
        $data = [
            'data_array'    => $array,
            'total_count'   => $total_count,
            'poll_option_count' => $poll_option_count,
            'poll_percentage' => $poll_percentage
        ];
        return $data;
    }

    /**
     * Get the options for the poll.
     */
    public function poll_options(): HasMany
    {
        return $this->hasMany(PollOption::class);
    }

    /**
     * Get the options for the poll.
     */
    public function poll_answer(): HasMany
    {
        return $this->hasMany(PollAnswer::class,'poll_id');
    }

}
