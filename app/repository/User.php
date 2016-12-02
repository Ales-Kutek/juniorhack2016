<?php
/**
 * User repository
 */

namespace Repository;

use Kdyby\Doctrine\EntityDao;
use Doctrine\Common\Collections;

/**
 * User repository class
 */
class User extends EntityDao
{

    /**
     * Vrátí všechny uživatele
     * @return array
     */
    public function getAll($getQuery = false)
    {
        $query = $this->createQueryBuilder()
                      ->select("u")
                      ->from("\Entity\User", "u");
        
        if ($getQuery) {
            return $query;
        }

        $query = $query->getQuery();
        
        $result = $query->getResult();

        return $result;
    }

    /**
     * smaže uživatele pomocí id
     * @param int $id
     */
    public function remove($id)
    {
        $entity = $this->findOneBy(array("id" => (int) $id));
        
        $this->getEntityManager()->remove($entity);
        
        $this->flush();
    }
    
    public function filterValues($obj, $data) {
        foreach ($data as $key => $value) {
            if ($value != '' && $value !== NULL || $value === TRUE || $value === FALSE) {
                switch($key) {
                    case "group":
                        $dao = $this->getEntityManager();
                        $dao = $dao->getRepository(\Entity\Group::getClassName());
                        $group = $dao->findOneById($data["group"]);

                        $obj->{$key} = $group;
                    break;
                    
                    default:
                         $obj->{$key} = $value;
                    break;
                }
            }
        }
        
        return $obj;
    }

    /**
     * vloží nového uživatele, vrátí poslední ID
     * @param array $values
     * @return int
     */
    public function insert($values)
    {
        $item = new \Entity\User();

        $item = $this->filterValues($item, $values);
        
        $this->add($item);
        $this->flush();

        return $item->getId();
    }

    /**
     * update uživatele
     * @param mixed $values
     * @param int $id
     */
    public function update($values, int $id)
    {
        $dao = $this->getEntityManager()->getRepository(\Entity\User::getClassName());

        $item = $dao->findOneby(array("id" => $id));

        $item = $this->filterValues($item, $values);
        
        $this->add($item);
        $this->flush();

        return $item->getId();
    }

    /**
     * vrátí osobu pomocí id
     * hash = true -> vráti array objektů
     * hash = false -> vrátí vícerozměrné array
     * @param integer $id
     * @param boolean $hash
     * @return array
     */
    public function getSingle($id, $hash = false)
    {
        $query = $this->createQueryBuilder();

        $query
            ->select("u, g")
            ->from("\Entity\User", "u")
            ->leftJoin("u.group", "g")
            ->where("u.id = :id")
            ->setParameter("id", $id);

        $query = $query->getQuery();

        if ($hash === false) {
            $result = $query->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            
            $temp = $result;
            
            $result = $temp;
        } else if ($hash === true) {
            $result = $query->getSingleResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);
        }

        return $result;
    }

    /**
     * Vrátí uživatele na základě role
     * @param string $role
     * @param bool $hash
     * @return mixed
     */
    public function getByRole(int $role, bool $hash)
    {
        $query = $this->createQueryBuilder();

        $query
            ->select("partial p.{id,name,surname,username,phone,street,city,zip,ipaddress,created,email,group}")
            ->from("\Entity\User", "p")
            ->where("p.group = :group")
            ->setParameter("group", $role);

        $query = $query->getQuery()->setHint(\Doctrine\ORM\Query::HINT_FORCE_PARTIAL_LOAD,
            1);

        if ($hash === false) {
            $result = $query->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        } else if ($hash === true) {
            $result = $query->getResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);
        }

        return $result;
    }
}