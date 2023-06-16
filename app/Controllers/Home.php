<?php

namespace App\Controllers;

use App\Models\UserRole;

class Home extends BaseController
{
    public function index()
    {

        return view('welcome_message');
    }
}
