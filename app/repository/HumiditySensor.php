<?php
/**
 * HumiditySensor repository
 */

namespace Repository;

use Kdyby\Doctrine\EntityDao;

/**
 * HumiditySensor repository class
 */
class HumiditySensor extends EntityDao
{
    /**
     * získání všech hodnot
     * @param bool $getQuery
     * @return mixed
     */
    public function getAll($getQuery = FALSE) {
        $query = $this->createQueryBuilder()
                      ->select("u")
                      ->from('\Entity\HumiditySensor', "u");
        
        if ($getQuery) {
            return $query;
        }
        
        $query = $query->getQuery();
        
        $result = $query->getResult();
        
        return $result;
    }
    
    /**
     * získání všech primárních hodnot jako array
     * @return array
     */
    public function getAllAsTwoDimensionalArray() {
        $array = array();
        
        foreach ($this->getAll() as $key => $value) {
            $array[$value->id] = $value->name;
        }
        
        return $array;
    }
    
    public function insert($id, $heat) {
        $item = new \Entity\HumiditySensor();
        
        $item->value = $heat;
        $item->updated = new \DateTime();
        
        $item->element = $this->getEntityManager()->getRepository(\Entity\Element::getClassName())
                ->findOneBy(array("id" => $id));
        
        $this->add($item);
        
        $this->flush();
    }
}