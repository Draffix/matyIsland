<?php

namespace AdminModule;

use NiftyGrid\Grid;

class categoryGrid extends Grid {

    /**
     * @var \CategoryModel
     */
    protected $category;

    function __construct(\CategoryModel $category) {
        parent::__construct();
        $this->category = $category;
    }

    protected function configure($presenter) {
        //Vytvoříme si zdroj dat pro Grid
        //Při výběru dat vždy vybereme id
        $source = new \NiftyGrid\DataSource\NDataSource($this->category->pokus());

        //Předáme zdroj
        $this->setDataSource($source);

        $this->addColumn('cat_id', 'ID kategorie', '250px', 30)
                ->setTextFilter();
        $this->addColumn('cat_name', 'Jméno kategorie', '200px')
                ->setTextFilter()
                ->setAutocomplete('1');

        $self = $this;

        $this->addButton("delete", "Smazat")
                ->setClass("delete")
                ->setLink(function($row) use ($self) {
                            return $self->link("delete!");
                        })
                ->setConfirmationDialog(function($row) {
                            return "Určitě chcete odstranit článek?";
                        });

        $this->addButton("edit", "Editovat")
                ->setClass("edit")
                ->setLink(function($row) use ($presenter) {
                            return $presenter->link("article:edit");
                        })
                ->setAjax(FALSE);

        $this->setWidth('800px');
    }

}
