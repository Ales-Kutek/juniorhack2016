<?php
/**
 * Element repository
 */

namespace Repository;

use Kdyby\Doctrine\EntityDao;
use Doctrine\Common\Collections;

/**
 * Element repository class
 */
class Element extends EntityDao
{

    /**
     * Vrátí všechny uživatele
     * @return array
     */
    public function getAll($getQuery = false, $id)
    {
        $query = $this->createQueryBuilder()
                      ->select("u, hs, hms")
                      ->from('\Entity\Element', "u")
                        ->leftJoin("u.heat_sensor", "hs")
                        ->leftJoin("u.humidity_sensor", "hms")
                      ->where("u.user = :user")
                        ->setParameter("user", $id);
        
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
    public function insert($values, int $id)
    {
        $item = new \Entity\Element();

        $item = $this->filterValues($item, $values);
        
        $item->user = $this->getEntityManager()->getRepository(\Entity\User::getClassName())->findOneBy(array("id" => $id));
        
        $this->add($item);
        $this->flush();

        return $item;
    }

    /**
     * update uživatele
     * @param mixed $values
     * @param int $id
     */
    public function update($values, int $id)
    {
        $dao = $this->getEntityManager()->getRepository(\Entity\Element::getClassName());

        $item = $dao->findOneby(array("id" => $id));

        $item = $this->filterValues($item, $values);
        
        $this->add($item);
        $this->flush();

        return $item;
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
            ->select("u, hs, hms")
            ->from("\Entity\Element", "u")
                ->leftJoin("u.heat_sensor", "hs")
                ->leftJoin("u.humidity_sensor", "hms")
            ->where("u.id = :id")
            ->setParameter("id", $id);

        $query = $query->getQuery();

        if ($hash === false) {
            $result = $query->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

            $result = $temp;
        } else if ($hash === true) {
            $result = $query->getSingleResult(\Doctrine\ORM\Query::HYDRATE_OBJECT);


        return $result;
    }
    }
}