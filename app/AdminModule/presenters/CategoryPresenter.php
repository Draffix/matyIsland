<?php

namespace AdminModule;

class CategoryPresenter extends BasePresenter {

    public function renderDefault() {
        
    }

    protected function createComponentCategoryGrid($name) {
        $grid = new \Grido\Grid($this, $name);
        $grid->setModel($this->category->pokus());
        $grid->addColumn('cat_id', 'ID');
        $grid->addFilter('cat_id', 'Birthday');
        $grid->addColumn('cat_name', 'Jméno');
    }

}