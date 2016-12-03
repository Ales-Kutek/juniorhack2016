<?php

namespace App\Presenters;

use Nette;


class HomepagePresenter extends SecurePresenter
{
        /** @var \Repository\Element $elementRepository */
        private $elementRepository;
        
        /** @var \Repository\HeatSensor $heatSensorRepository */
        private $heatSensorRepository;
        
        /** @var \Repository\HeatSensorLog $heatSensorLogRepository */
        private $heatSensorLogRepository;
        
        /** @var \Repository\HumiditySensor $humiditySensorRepository */
        private $humiditySensorRepository;
        
        /** @var \Repository\HumiditySensorLog @inject */
        public $humiditySensorLogRepository;
        
        public function __construct(\Repository\Element $elementRepository,
                                    \Repository\HeatSensor $heatSensorRepository,
                                    \Repository\HumiditySensor $humiditySensorRepository,
                                    \Repository\HeatSensorLog $heatSensorLogRepository) {
            
            $this->elementRepository = $elementRepository;
            $this->heatSensorRepository = $heatSensorRepository;
            $this->humiditySensorRepository = $humiditySensorRepository;
            $this->heatSensorLogRepository = $heatSensorLogRepository;
        }
        
        public function handleGetChart($id) {
            $element = $this->elementRepository->getSingle($id, TRUE);
            
            $convertor = new \Utils\DateConvertor();
            
            $sensors = array();
            
            foreach ($element->heat_sensor as $key => $value) {
                $count = $this->heatSensorLogRepository->countRecords($value->id);
                
                $max = 100;
                
                $log = $this->heatSensorLogRepository->getRecords($value->id, $count - $max, $max);
                
                $data = array();
                $category = array();
                
                foreach ($log as $k => $v) {
                    $data[] = $v->value;
                    $category[] = $v->created->getTimestamp();
                }
                
                $sensors[] = array(
                    "name" => $value->name,
                    "data" => $data,
                    "category" => $category
                );
            }
            
            foreach ($element->humidity_sensor as $key => $value) {
                $countHum = $this->humiditySensorLogRepository->countRecords($value->id);
                
                $max = 100;
                
                $logHum = $this->humiditySensorLogRepository->getRecords($value->id, $countHum - $max, $max);
                
                $data = array();
                $category = array();
                
                foreach ($logHum as $k => $v) {
                    $data[] = $v->value;
                    $category[] = $v->created->getTimestamp();
                }
                
                $sensors[] = array(
                    "name" => $value->name,
                    "data" => $data,
                    "category" => $category
                );
            }
            
            echo json_encode($sensors);
            
            die();
        }

	public function renderDefault()
	{
		$rooms = $this->elementRepository->getAll(FALSE, $this->user->identity->id);
                
                $heat_sensor = array();
                $humidity_sensor = array();
                
                foreach ($rooms as $k => $v) {
                    foreach ($v->heat_sensor as $key => $value) {
                        $heat_sensor[$value->id] = $this->heatSensorLogRepository->getSingle(FALSE, $value->id);
                    }
                    foreach ($v->humidity_sensor as $key => $value) {
                        $humidity_sensor[$value->id] = $this->humiditySensorLogRepository->getSingle(FALSE, $value->id);
                    }
                }
                
                $this->template->heat_sensor = $heat_sensor;
                $this->template->humidity_sensor = $humidity_sensor;
                $this->template->rooms = $rooms;
	}
}
