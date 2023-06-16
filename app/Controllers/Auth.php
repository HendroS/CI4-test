<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Auth extends BaseController
{
    protected $user;
    protected $user_token;

    public function __construct()
    {
        helper('form');
        $this->user = model('App\Models\User');
        $this->user_token = model('App\Models\UserToken');
    }
    public function index()
    {
        $data['title'] = 'LOGIN';
        return view('auth/login', $data);
    }
    public function register()
    {
        helper('form');
        $data['title'] = 'User Registration';

        if (!$this->request->is('post')) {
            return view('auth/registration', $data);
        }

        $rules = [
            'name' => 'required|trim|min_length[3]',
            'email' => [
                'label' => 'Email',
                'rules' => 'required|trim|valid_email|is_unique[user.email]',
                'errors' => [
                    'is_unique' => 'This email has already registered',
                ]
            ],
            'password' => [
                'label' => 'Password',
                'rules' => 'required|trim|min_length[6]|matches[conf_password]',
                'errors' => [
                    'matches' => 'password not match'
                ]
            ],
            'conf_password' => [
                'label' => 'Confirmation Password',
                'rules' => 'required|trim|min_length[6]|matches[password]',
                'errors' => [
                    'matches' => 'password not match'
                ],
            ]
        ];

        if (!$this->validate($rules)) {
            // return redirect()->to('auth/registration')->withInput();
            return redirect()->to(base_url('auth/register'))->withInput();
        } else {
            //passed validation
            // create data user
            $email = $this->request->getVar('email');
            $user = [
                'name' => htmlspecialchars($this->request->getVar('name'), true),
                'email' => htmlspecialchars($email, true),
                'image' => 'default.jpg',
                'password' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT),
                'role_id' => 2,
                'is_active' => false,
                'date_created' => time(),
            ];

            //create data token
            $token = base64_encode(random_bytes(32));
            $user_token = [
                'email' => $email,
                'token' => $token,
                'date_created' => time()
            ];

            // insert user and token to db
            $this->user->insert($user);
            $this->user_token->insert($user_token);

            //send mail activation token
            //......

            session()->setFlashdata(
                'message',
                "<div class='alert alert-success' role='alert'>
                        Congrotulation! Your account has been created. We have sent activation link to $email .
                         verify your account.
                        </div>"
            );

            return redirect()->to('auth');
        }



        // return view('auth/registration', $data);
    }
    public function forgotPassword()
    {
        $data['title'] = 'FORGOT PASSWORD';

        return view('auth/forgot_password', $data);
    }
}
