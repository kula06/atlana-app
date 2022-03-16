<?php

namespace App\Models\Github;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property int id
 * @property GithubUser user
 * @property Carbon created_at
 */
class GithubUserStatistics extends Model
{
    use HasFactory;

    protected $guarded = [];
    public $timestamps = false;

    public function user(): HasOne
    {
        return $this->hasOne(GithubUser::class);
    }

}
