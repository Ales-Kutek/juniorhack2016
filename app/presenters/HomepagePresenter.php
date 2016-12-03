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
        
        public function handleRedraw()
        {
            $this->redrawControl();
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
                
                $this->template->light = (int) file_get_contents(WWW_DIR . "/light.db");
                
                $this->template->heat_sensor = $heat_sensor;
                $this->template->humidity_sensor = $humidity_sensor;
                $this->template->rooms = $rooms;
	}
        
        public function handleLight() {
            $ch = curl_init();
            
            $url = "http://192.168.43.19/relayToggle";
            
            curl_setopt($ch, CURLOPT_URL, $url); 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
            $output = curl_exec($ch); 
            curl_close($ch);    
            $content = $output;
            
            echo $content;
            die();
        }
        
        public function handleGetArdu() {
            $ch = curl_init();
            
            $url = "http://192.168.43.19/";
            
            curl_setopt($ch, CURLOPT_URL, $url); 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
            $output = curl_exec($ch); 
            curl_close($ch);    
            $content = $output;
            
            $content = explode(";", $content);
            
            $heat = $content[0];
            $humidity = $content[1];
            $light = $content[2];
            
            $heatItem = $this->heatSensorRepository->findOneBy(array("id" => 1));
            $humidityItem = $this->humiditySensorRepository->findOneBy(array("id" => 1));
            
            $heatLogItem = new \Entity\HeatSensorLog();
            
            $heatLogItem->value = $heat;
            $heatLogItem->heat_sensor = $heatItem;
            
            $humidityLogItem = new \Entity\HumiditySensorLog();
            
            $humidityLogItem->value = $humidity;
            $humidityLogItem->humidity_sensor = $humidityItem;
            
            $this->heatSensorLogRepository->getEntityManager()->persist($humidityLogItem);
            $this->heatSensorLogRepository->getEntityManager()->persist($heatLogItem);
            $this->heatSensorLogRepository->getEntityManager()->flush();
            
            file_put_contents(WWW_DIR . "/light.db", $light);
            echo '<meta http-equiv="refresh" content="5">';
            die();
        }
}
