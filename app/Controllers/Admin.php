<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Admin extends BaseController
{
    protected $user;
    protected $user_menu;
    protected $user_access_menu;
    protected $role_id;
    protected $menu;
    protected $user_role;

    public function __construct()
    {
        helper('form');
        $this->user = model('App\Models\User');
        $this->user_menu = model('App\Models\UserMenu');
        $this->user_access_menu = model('App\Models\UserAccessMenu');
        $this->user_role = model('App\Models\UserRole');
        $this->role_id = session('role_id');
        $this->menu = $this->user_menu->getUserMenuAllowed($this->role_id);
    }
    public function index()
    {
        $data['title'] = 'Admin Dashboard';
        $data['user'] = $this->user->where('email', session()->get('email'))->first();
        $data['menu'] = $this->menu;
        return view('admin/index', $data);
    }
    public function role()
    {
        $data['title'] = 'Role';
        $data['user'] = $this->user->where('email', session()->get('email'))->first();
        $data['role'] = $this->user_role->findAll();
        $data['menu'] = $this->menu;

        return view('admin/role', $data);
    }


    public function roleAccess($role_id)
    {
        helper(['my_helper']);
        $data['title'] = 'Role';
        $data['user'] = $this->user->where('email', session()->get('email'))->first();
        $data['role'] = $this->user_role->find($role_id);

        $data['menu'] = $this->user_menu->where('id !=', 1)->find();
        return view('admin/role-access', $data);
    }

    // called by ajax
    public function changeAccess()
    {
        $menuId = $this->request->getVar('menuId');
        $roleId = $this->request->getVar('roleId');
        $data = [
            'role_id' => $roleId,
            'menu_id' => $menuId,
        ];

        // $result = $this->user_access_menu->where($data)
        //     ->countAllResults();
        $db = db_connect()->table('user_access_menu');
        $result = $db->where($data)->get()->getNumRows();
        if ($result < 1) {
            $db->insert($data);
        } else {
            $db->delete($data);
        }
        session()->setFlashdata(
            'message',
            "<div class='alert alert-success' role='alert'>
                Access Changed.
                </div>"
        );
    }
}
