<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class User extends BaseController
{

    protected $user;
    protected $user_menu;
    protected $user_access_menu;
    protected $role_id;
    protected $menu;

    public function __construct()
    {
        helper('form');
        $this->user = model('App\Models\User');
        $this->user_menu = model('App\Models\UserMenu');
        $this->user_access_menu = model('App\Models\UserAccessMenu');
        $this->role_id = session('role_id');
        $this->menu = $this->user_menu->getUserMenuAllowed($this->role_id);
    }
    public function index()
    {
        $data['title'] = 'My Profile';
        $data['user'] = $this->user->where('email', session()->get('email'))->first();
        $data['menu'] = $this->menu;
        return view('user/index', $data);
    }
}
