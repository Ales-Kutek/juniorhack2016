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
        
        /** @var \Repository\HeatSensorLog $heatSesorLogRepository */
        private $heatSesorLogRepository;
        
        /** @var \App\Forms\ElementFormFactory $elementFormFactory */
        private $elementFormFactory;
        
        public function __construct(\Repository\Element $elementRepository,
                                    \Repository\HeatSensor $heatSensorRepository,
                                    \Repository\HumiditySensor $humiditySensorRepository,
                                    \App\Forms\ElementFormFactory $elementFormFactory ) {
            $this->elementRepository = $elementRepository;
            $this->humiditySensorRepository = $humiditySensorRepository;
            $this->heatSensorRepository = $heatSensorRepository;
            
            $this->elementFormFactory = $elementFormFactory;
        }

	public function renderDefault($id, $heat, $humidity)
	{
            die();
	}
        
            public function createComponentNew()
    {
        return $this->elementFormFactory->createSensor(function($form, $values) {
            try {
                $this->heatSensorRepository->insert($this->getParameter("id"), $values["name"], $values["code"]);
                $this->flashMessage("Sensor byl úspěšně přidán.", 'success');
            } catch (\Exception $ex) {
                dump($ex);
                $this->flashMessage(\Message::UNKNOWN_ERROR, 'danger');
            }

            $this->redirect("Homepage:default");
            
        }, $this, $this->createTemplate());
    }

}
