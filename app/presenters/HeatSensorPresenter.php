<?php

namespace App\Presenters;

use Nette;


class HeatSensorPresenter extends SecurePresenter
{
        /** @var \Repository\HeatSensor $heatSensorRepository */
        private $heatSensorRepository;
        
        /** @var \Repository\HumiditySensor $humiditySensorRepository */
        private $humiditySensorRepository;
        
        /** @var \Repository\Element $elementRepository */
        private $elementRepository;
        
        /** @var \Repository\HeatSensorLog $heatSensorLogRepository */
        private $heatSensorLogRepository;
        
        /** @var \App\Forms\ElementFormFactory $elementFormFactory */
        private $elementFormFactory;
        
        public function __construct(\Repository\Element $elementRepository,
                                    \Repository\HeatSensor $heatSensorRepository,
                                    \Repository\HumiditySensor $humiditySensorRepository,
                                    \App\Forms\ElementFormFactory $elementFormFactory,
                                    \Repository\HeatSensorLog $heatSensorLogRepository) {
            $this->elementRepository = $elementRepository;
            $this->humiditySensorRepository = $humiditySensorRepository;
            $this->heatSensorRepository = $heatSensorRepository;
            $this->heatSensorLogRepository = $heatSensorLogRepository;
            
            $this->elementFormFactory = $elementFormFactory;
        }
        
        public function renderNew($id) {
            $this->getComponent("new")->setDefaults(array("id" => $id));
        }

	public function renderDefault($code, $heat)
	{
            $this->heatSensorLogRepository->insert($code, $heat);
            echo "OK";
            die();
	}
        
            public function createComponentNew()
    {
        return $this->elementFormFactory->createSensor(function($form, $values) {
            try {
                $this->heatSensorRepository->insert($values["id"], $values["name"], $values["code"]);
                $this->flashMessage("Sensor byl úspěšně přidán.", 'success');
            } catch (\Exception $ex) {
                dump($ex);
                $this->flashMessage(\Message::UNKNOWN_ERROR, 'danger');
            }

            $this->redirect("Homepage:default");
            
        }, $this, $this->createTemplate());
    }

}
