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

        if (!$this->request->is('post')) {
            $data['title'] = 'User Login';
            return view('auth/login', $data);
        }

        $rules = [
            'email' => [
                'label' => 'Email',
                'rules' => 'required|trim|valid_email',
                'errors' => [
                    'valid_email' => 'Email is not valid'
                ]
            ],
            'password' => [
                'label'  => 'Password',
                'rules'  => 'required|trim|min_length[6]|',
                'errors' => []
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->to(base_url('auth'))->withInput();
        } else {
            return $this->_login();
        }
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
            $this->_sendEmail($token, 'verify');

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


    private function _login()
    {
        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');

        $user = $this->user->where('email', $email)->first();

        if ($user) {
            // email exist
            if ($user['is_active']) {
                //account active
                if (password_verify($password, $user['password'])) {
                    $data = [
                        'email' => $user['email'],
                        'role_id' => $user['role_id']
                    ];
                    session()->set($data);
                    //go to user page
                    return redirect()->to(base_url('user'));
                } else {
                    //wrong password
                    session()->setFlashdata(
                        'message',
                        "<div class='alert alert-danger' role='alert'>
                        Wrong password!
                        </div>"
                    );
                    return redirect()->to(base_url('auth'))->withInput();
                }
            } else {
                //not activated
                session()->setFlashdata(
                    'message',
                    "<div class='alert alert-danger' role='alert'>
                This email has not been activated!
                </div>"
                );
                return redirect()->to(base_url('auth'))->withInput();
            }
        } else {
            //email no exist
            session()->setFlashdata(
                'message',
                "<div class='alert alert-danger' role='alert'>
                This email is not registered!
                </div>"
            );
            return redirect()->to(base_url('auth'))->withInput();
        }
    }

    private function _sendEmail(String $token, String $type)
    {
        $email = \Config\Services::email();
        $to = $this->request->getVar('email');

        //message email link to verify 
        if ($type == "verify") {
            $subject = 'Account Verification';
            $message = 'Click this link to verify your account : <a href="' .
                base_url('auth/verify?email=') .
                $to . '&token=' . urlencode($token) . '">Activate</a>';
        }

        $email->setFrom('indradeveloptest@gmail.com', 'Admin App test');

        $email->setSubject($subject);
        $email->setTo($to);
        $email->setMessage($message);
        $res = $email->send();
        if ($res) {
            //....
        } else {
            $data = $email->printDebugger();
            print_r($data);
        }
    }

    public function verify()
    {
        $email = $this->request->getVar('email');
        $token = $this->request->getVar('token');

        $user = $this->user_token->where('email', $email)->first();

        if ($user) {
            //email found
            // $user_token = $db->table('user_token')
            // ->where('token', $token)->get()->getRowArray();
            $user_token = $this->user_token
                ->where('token', $token)->first();
            if ($user_token) {
                //valid token
                if (time() - $user_token['date_created'] < (60 * 60 * 24)) {
                    // less than 24 hour

                    $this->user->where('email', $email)->update(['is_active' => true]);
                    $this->user_token->where('email', $email)->delete();

                    $this->session->setFlashdata(
                        'message',
                        "<div class='alert alert-success' role='alert'>
                        $email has been activated. Please login!
                        </div>"
                    );
                    return redirect()->to(base_url('/auth'));
                } else {
                    //more than 24 

                    //delete token and account
                    $this->user->where('email', $email)->delete();
                    $this->user_token->where('email', $email)->delete();

                    session()->setFlashdata(
                        'message',
                        "<div class='alert alert-danger' role='alert'>
                        Your activation failed. Expired token!
                        </div>"
                    );
                    return redirect()->to(base_url('/auth'));
                }
            } else {
                //invalid token
                session()->setFlashdata(
                    'message',
                    "<div class='alert alert-danger' role='alert'>
                        Your activation failed. Invalid token!
                        </div>"
                );
                return redirect()->to(base_url('/auth'));
            }
        } else {
            // email not found on token table
            $this->session->setFlashdata(
                'message',
                "<div class='alert alert-danger' role='alert'>
                Your activation failed. Wrong email!
                </div>"
            );
            return redirect()->to(base_url('/auth'));
        }
    }
}
