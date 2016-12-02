<?php
/**
 * Person presenter
 */

namespace App\Presenters;

use Nette;
use Entity;
use Ublaboo\DataGrid\DataGrid;
use Nette\Application\UI\Form;

/**
 * Person presenter class
 */
class UserPresenter extends SecurePresenter
{
    /** @var \Repository\Person $user */
    private $userRepository;
    
    /** @var \App\Forms\UserFormFactory $personFormFactory */
    private $userFormFactory;
    
    /** @var \App\Grids\UserGridFactory $personGridFactory */
    private $userGridFactory;

    /**
     * Injekce objektů
     * @param \Repository\User $userRepository
     * @param \App\Forms\UserFormFactory $userFormFactory
     * @param \App\Grids\UserGridFactory $userGridFactory
     */
    public function __construct(\Repository\User $userRepository,
                                \App\Forms\UserFormFactory $userFormFactory,
                                \App\Grids\UserGridFactory $userGridFactory)
    {
            
        $this->userRepository  = $userRepository;
        $this->userFormFactory = $userFormFactory;
        $this->userGridFactory = $userGridFactory;
    }
    
    public function renderNew() {
        if ($this->user->isInRole("Operátor")) {
            throw new Nette\Application\ForbiddenRequestException();
        }
    }
    
    /**
     * Vykreslení detailu
     * @param int $id
     */
    public function renderDetail(int $id)
    {
        $data = $this->userRepository->getSingle($id, true);

        $this->template->data = $data;
    }

    /**
     * Vykreslení úpravy 
     * @param int $id
     */
    public function renderEdit(int $id)
    {
        $data = $this->userRepository->getSingle($id);

        $data["group"] = $data["group"]["id"];
        
        $this->template->data = $data;
        
        $this->getComponent("edit")->setDefaults($data);
    }

    /**
     * Odchycení signálu pro smazaní z datagridu
     * @param int $id
     */
    public function handleDelete(int $id, $confirm = FALSE) {
        if ($this->user->isInRole("Admin")) {
            if(!$confirm) {
                $this->template->confirm = true;
            } else {
                try {
                    $this->userRepository->remove($id);
                    $this->flashMessage("Položka byla smazána.", 'success');
                } catch (\Exception $ex) {
                    dump($ex);
                    $this->flashMessage("Položka nebyla smazána.", 'danger');
                }
            }
        } else {
            throw new Nette\Application\ForbiddenRequestException();
        }
    }
    /**
     * vytvoření datagridu
     * @return DataGrid
     */
    public function createComponentGrid()
    {
        $source = $this->userRepository->getAll(true);

        return $this->userGridFactory->create($source, $this);
    }

    /**
     * Formulář pro novou osobu
     * @return Form
     */
    public function createComponentNew()
    {
        return $this->userFormFactory->create(function(Form $form, $values) {
            
            try {
                $this->userRepository->insert($values);
                $this->flashMessage("Uživatel byl úspěšně přidán.", 'success');
            } catch (\Exception $ex) {
                dump($ex);
                $this->flashMessage(\Message::UNKNOWN_ERROR, 'danger');
            }      

            if ($form["submitandgo"]->isSubmittedBy()) {
                $this->redirect("User:default");
            }
        }, true, $this, $this->createTemplate());
    }

    /**
     * Vytvoření formuláře pro uprávy jednotlivých osob
     * @return Form
     */
    public function createComponentEdit()
    {
        return $this->userFormFactory->create(function(Form $form, $values) {
                $this->userRepository->update($values, $values["id"]);

                $this->flashMessage("Uživatel byl úspěšně upraven.", 'success');

                if ($form["submitandgo"]->isSubmittedBy()) {
                    $this->redirect("User:default");
                }
        }, false, $this, $this->createTemplate());
    }
}