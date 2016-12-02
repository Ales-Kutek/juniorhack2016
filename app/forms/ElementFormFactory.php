<?php

/**
 * ElementFormFactory
 */

namespace App\Forms;

use Nette;
use Nette\Application\UI\Form;
use Nette\Utils\Validators;
use Nette\Utils\Strings;

/**
 * ElementFormFactory class
 */
class ElementFormFactory {

    /** @var FormFactory */
    private $factory;

    /** @var \Repository\Group $groupRepository */
    private $groupRepository;

    /** @var \Kdyby\Doctrine\EntityManager $em */
    private $em;

    /**
     * injekce objektů
     * @param \App\Forms\FormFactory $factory
     * @param \Repository\Group $groupRepository
     * @param \Kdyby\Doctrine\EntityManager $em
     * @param \Repository\UserCommission $userCommissionRepository
     * @param \Repository\UserCenter $userCenterRepository
     */
    public function __construct(FormFactory $factory, \Repository\Group $groupRepository, \Kdyby\Doctrine\EntityManager $em) {
        $this->factory = $factory;
        $this->em = $em;

        $this->groupRepository = $groupRepository;
    }

    /**
     * 
     * @param \App\Forms\callable $onSuccess
     * @param bool $pwdRequired (při editačním formuláře nastavit na TRUE)
     * @param \Nette\Application\IPresenter $presenter
     * @param Nette\Bridges\ApplicationLatte\Template $template
     * @return Nette\Application\UI\Form
     */
    public function create(callable $onSuccess, bool $pwdRequired, \Nette\Application\IPresenter $presenter, Nette\Bridges\ApplicationLatte\Template $template) {
        $form = $this->factory->create($presenter, $template);

        $form->addText("name", "Název místnosti")->setRequired();
        $form->addText("color", "Barva záložky")->setRequired()->setAttribute("class", "jscolor");
        
        $form->addSubmit("Odeslat");
        
        $form->onSuccess[] = function($form, $values) use ($onSuccess) {
            $onSuccess($form, $values);
        };
        
        return $form;
    }
}
