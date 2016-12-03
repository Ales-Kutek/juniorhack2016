<?php
/**
 * HeatSensorLog repository
 */

namespace Repository;

use Kdyby\Doctrine\EntityDao;

/**
 * HeatSensorLog repository class
 */
class HeatSensorLog extends EntityDao
{
    /**
     * získání všech hodnot
     * @param bool $getQuery
     * @return mixed
     */
    public function getAll($getQuery = FALSE) {
        $query = $this->createQueryBuilder()
                      ->select("u")
                      ->from('\Entity\HeatSensorLog', "u");
        
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