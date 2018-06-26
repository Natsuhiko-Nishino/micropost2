<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

   
    protected $hidden = [
        'password', 'remember_token',
    ];
    public function microposts()
    {
        return $this->hasMany(Micropost::class);
    }
    
     public function followings()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps();
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'follow_id', 'user_id')->withTimestamps();
    }
    
    
    public function follow($userId)
{
    // confirm if already following
    $exist = $this->is_following($userId);
    // confirming that it is not you
    $its_me = $this->id == $userId;

    if ($exist || $its_me) {
        // do nothing if already following
        return false;
    } else {
        // follow if not following
        $this->followings()->attach($userId);
        return true;
    }
}

    public function unfollow($userId)
{
    // confirming if already following
    $exist = $this->is_following($userId);
    // confirming that it is not you
    $its_me = $this->id == $userId;


    if ($exist && !$its_me) {
        // stop following if following
        $this->followings()->detach($userId);
        return true;
    } else {
        // do nothing if not following
        return false;
    }
}

    public function is_following($userId) {
    return $this->followings()->where('follow_id', $userId)->exists();
    
    
}

    public function feed_microposts()
    {
        $follow_user_ids = $this->followings()-> pluck('users.id')->toArray();
        $follow_user_ids[] = $this->id;
        return Micropost::whereIn('user_id', $follow_user_ids);
    }
public function favoritepost()
    {
        return $this->belongsToMany(Micropost::class, 'favorites', 'user_id', 'microposts_id')->withTimestamps();
    }

public function pushfavorite($micropostId)
{
    // confirm if already pushed favorite
    $done = $this->is_pushing($micropostId);
   

    if ($done) {
        // do nothing if already pushed favorite
        return false;
    } else {
        // push fav if not pushed fav
        $this->favoritepost()->attach($micropostId);
        return true;
    }
}

public function drawfavorite($micropostId)
{
    $done = $this->is_pushing($micropostId);
    if ($done) {
        // stop faving if faving
        $this->favoritepost()->detach($micropostId);
        return true;
    } else {
        // do nothing if not faving
        return false;
    }
}

public function is_pushing($micropostId) {
    return $this->favoritepost()->where('microposts_id', $micropostId)->exists();
}

 public function feed_favoriteposts()
    {
        $favorite_micropost_ids = $this->favoritepost()-> pluck('microposts.id')->toArray();
        return Micropost::whereIn('user_id', $favorite_micropost_ids);
    }
}


