<?php

/**
 * UserFormFactory
 */

namespace App\Forms;

use Nette;
use Nette\Application\UI\Form;
use Nette\Utils\Validators;
use Nette\Utils\Strings;

/**
 * UserFormFactory class
 */
class UserFormFactory {

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

            $form->addGroup('Základní údaje');
            $form->addText("name", "Jméno")->setAttribute("data-heading", "true")->setRequired()
                    ->setAttribute("data-input", "placeit")
                    ->setAttribute("Placeholder", "Jméno");
            $form->addText("surname", "Příjmení")->setAttribute("data-heading", "true")->setRequired()
                    ->setAttribute("data-input", "placeit")
                    ->setAttribute("Placeholder", "Příjmení");
            $form->addText("username", "Uživatelské jméno")->setRequired()
                    ->setAttribute("data-input", "placeit")
                    ->setAttribute("Placeholder", "Uživatelské jméno");
            $form->addText("phone", "Mob. telefon")
                    ->addRule(\ValidationRule::PHONE_NUMBER, \Message::BAD_PHONE_NUMBER)
                    ->setRequired()
                    ->setAttribute("data-input", "placeit")
                    ->setAttribute("data-inputmask", '"mask": "+999 999 999 999"')
                    ->setDefaultValue("+420")
                    ->setAttribute("Placeholder", "Telefon");
            $form->addText("email", "Email")
                    ->addRule(\ValidationRule::EMAIL, \Message::BAD_EMAIL)
                    ->setRequired()->setAttribute("data-input", "placeit")
                    ->setAttribute("Placeholder", "Email");

            $form->addGroup('Adresa');
            $form->addText("street", "Ulice")->setRequired()
                    ->setAttribute("data-input", "placeit")
                    ->setAttribute("Placeholder", "Ulice");
            $form->addText("city", "Město")->setRequired()
                    ->setAttribute("data-input", "placeit")
                    ->setAttribute("Placeholder", "Město");
            $form->addText("zip", "PSČ")
                    ->addRule(\ValidationRule::ZIP, \Message::BAD_ZIP)
                    ->setRequired()
                    ->setAttribute("data-input", "placeit")
                    ->setAttribute("Placeholder", "PSČ");

            if ($pwdRequired === true) {
                $form->addText("password", "Heslo")->setType("password")->setRequired()
                        ->setAttribute("data-input", "placeit")
                        ->setAttribute("Placeholder", "Heslo");
                $form->addText("password_repeat", "Heslo znovu")->setType("password")
                        ->setRequired()
                        ->addRule(Form::EQUAL, 'Hesla se neshodují.', $form['password'])
                        ->setAttribute("data-input", "placeit")
                        ->setAttribute("Placeholder", "Heslo znovu");
            } else {
                $form->addText("password", "Heslo")->setType("password")
                        ->setAttribute("data-input", "placeit")
                        ->setAttribute("Placeholder", "Heslo");
                $form->addText("password_repeat", "Heslo znovu")->setType("password")->setAttribute("data-input", "placeit")
                        ->setAttribute("Placeholder", "Heslo znovu")
                        ->addConditionOn($form['password'], Form::FILLED)
                        ->addRule(Form::EQUAL, 'Hesla se neshodují', $form['password'])->setRequired();
            }
        
        $form->addGroup('Nastavení');
            $form->addCheckbox("blocked", "Zablokovat přístup do systému")
                    ->addCondition($form::EQUAL, TRUE)
                        ->toggle("blocked_reason-pair");
            $form->addTextArea("blocked_reason")->setHtmlId("blocked_reason")
                ->addConditionOn($form["blocked"], Form::EQUAL, TRUE)
                    ->setRequired();

            $form->addGroup('Data');
            $groups = $this->groupRepository->getAllAsTwoDimensionalArray();

            $form->addSelect("group", "Skupina", $groups)
                    ->setPrompt("--------")
                    ->setRequired()
                    ->addCondition($form::EQUAL, array_search("Obchodník", $groups))
                    ->toggle("max_discount-pair");
        
        $form->addHidden("id");

        $form->addGroup('navigace');
        $form->addSubmit("submitandgo", "Uložit")->setAttribute("class", "saveButton");
        $form->addSubmit("submit", "Uložit a zůstat")->setAttribute("class", "saveButton");

        $form->onValidate[] = array($this, "validateForm");
        $form->onSuccess[] = function($form, $values) use ($onSuccess) {
            unset($values["password_repeat"]);
            $onSuccess($form, $values);
        };

        return $form;
    }

    /**
     * Dodatečná validační pravidla
     * @param Nette\Application\UI\Form $form
     * @param array $values
     */
    public function validateForm(Form $form, $values) {
        $userRepository = $this->em->getRepository(\Entity\User::getClassName());

        if ($values["id"] !== NULL) {
            $user = $userRepository->findOneBy(array("id" => $values["id"]));
        }

        /** ověří unikátní email */
        if (isset($values["email"])) {
            if (($user !== NULL && $user->email != $values["email"]) || $values["id"] == NULL) {
                if ($userRepository->findOneBy(array("email" => $values["email"])) !== NULL) {
                    $form->addError(\Message::UNIQUE_EMAIL);
                }
            }
        }

        /** ověřní unikátní uživatelské jméno */
        if (isset($values["username"])) {
            if (($user !== NULL && $user->username != $values["username"]) || $values["id"] == NULL) {
                if ($userRepository->findOneBy(array("username" => $values["username"])) !== NULL) {
                    $form->addError(\Message::UNIQUE_USERNAME);
                }
            }
        }
    }
}
