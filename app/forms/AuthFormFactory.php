<?php

/**
 * AuthFormFactory
 */
namespace App\Forms;

use Nette;
use Nette\Application\UI\Form;
use Nette\Security\User;

/**
 * AuthFormFactory class
 */
class AuthFormFactory {

    /** @var FormFactory */
    private $factory;

    /** @var User */
    private $user;

    public function __construct(FormFactory $factory, User $user) {
        $this->factory = $factory;
        $this->user = $user;
    }


    /**
     * vytvoření komponenty
     * @param \App\Forms\callable $onSuccess
     * @param Nette\Application\UI\Presenter $presenter
     * @param Nette\Bridges\ApplicationLatte\Template $template
     * @return form
     */
    public function create(callable $onSuccess, Nette\Application\IPresenter $presenter, Nette\Bridges\ApplicationLatte\Template $template) {
        $form = $this->factory->create($presenter, $template);
        $form->addText('username', 'Uživatelský email:')
                ->setRequired('Prosím vložte uživatelské jméno.')
                ->setAttribute('placeholder', 'Uživatelské jméno')
                ->setAttribute('class', 'form-control');
        $form->addPassword('password', 'Password:')
                ->setRequired('Prosím vložte heslo.')
                ->setAttribute('placeholder', 'Heslo')
                ->setAttribute('class', 'form-control');
        $form->addCheckbox('remember', 'Pamatovat si mě');
        $form->addSubmit('send', 'Přihlásit')
                ->setAttribute('class', 'btn btn-primary');
        
        $form->onSuccess[] = function (Form $form, $values) use ($onSuccess, $presenter) {
            try {
                $this->user->setExpiration($values->remember ? '14 days' : '20 minutes');
                $this->user->login($values->username, $values->password);
                 $presenter->flashMessage("Byli jste úspěšně přihlášeni!", "success");
            } catch (Nette\Security\AuthenticationException $e) {
                $form->addError($e->getMessage());
                return;
            }
            $onSuccess();
        };
        return $form;
    }

}
