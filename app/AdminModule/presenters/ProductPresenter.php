<?php

namespace AdminModule;

use Nette\Application\UI;

class ProductPresenter extends BasePresenter {

    /** @persistent */
    public $id;

    public function renderAddProduct() {
        $name = array();
        foreach ($this->category->fetchAllCategoryNames() as $n) {
            $name[] = $n->cat_name;
        }
//        $_SESSION['b'] = $this->context->params['wwwwDir'] . '/images/';
    }

    protected function createComponentEditProductForm() {
        $form = new editProductForm($this->category);
        $folder = $this->getParam('folder');
        $form->addHidden('folder', $folder);
        $form->onSuccess[] = callback($this, 'editProductFormSubmitted');
        return $form;
    }

    public function editProductFormSubmitted(UI\Form $form) {
        $values = $form->getValues();
        
        $_SESSION['c'] = $values['prod_describe'];

        if ($values['image_name']->isImage() == FALSE ||
                $values['image_name'] == '') {
            $this->flashMessage('Nebyl zadán hlavní obrázek nebo není obrázek v platném formátu JPG, PNG nebo GIF', 'error');
            $this->redirect('this');
        }

        if ($values['image_name2'] != '' && $values['image_name2']->isImage() == FALSE ||
                $values['image_name3'] != '' && $values['image_name3']->isImage() == FALSE ||
                $values['image_name4'] != '' && $values['image_name4']->isImage() == FALSE) {
            $this->flashMessage('Nebyl zadán obrázek v platném formátu JPG, PNG nebo GIF', 'error');
            $this->redirect('this');
        }

        if ($values['image_name'] != '') {
            $this->moveImage($values['folder'], $values['image_name']);
        }
        if ($values['image_name2'] != '') {
            $this->moveImage($values['folder'], $values['image_name2']);
        }
        if ($values['image_name3'] != '') {
            $this->moveImage($values['folder'], $values['image_name3']);
        }
        if ($values['image_name4'] != '') {
            $this->moveImage($values['folder'], $values['image_name4']);
        }
    }

    private function moveImage($folder, $name) {
        $filename = $name->getSanitizedName();
        $targetPath = $this->context->params['wwwDir'] . '/images/upload/';
        if ($folder !== '') {
            $targetPath .= "/$folder";
        }
        // @TODO vyřešit kolize
        $name->move("$targetPath/$filename");
    }

}