<?php

namespace App\Presenters;

use Nette;


class SendPresenter extends SecurePresenter
{
        /** @var \Repository\HeatSensor $heatSensorRepository */
        private $heatSensorRepository;
        
        /** @var \Repository\HumiditySensor $humiditySensorRepository */
        private $humiditySensorRepository;
        
        /** @var \Repository\Element $elementRepository */
        private $elementRepository;
        
        public function __construct(\Repository\Element $elementRepository,
                                    \Repository\HeatSensor $heatSensorRepository,
                                    \Repository\HumiditySensor $humiditySensorRepository) {
            $this->elementRepository = $elementRepository;
            $this->humiditySensorRepository = $humiditySensorRepository;
            $this->heatSensorRepository = $heatSensorRepository;
        }

	public function renderDefault($id, $heat, $humidity)
	{
            $item = $this->elementRepository->findOneBy(array("id" => $id));
            
            
            $this->heatSensorRepository->insert($item, $heat);
            $this->humiditySensorRepository->insert($item, $humidity);
            echo "OK";
            die();
	}

}
