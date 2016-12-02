<?php
/**
 * Base presenter
 */

namespace App\Presenters;

use Nette;
use App\Model;
use Nette\Application\UI\Form;
use Instante\Bootstrap3Renderer\BootstrapRenderer;
use Nette\Utils\Html;

/**
 * Základní presenter
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{

    /** vytvoření komponent **/
    public function createComponent($name)
    {
        switch ($name) {
            case "search":
                $form = new Form();
                $form->setMethod("GET");
                $form->setAction("/search");
                
                $form->addText("q")->setAttribute("placeholder", "Vyhledávání...");
                $form->addSubmit("Submit");
                
                return $form;
            break;
        
            default:
                return parent::createComponent($name);
        }
    }
    
    /**
     * @param type $component - formulář ($this->getComponent("xxx"))
     * @param array $data - data z formuláře ($this->getComponent("xxx")->getValues(TRUE))
     * @param string $containerIndex - název dynamic containeru a polí pod ním ($form->addDynamic("xxx"))
     * @param string $dataKey - pokud se index v datech liší od názvu dynamic containeru a polí pod ním // tohle se moc nepoužije, ale pro případ..
     * @param string|array $valueKey - index pro $data, nastaví defaultní hodnoty k polím
     */
    public function _replicatorConstructFields(Nette\Application\UI\Form $component, array $data, string $containerIndex, $valueKey, $dataKey = NULL) {
        if ($dataKey === NULL) {
            $dataKey = $containerIndex;
        }
        
        $container = $component->getComponents();
        $container = $container[$containerIndex];
        
        if (!$component->isSubmitted() && count($data[$dataKey]) !== 0) {
            if (is_array($valueKey)) {
                foreach ($valueKey as $k => $v) {
                    $container->getComponent(0)->getComponent($k)->setValue($data[$dataKey][0][$v]);
                }
            } else {
                $container->getComponent(0)->getComponent($containerIndex)->setValue($data[$dataKey][0][$valueKey]);
            }
            
            unset($data[$dataKey][0]);
                        
            foreach($data[$dataKey] as $key => $value) {
                $field = $container->createOne()->getComponents();
                
                /** pokud máme více polí **/
                if (is_array($valueKey)) {
                    foreach ($valueKey as $k => $v) {
                        $field[$k]->setValue($value[$v]);
                    }
                } else {
                    $field[$containerIndex]->setValue($value[$valueKey]);
                }
            }
        }
        
        $this->_replicatorRemoveEmptyFields($component, $containerIndex, $valueKey);
    }
    
    /**
     * smaže přebytečná pole při submitu
     * @param Form $component
     * @param string $containerIndex
     */
    public function _replicatorRemoveEmptyFields(Nette\Application\UI\Form $component, $containerIndex, $valueKey = NULL) {
        $container = $component->getComponents();
        $container = $container[$containerIndex];
        
        if (!$container->isSubmittedBy()) {
            $components = $container->getComponents();
            
            foreach($components as $key => $item) {
                if (is_numeric($key)) {
                    $value = $item->getValues();
                    

                    
                    if (is_array($valueKey)) {
                        $delete = TRUE;
                        foreach ($valueKey as $k => $v) {
                            if (!($value[$k] == "" || $value[$k] === NULL)) {
                                $delete = FALSE;
                            } else if ($key == 0) {
                                $delete = FALSE;
                            }
                        }
                        
                        if ($delete) {
                            $container->remove($container->getComponent($key));
                        }
                    } else {                     
                        if (($value[$containerIndex] == "" || $value[$containerIndex] === NULL) && $key != 0) {
                            $container->remove($container->getComponent($key));
                        }
                    }
                }
            }
        }
    }
    
    /**
     * smaže všechna replikovaná pole
     * první pole ponechá, ale smaže jeho hodnotu
     * @param Form $component
     * @param string $containerIndex
     */
    public function _replicatorClearFields(Nette\Application\UI\Form $component, string $containerIndex) {
        $container = $component->getComponents();
        $container = $container[$containerIndex];

        $components = $container->getComponents();
            
        foreach($components as $key => $item) {
            if (is_numeric($key)) {
                $value = $item->getValues();

                if ($key != 0) {
                    $container->remove($container->getComponent($key));
                } else {
                    $container->getComponent($key)->getComponent($containerIndex)->setValue("");
                }
            }
        }
        
        return $container;
    }
    
    /**
     * 
     * @param Nette\Application\UI\Form $component
     * @param string $containerName
     * @param string $submitName
     * @param array $exceptions
     */
    public function _replicatorRemoveField(Nette\Application\UI\Form $component, string $containerName, string $submitName, array $exceptions = array()) {
            $comp = $component->getComponents();

            $container = $comp;
            $container = $container[$containerName];

            $comp = $container->getComponents();

            foreach ($comp as $key => $value) {
                if (!in_array($key, $exceptions)) {
                    $submit = $value->getComponents();

                    $submit = $submit[$submitName];

                    if ($submit->isSubmittedBy()) {
                        $container->remove($value);
                    }
                }
            }
    }
    
    public function handleSwitchFilter() {
        $section = $this->getSession()->getSection("user");
             
        if ($this->user->isInRole("Admin")) {
            if ($section->filter === TRUE) {
                $section->filter = FALSE;
            } else {
                $section->filter = TRUE;
            }
        } else {
            throw new Nette\Application\ForbiddenRequestException;
        }
    }
}