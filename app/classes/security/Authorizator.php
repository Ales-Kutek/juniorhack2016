<?php
    /**
     * Authorizator
     */
    namespace Security;

    /**
     * Authorizator class
     * zbytečná třída, ale je vyžuduje jí ověřování přes anotace..
     */
    class Authorizator extends \Nette\Object implements \Nette\Security\IAuthorizator {
        /**
         * všechny ověření oprávnění se posílají sem
         * @param string $role
         * @param string $resource
         * @param string $privilege
         * @return boolean
         */
        public function isAllowed($role, $resource, $privilege) {     
            return true;
        }
    }
