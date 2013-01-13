<?php

namespace AdminModule;

use NiftyGrid\Grid;

class productGrid extends Grid {

    /**
     * @var \ProductModel
     */
    protected $product;

    function __construct(\ProductModel $product) {
        parent::__construct();
        $this->product = $product;
    }

    protected function configure($presenter) {
        //Vytvoříme si zdroj dat pro Grid
        //Při výběru dat vždy vybereme id
        $source = new \NiftyGrid\DataSource\NDataSource($this->product->pokus());

        //Předáme zdroj
        $this->setDataSource($source);

        $this->addColumn('prod_id', 'ID', '100px', 30)
                ->setTextFilter();

        $this->addColumn('prod_name', 'ID', '400px')
                ->setTextFilter();

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