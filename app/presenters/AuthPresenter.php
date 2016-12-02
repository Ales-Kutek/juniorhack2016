<?php
/**
 * Soubor s hlavním presenterem.
 */

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;

/**
 * Zajišťuje, zdali je uživatel přihlášen, pro funkčnost je nutné z tohoto presenteru dědit ostatní presentery
 */
class AuthPresenter extends BasePresenter
{
    /** @var \App\Forms\AuthFormFactory @inject */
    public $authFactory;

    /**
     * Injekce objektů
     * @param \App\Forms\AuthFormFactory $authFactory Předání auth továrničky
     */
    public function __construct(\App\Forms\AuthFormFactory $authFactory)
    {
        $this->authFactory = $authFactory;
    }

    /**
     * Formulář pro login
     * @return \Nette\Application\UI\Form
     */
    protected function createComponentAuthForm()
    {
        return $this->authFactory->create(function () {
                $this->redirect('Homepage:');
            }, $this, $this->createTemplate());
    }

    /**
     * Render default
     * @return void
     * @param string $key
     */
    public function renderDefault($key)
    {
        
    }

    /**
     * Odhlášení
     * @return void
     */
    public function actionLogout()
    {
        $this->user->logout(TRUE);
        $this->flashMessage("Byli jste úspěšně odhlášeni.", 'success');
        $this->redirect("default");
    }
}