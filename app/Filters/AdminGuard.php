<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AdminGuard implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // if (session()->get('role_id') != 1) {
        //     return redirect()
        //         ->to(base_url('user'));
        // }

        $role_id = session()->get('role_id');
        $uri = service('uri');
        $db = \Config\Database::connect();
        $menu = $uri->getSegment(1);
        $queryMenu = $db->table('user_menu')->getWhere(['menu' => $menu])->getFirstRow('array');
        $menu_id = $queryMenu['id'];

        $userAccess = $db->table('user_access_menu')->getWhere(['role_id' => $role_id, 'menu_id' => $menu_id])->getNumRows();
        if ($userAccess < 1) {
            return redirect()->to(base_url('auth/blocked'));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
