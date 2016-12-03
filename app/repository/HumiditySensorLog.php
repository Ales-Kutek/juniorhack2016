<?php
/**
 * HumiditySensorLog repository
 */

namespace Repository;

use Kdyby\Doctrine\EntityDao;

/**
 * HumiditySensorLog repository class
 */
class HumiditySensorLog extends EntityDao
{
    /**
     * získání všech hodnot
     * @param bool $getQuery
     * @return mixed
     */
    public function getAll($getQuery = FALSE) {
        $query = $this->createQueryBuilder()
                      ->select("u")
                      ->from('\Entity\HumiditySensorLog', "u");
        
        if ($getQuery) {
            return $query;
        }
        
        $query = $query->getQuery();
        
        $result = $query->getResult();
        
        return $result;
    }
    
    public function getSingle($getQuery = FALSE, $id) {
        $query = $this->createQueryBuilder()
                      ->select("u")
                      ->from('\Entity\HumiditySensorLog', "u")
                        ->where("u.humidity_sensor = :hs")
                            ->setParameter("hs", $id)
                      ->orderBy("u.created", "DESC")
                        ->setMaxResults(1);
        
        $query = $query->getQuery();
        
        $result = $query->getResult();
        
        return $result;
    }
    
    public function getRecords($id, $firstResult, $max) {
                $query = $this->createQueryBuilder()
                      ->select("u")
                      ->from('\Entity\HumiditySensorLog', "u")
                        ->where("u.humidity_sensor = :hs")
                            ->setParameter("hs", $id)
                      ->orderBy("u.created", "ASC")
                        ->setFirstResult($firstResult)
                        ->setMaxResults($max);
        
        $query = $query->getQuery();
        
        $result = $query->getResult();
        
        return $result;
    }
    
    public function countRecords($id) {
                $query = $this->createQueryBuilder()
                      ->select("count(u)")
                      ->from('\Entity\HumiditySensorLog', "u")
                        ->where("u.humidity_sensor = :hs")
                            ->setParameter("hs", $id);
        
        $query = $query->getQuery();
        
        $result = $query->getSingleScalarResult();
        
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
    
    public function insert($id, $value) {
        $item = new \Entity\HeatSensorLog();
        
        $item->updated = new \DateTime();
        $item->value = $value;
        
        $item->heat_sensor = $this->getEntityManager()->getRepository(\Entity\HeatSensor::getClassName())
                ->findOneBy(array("id" => $id));
        
        $this->add($item);
        
        $this->flush();
    }
}