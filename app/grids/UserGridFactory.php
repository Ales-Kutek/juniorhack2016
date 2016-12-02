<?php

/**
 * UserGridFactory
 */
namespace App\Grids;

use Nette;
use Ublaboo\DataGrid\DataGrid;

/**
 * UserGridFactory class
 */
class UserGridFactory
{
    /** @var GridFactory */
    private $factory;
    
    /**
     * Injekce objetů
     * @param \App\Grids\GridFactory $factory
     */
    public function __construct(GridFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param mixed $source
     * @return Ublaboo\DataGrid\DataGrid
     */
    public function create($source, Nette\Application\UI\Presenter $presenter)
    {
        $grid = $this->factory->create($source);

        $grid->addColumnLink("name", "Jméno", "detail")->setSortable()->setFilterText();
        $grid->addColumnLink("surname", "Příjmení", "detail")->setSortable()->setFilterText();
        $grid->addColumnLink("username", "Uživatelské jméno", "detail")->setSortable()->setFilterText();
        $grid->addColumnLink("email", "Email", "detail")->setSortable()->setFilterText();
        $grid->addColumnLink("phone", "Telefon", "detail")->setSortable()->setFilterText();
        $grid->addColumnLink("group", "Skupina", "detail", "group.name")->setSortable()->setFilterText();

        $grid->setRowCallback(function($item, $tr) {
            if ($item->blocked == TRUE) {
                $tr->addClass("banned");
            }
        });

        $grid->addAction('edit', '')
            ->setIcon('pencil')
            ->setTitle('Upravit');

        if ($presenter->user->isInRole("Admin")) {
            $grid->addAction('delete', '', 'delete!')
                ->setIcon('trash')
                ->setTitle('Smazat')
                ->setClass('btn btn-xs btn-danger')
                ->setConfirm(function($item) {
                    return 'Opravdu chcete smazat položku "%s"?';
                }, 'id');
        }
            
        return $grid;
    }
    
    /**
     * grid pro jednoduché moduly..
     * @param mixed $source
     * @return Ublaboo\DataGrid\DataGrid
     */
    public function createSimpleModule($source, $deleteMessage) {
        $grid = $this->factory->create($source);

        $grid->addColumnText("name", "Název")->setSortable()->setFilterText();

        $grid->addAction('edit', '')
            ->setIcon('pencil')
            ->setTitle('Upravit');

        $grid->addAction('delete', '', 'delete!')
            ->setIcon('trash')
            ->setTitle('Smazat')
            ->setClass('btn btn-xs btn-danger')
            ->setConfirm(function($item) {
                return "Opravdu chcete smazat $deleteMessage {$item->name}?";
            }, 'id');

        return $grid;
    }
}