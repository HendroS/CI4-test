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
        if (session('email')) {
            return redirect()->to(base_url('user'));
        }
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
        if (session('email')) {
            return redirect()->to(base_url('user'));
        }
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
        if (!$this->request->is('post')) {

            return view('auth/forgot_password', $data);
        }

        $rules = [
            'email' => [
                'label' => 'Email',
                'rules' => 'required|trim|valid_email',
                'errors' => [
                    'valid_email' => 'Email is not valid'
                ]
            ],
        ];
        if (!$this->validate($rules)) {
            return view('auth/forgot_password', $data);
        } else {

            $email = $this->request->getVar('email');
            $user = $this->user->where(['email' => $email, 'is_active' => true])->first();

            if ($user) {
                //user active

                //create token email and save to db
                $token = base64_encode(random_bytes(32));
                $user_token = [
                    'email' => $email,
                    'token' => $token,
                    'date_created' => time(),
                ];
                $this->user_token->insert($user_token);

                //send token resset
                $this->_sendEmail($token, 'reset');

                session()->setFlashdata(
                    'message',
                    "<div class='alert alert-success' role='alert'>
                Password reset link has been sent to $email! Please check the email.
                </div>"
                );
                return redirect()->to(base_url('auth/forgotpassword'));
            } else {
                //user not found or not active
                session()->setFlashdata(
                    'message',
                    "<div class='alert alert-danger' role='alert'>
                $email is not registered or activated!
                </div>"
                );
                return redirect()->to(base_url('auth/forgotpassword'));
            }
        }
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

        if ($type == "verify") {
            //message email link to verify 
            $subject = 'Account Verification';
            $message = 'Click this link to verify your account : <a href="' .
                base_url('auth/verify?email=') .
                $to . '&token=' . urlencode($token) . '">Activate</a>';
        } else if ($type == "reset") {
            //message email link to reset 
            $subject = 'Password Reset';
            $message = 'Click this link to reset your password : <a href="' .
                base_url('auth/resetpassword?email=') .
                $to . '&token=' . urlencode($token) . '">Reset Password</a>';
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

                    $this->user->set('is_active', true)->where('email', $email)->update();
                    $this->user_token->where('email', $email)->delete();

                    session()->setFlashdata(
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
            session()->setFlashdata(
                'message',
                "<div class='alert alert-danger' role='alert'>
                Your activation failed. Wrong email!
                </div>"
            );
            return redirect()->to(base_url('/auth'));
        }
    }

    public function resetpassword()
    {
        $email = $this->request->getVar('email');
        $token = $this->request->getVar('token');

        $user = $this->user->where('email', $email)->first();

        if ($user) {
            //email in db
            $user_token = $this->user_token->where('token', $token)->first();
            if ($user_token) {
                //token valid

                //session to prevent back after reset
                session()->set('reset_email', $email);
                return $this->changePassword();
            } else {
                //invalid token
                session()->setFlashdata(
                    'message',
                    "<div class='alert alert-danger' role='alert'>
                        Reset password failed. Token invalid!
                        </div>"
                );
                return redirect()->to(base_url('auth'));
            }
        } else {
            //no email in db
            session()->setFlashdata(
                'message',
                "<div class='alert alert-danger' role='alert'>
                        Reset password failed. Wrong email!
                        </div>"
            );
            return redirect()->to(base_url('auth'));
        }
    }

    public function changePassword()
    {
        if (!session()->has('reset_email')) {
            return redirect()->to(base_url('auth'));
        }

        $data['title'] = 'Change Password';
        if (!$this->request->is('post')) {
            return view('auth/change_password', $data);
        }
        $rules = [
            'password' => [
                'label' => 'Password',
                'rules' => 'required|trim|min_length[6]|matches[confirm_password]',
                'errors' => [
                    'matches' => 'password not match'
                ]
            ],
            'confirm_password' => [
                'label' => 'Confirmation Password',
                'rules' => 'required|trim|min_length[6]|matches[password]',
                'errors' => [
                    'matches' => 'password not match'
                ],
            ]
        ];

        if (!$this->validate($rules)) {
            return view('auth/change_password', $data);
        } else {
            $hash_password = password_hash($this->request->getVar('password'), PASSWORD_DEFAULT);
            $email = session()->get('reset_email');

            //update password
            $this->user->set('password', $hash_password)->where('email', $email)->update();
            $this->user_token->where('email', $email)->delete();

            //remove session to prevent back to this url
            session()->remove('reset_email');
            session()->setFlashdata(
                'message',
                "<div class='alert alert-success' role='alert'>
                        Reset password success. Please login!
                        </div>"
            );
            return redirect()->to(base_url('auth'));
        }
    }

    function blocked()
    {
        $data['title'] = 'Access Blocked';
        return view('auth/blocked', $data);
    }

    function logout()
    {
        session()->remove('email');
        session()->remove('role_id');
        session()->setFlashdata(
            'message',
            "<div class='alert alert-success' role='alert'>
                You have been log out.
                </div>"
        );
        return redirect()->to(base_url('auth'));
    }
}
