<?php
    /**
     * Role file
     */
    namespace Security;
    
    /**
     * Role class
     */
    class Role extends \IPub\Permissions\Entities\Role implements \IPub\Permissions\Models\IRolesModel {
        /**
         * Definice rolí
         * @return array
         */
        public function findAll() {
            $obchodnik = new \IPub\Permissions\Entities\Role();
            $obchodnik->setKeyName("Obchodnik");
            $obchodnik->addPermission("Perm");

            $operator = new \IPub\Permissions\Entities\Role();
            $operator->setKeyName("Operator");
            $operator->setChildren($obchodnik);
            $operator->addPermission("Perm");

            $admin = new \IPub\Permissions\Entities\Role();
            $admin->setKeyName("Admin");
            $admin->setChildren($operator);
            $admin->isAdministrator();
            $admin->addPermission("Perm");

            $roles = array($admin, $operator, $obchodnik);

            return $roles;
        }
}
