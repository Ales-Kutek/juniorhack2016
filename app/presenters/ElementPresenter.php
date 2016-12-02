<?php
/**
 * Element presenter
 */

namespace App\Presenters;

use Nette;
use Entity;
use Ublaboo\DataGrid\DataGrid;
use Nette\Application\UI\Form;

/**
 * Element presenter class
 */
class ElementPresenter extends SecurePresenter
{
    /** @var \Repository\Element $elementRepository */
    private $elementRepository;
    
    /** @var \App\Forms\ElementFormFactory $personFormFactory */
    private $elementFormFactory;

    /**
     * Injekce objektů
     * @param \Repository\User $elementRepository
     * @param \App\Forms\ElementFormFactory $elementFormFactory
     * @param \App\Grids\UserGridFactory $userGridFactory
     */
    public function __construct(\Repository\Element $elementRepository,
                                \App\Forms\ElementFormFactory $elementFormFactory)
    {
            
        $this->elementRepository  = $elementRepository;
        $this->elementFormFactory = $elementFormFactory;
    }

    /**
     * Vykreslení úpravy 
     * @param int $id
     */
    public function renderEdit(int $id)
    {
        $data = $this->elementRepository->getSingle($id);
        
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
                    $this->elementRepository->remove($id);
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
     * Formulář pro novou osobu
     * @return Form
     */
    public function createComponentNew()
    {
        return $this->elementFormFactory->create(function(Form $form, $values) {
            try {
                $this->elementRepository->insert($values, $this->user->identity->id);
                $this->flashMessage("Místnost byla úspěšně přidána.", 'success');
            } catch (\Exception $ex) {
                dump($ex);
                $this->flashMessage(\Message::UNKNOWN_ERROR, 'danger');
            }

            $this->redirect("Homepage:default");
            
        }, true, $this, $this->createTemplate());
    }

    /**
     * Vytvoření formuláře pro uprávy jednotlivých osob
     * @return Form
     */
    public function createComponentEdit()
    {
        return $this->elementFormFactory->create(function(Form $form, $values) {
                $this->elementRepository->update($values, $values["id"]);

                $this->flashMessage("Místnost byla úspěšně upravena.", 'success');

                if ($form["submitandgo"]->isSubmittedBy()) {
                    $this->redirect("Homepage:default");
                }
        }, false, $this, $this->createTemplate());
    }
}