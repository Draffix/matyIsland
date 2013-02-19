<?php

namespace AdminModule;

use Nette\Application\UI;

class CategoryPresenter extends BasePresenter {

    public function handleDeleteCategory($cat_id) {
        $this->category->deleteCategory($cat_id);
        $this->flashMessage('Kategorie byla smazána', 'success');
        $this->redirect('this');
    }

    public function renderDefault() {
        foreach ($this->category->fetchAllRoots() as $value) {
            $pocet[$value->cat_id] = $value;

            foreach ($this->category->countOfSubcategory($value->cat_id) as $value) {
                if (is_bool($value->pocet)) {
                    $countOfSubcategory = 0;
                } else {
                    $countOfSubcategory = $value->pocet;
                }
                $pocet[$value->cat_id]['countOfSubcategory'] = $countOfSubcategory;
            }
        }
        $this->template->categories = $pocet;
    }

    public function renderSubcategory($id, $categoryName) {
        $this->template->name = $categoryName;
        $this->template->cat_id = $id;
        $pocet = array();
        foreach ($this->category->fetchAllAcestors($id) as $value) {
            $pocet[$value->cat_id] = $value;

            foreach ($this->category->countOfSubcategory($value->cat_id) as $value) {
                $pocet[$value->cat_id]['countOfSubcategory'] = $value->pocet;
            }
        }
        $this->template->categories = $pocet;
    }

    // upraví jméno kategorie
    public function createComponentUpdateCategoryForm() {
        $form = new UI\Form;
        $form->addText('cat_name')
                ->addRule(UI\Form::FILLED);
        $form->addHidden('cat_id');
        $form->addSubmit('save_change');
        $form->onSuccess[] = callback($this, 'updateCategoryFormSubmitted');
        return $form;
    }

    public function updateCategoryFormSubmitted(UI\Form $form) {
        $values = $form->getValues();
        $this->category->updateCategoryName($values->cat_id, $values->cat_name);
        $this->flashMessage('Kategorie byla změněna', 'success');
        $this->redirect('Category:');
    }

    public function createComponentAddCategoryForm() {
        $form = new UI\Form;
        $form->addText('cat_name')
                ->addRule(UI\Form::FILLED);
        $form->addSubmit('save_change');
        $form->onSuccess[] = callback($this, 'addCategoryFormSubmitted');
        return $form;
    }

    public function addCategoryFormSubmitted(UI\Form $form) {
        $values = $form->getValues();
        $this->category->addCategory($values, 1);
        $this->flashMessage('Kategorie byla přidána', 'success');
        $this->redirect('this');
    }

    public function createComponentAddSubcategoryForm() {
        $form = new UI\Form;
        $form->addText('cat_name')
                ->addRule(UI\Form::FILLED);
        $form->addHidden('cat_id');
        $form->addSubmit('save_change');
        $form->onSuccess[] = callback($this, 'addSubcategoryFormSubmitted');
        return $form;
    }

    public function addSubcategoryFormSubmitted(UI\Form $form) {
        $values = $form->getValues();
        $this->category->addCategory($values, $values->cat_id);
        $this->flashMessage('Kategorie byla přidána', 'success');
        $this->redirect('this');
    }

}