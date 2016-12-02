<?php

/**
 * FormFactory
 */
namespace App\Forms;

use Nette;
use Nette\Application\UI\Form;
use Instante\Bootstrap3Renderer\BootstrapRenderer;

/**
 * Základní továrnička pro formuláře
 */
class FormFactory
{
    /**
     * 
     * @param \Nette\Application\IPresenter $presenter
     * @param Nette\Bridges\ApplicationLatte\Template $template
     * @return \Nette\Application\UI\Form
     */
    public function create(\Nette\Application\IPresenter $presenter, Nette\Bridges\ApplicationLatte\Template $template)
    {
        
        $form = new Form();
                
        $template = $template->setFile(APP_DIR. DIRECTORY_SEPARATOR . 'presenters' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . '@form.latte');
        $renderer = new \Instante\Bootstrap3Renderer\MyBootstrapRenderer($template);
        
        $renderer->setMode(\Instante\Bootstrap3Renderer\MyBootstrapRenderer::MODE_VERTICAL);
        
        $form->setRenderer($renderer);
        
        return $form;
    }
}