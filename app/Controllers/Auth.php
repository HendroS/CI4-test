<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Auth extends BaseController
{
    public function index()
    {
        $data['title'] = 'LOGIN';
        return view('auth/login', $data);
    }
    public function register()
    {
        $data['title'] = 'REGISTRATION';
        return view('auth/registration', $data);
    }
    public function forgotPassword()
    {
        $data['title'] = 'FORGOT PASSWORD';
        return view('auth/forgot_password', $data);
    }
}
