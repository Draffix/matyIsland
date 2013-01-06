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

        if ($values['image']->isImage() == FALSE || $values['image'] == '') {
            $this->flashMessage('Nebyl zadán hlavní obrázek nebo není obrázek v platném formátu JPG, PNG nebo GIF', 'error');
            $this->redirect('this');
        }

        if ($values['image']->isOk()) {
            $filename = $values['image']->getSanitizedName();
            $targetPath = $this->context->params['wwwDir'] . '/images/';
            if ($values['folder'] !== '') {
                $targetPath .= "/$values[folder]";
            }
            // @TODO vyřešit kolize
            $values['image']->move("$targetPath/$filename");
        } else {
            $this->flashMessage('chyba', 'error');
            $this->redirect('this');
        }


//        if ($values->image->isImage() == FALSE) {
//            $this->flashMessage('chyba');
//            $this->redirect('this');
//            return;
//        }
    }

}