<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserMenu extends Model {

    protected $fillable = [
        'parentMenu', 'menuName', 'menuLink', 'menuIcon', 'orderBy', 'menuStatus'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at',
    ];

    public function parent() {
        return $this->hasOne($this, 'id', 'parentMenu');
    }

    public function child() {
        $children = $this->hasMany($this, 'parentMenu', 'id')
                ->where('menuStatus', 1)
                ->orderBy('orderBy', 'ASC');
        return $children;
    }

    public function children() {
        return $this->child()->with('children');
    }

}
