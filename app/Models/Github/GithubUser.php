<?php

namespace App\Models\Github;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int id
 * @property string login
 * @property string avatar_url
 * @property string email
 * @property string location
 * @property string bio
 * @property Carbon created_at
 * @property int followers
 * @property int following
 * @property Collection|GithubUserRepository[] repositories
 * @property Collection|GithubUserStatistics[] statistics
 */
class GithubUser extends Model
{
    use HasFactory;

    const AVAILABLE_FIELDS = ['id', 'login', 'avatar_url', 'email', 'location', 'bio', 'created_at', 'followers', 'following'];

    protected $fillable = self::AVAILABLE_FIELDS;
    public $timestamps = false;
    public $incrementing = false;

    protected $withCount = ['repositories', 'statistics'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:00'
    ];

    public function repositories(): HasMany
    {
        return $this->hasMany(GithubUserRepository::class);
    }

    public function statistics(): HasMany
    {
        return $this->hasMany(GithubUserStatistics::class);
    }

}
