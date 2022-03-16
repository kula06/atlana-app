<?php

namespace App\Models\Github;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int id
 * @property string name
 * @property string full_name
 * @property int forks_count
 * @property int stargazers_count
 * @property GithubUser user
 */
class GithubUserRepository extends Model
{
    use HasFactory;

    const AVAILABLE_FIELDS = ['id', 'name', 'full_name', 'forks_count', 'stargazers_count'];

    protected $fillable = self::AVAILABLE_FIELDS;
    public $incrementing = false;

    public $timestamps = false;

    public function user(): HasOne
    {
        return $this->hasOne(GithubUser::class);
    }

}
