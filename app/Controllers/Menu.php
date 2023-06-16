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
    protected $user_sub_menu;
    public function __construct()
    {
        helper('form');
        $this->user = model('App\Models\User');
        $this->user_menu = model('App\Models\UserMenu');
        $this->user_access_menu = model('App\Models\UserAccessMenu');
        $this->role_id = session('role_id');
        $this->menu = $this->user_menu->getUserMenuAllowed($this->role_id);
        $this->user_sub_menu = model('App\Models\UserSubMenu');
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

    public function subMenu()
    {
        // $menu = new ModelsMenu();
        $data['title'] = 'Submenu Management';
        $data['user'] = $this->user->where('email', session()->get('email'))->first();
        $data['menu'] = $this->menu;
        $data['subMenu'] = $this->user_menu->getAllSubMenu();
        if (!$this->request->is('post')) {
            return view('menu/sub_menu', $data);
        }
        $rules = [
            'title' => 'required|trim',
            'menu_id' => 'required|trim',
            'url' => 'required|trim',
            'icon' => 'required|trim',
        ];

        if (!$this->validate($rules)) {

            return view('menu/submenu', $data);
        } else {
            $subMenu = [
                'title' => $this->request->getVar('title'),
                'menu_id' => $this->request->getVar('menu_id'),
                'url' => $this->request->getVar('url'),
                'icon' => $this->request->getVar('icon'),
                'is_active' => $this->request->getVar('is_active'),
            ];


            $this->user_sub_menu->insert($subMenu);
            session()->setFlashdata(
                'message',
                "<div class='alert alert-success' role='alert'>
                New submenu has been added.
                </div>"
            );
            return redirect()->to(base_url('menu/submenu'));
        }
    }
}
