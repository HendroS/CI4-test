<?php
function check_access($role_id, $menu_id)
{

    $db = model('App\Models\UserAccessMenu');

    $result = $db->where('role_id', $role_id)
        ->where('menu_id', $menu_id)
        ->countAllResults();

    if ($result > 0) {

        return "checked  = 'checked'";
    }
}
