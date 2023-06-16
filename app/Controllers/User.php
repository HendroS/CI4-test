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
    public function edit()
    {
        $data['title'] = 'Edit Profile';
        $data['user'] = $this->user->where('email', session()->get('email'))->first();
        $data['menu'] = $this->menu;

        if (!$this->request->is('post')) {
            return view('user/edit', $data);
        }

        $rules = ['name' => 'required|trim|min_length[3]',];
        $upload_image = $this->request->getFile('image');

        //if image uploaded
        if ($upload_image->getName()) {
            $rules = [
                'name' => 'required|trim|min_length[3]',
                'image' => [
                    'rules' => 'uploaded[image]|max_size[image,1024]|is_image[image]|mime_in[image,image/jpg,image/jpeg,image/png]',
                    'errors' => [
                        'max_size' => 'image max is 1Mb',
                        'is_image' => 'your file is not image',
                        'mime_in' => 'only .jpg, .jpeg, .png allowed',
                    ]
                ]
            ];
        }
        if (!$this->validate($rules)) {
            return view('user/edit', $data);
        } else {
            $name = $this->request->getVar('name');
            $email = $data['user']['email'];
            $old_image = $data['user']['image'];
            $data_to_update['name'] =  $name;
        }
        //if image uploaded
        if ($upload_image->getName()) {
            //generate random name
            $image_name = $upload_image->getRandomName();
            $upload_image->move('assets/img/profile', $image_name);
            $data_to_update['image'] = $image_name;
        }
        //update user
        $this->user->set($data_to_update)
            ->where('email', $email)
            ->update();

        //unlink if image not default
        if ($upload_image->getName()) {
            if ($old_image != 'default.jpg') {
                unlink(FCPATH . "assets/img/profile/$old_image");
            }
        }

        session()->setFlashdata(
            'message',
            "<div class='alert alert-success' role='alert'>
                Profil updated.
                </div>"
        );
        return redirect()->to(base_url('user'));
    }

    public function changepassword()
    {
        $data['title'] = 'Change Password';
        $data['user'] = $this->user->where('email', session()->get('email'))->first();
        $data['menu'] = $this->menu;


        if (!$this->request->is('post')) {
            return view('user/change_password', $data);
        }

        $rules = [
            'current_password' => [
                'label' => 'Current Password',
                'rules' => 'required|trim|min_length[6]',
            ],
            'new_password' => [
                'label' => 'New Password',
                'rules' => 'required|trim|min_length[6]|matches[confirm_password]',
                'errors' => [
                    'matches' => 'new password not match'
                ]
            ],
            'confirm_password' => [
                'label' => 'Confirmation Password',
                'rules' => 'required|trim|min_length[6]|matches[new_password]',
                'errors' => [
                    'matches' => 'password not match'
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            return view('user/change_password', $data);
        } else {
            $current_password = $this->request->getVar('current_password');
            $new_password = $this->request->getVar('new_password');

            if (password_verify($current_password, $data['user']['password'])) {
                //pwd match
                if ($current_password == $new_password) {
                    //pwd old == new
                    session()->setFlashdata(
                        'message',
                        "<div class='alert alert-danger' role='alert'>
                        New password cannot be the same as current password
                        </div>"
                    );
                    return redirect()->to(base_url('user/changepassword'));
                } else {
                    //pwd ok
                    $hash_password = password_hash($new_password, PASSWORD_DEFAULT);

                    //update password
                    $this->user->set('password', $hash_password)
                        ->where('email', session()->get('email'))
                        ->update();

                    session()->setFlashdata(
                        'message',
                        "<div class='alert alert-success' role='alert'>
                         Password has been changed.
                        </div>"
                    );
                    return redirect()->to(base_url('user/changepassword'));
                }
            } else {

                session()->setFlashdata(
                    'message',
                    "<div class='alert alert-danger' role='alert'>
                Wrong current password.
                </div>"
                );
                return redirect()->to(base_url('user/changepassword'));
            }
        }
    }
}
