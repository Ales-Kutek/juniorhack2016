<?php
/**
 * Group repository
 */

namespace Repository;

use Kdyby\Doctrine\EntityDao;

/**
 * Group repository class
 */
class Group extends EntityDao
{
    /**
     * získání všech hodnot
     * @param bool $getQuery
     * @return mixed
     */
    public function getAll($getQuery = FALSE) {
        $query = $this->createQueryBuilder()
                      ->select("u")
                      ->from('\Entity\Group', "u");
        
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
}