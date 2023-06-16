<?php

namespace App\Models;

use CodeIgniter\Model;

class UserMenu extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = "user_menu";
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['menu'];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function getUserMenuAllowed(int $role_id)
    {
        $queryMenu = "SELECT `user_menu`.`id`, `menu` FROM `user_menu`
                        JOIN `user_access_menu` ON `user_menu`.`id` = `user_access_menu`.`menu_id`
                        WHERE `user_access_menu`.`role_id`= $role_id
                        ORDER BY `user_access_menu`.`menu_id` ASC";
        return $this->db->query($queryMenu)->getResultArray();
    }

    public function getSubMenu(int $menu_id)
    {
        $querySubMenu = "SELECT * FROM `user_sub_menu`
                        JOIN `user_menu` ON `user_menu`.`id` = `user_sub_menu`.`menu_id`
                        WHERE `user_sub_menu`.`menu_id`= $menu_id
                        ORDER BY `user_sub_menu`.`menu_id` ASC";
        return $this->db->query($querySubMenu)->getResultArray();
    }
}
