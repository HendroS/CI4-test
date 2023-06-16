<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Menu extends BaseController
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
        $data['title'] = 'Menu Management';
        $data['user'] = $this->user->where('email', session()->get('email'))->first();
        $data['menu'] = $this->menu;
        $rules = [
            'menu' => 'required|trim'
        ];
        if (!$this->request->is('post')) {
            return view('menu/index', $data);
        }

        if (!$this->validate($rules)) {
            return view('menu/index', $data);
        } else {
            //insert new menu
            $this->user_menu->insert(['menu' => $this->request->getVar('menu')]);
            session()->setFlashdata(
                'message',
                "<div class='alert alert-success' role='alert'>
                New user menu has been added.
                </div>"
            );
            return redirect()->to(base_url('menu'));
        }
    }
}
