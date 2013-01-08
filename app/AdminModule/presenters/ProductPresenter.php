<?php

namespace AdminModule;

use Nette\Application\UI;

class ProductPresenter extends BasePresenter {

    /** @persistent */
    public $id;

    protected function createComponentPaginator() {
        $visualPaginator = new \VisualPaginator();
        return $visualPaginator;
    }

    public function renderAddProduct() {
        $name = array();
        foreach ($this->category->fetchAllCategoryNames() as $n) {
            $name[] = $n->cat_name;
        }
    }

    public function renderShow() {
        $paginator = $this['paginator']->getPaginator();
        $paginator->itemsPerPage = 30;
        $paginator->setBase(1);
        $paginator->itemCount = $this->product->countProducts();
        $products = $this->product->fetchAllProductsWithOffset($paginator->itemsPerPage, $paginator->offset);

        $this->template->products = $products;
    }

    public function renderDetail($id) {
        //získáme data o produktu
        $this->template->product = $this->product->fetchProductForDetail($id);
        //získáme všechny produktové obrázky
        $this->template->images = $this->product->fetchAllProductsImages($id);
        //získáme všechny kategorie do kterých produkt spadá
        $this->template->category = $this->category->fetchAllCategoryNamesForProduct($id);
    }

    public function renderEdit($id) {
        //získáme data o produktu
        $this->template->product = $this->product->fetchProductForDetail($id);
        //získáme všechny produktové obrázky
        $this->template->images = $this->product->fetchAllProductsImages($id);
        //získáme všechny kategorie do kterých produkt spadá
        $this->template->category = $this->category->fetchAllCategoryNamesForProduct($id);
    }

    protected function createComponentEditProductForm() {
        $form = new editProductForm($this->category, $this->id);
        $folder = $this->getParam('folder');
        $form->addHidden('folder', $folder);
        $form->onSuccess[] = callback($this, 'editProductFormSubmitted');
        return $form;
    }

    public function editProductFormSubmitted(UI\Form $form) {
        $values = $form->getValues();

        if ($values['prod_name'] == '' || $values['prod_on_stock'] == '' ||
                $values['prod_price'] == '' || $values['prod_code'] == '' ||
                $values['prod_describe'] == '' || $values['prod_long_describe'] == '') {
            $this->flashMessage('Nebyly vyplněny všechny povinné údaje', 'error');
            return;
        }


        // provedeme kontrolu obrázků
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



        // uložíme do tabulky Product
        $lastID = $this->product->insertProduct($values);

        // uložíme do tabulky Category_has_product
        $this->category->insertProductIntoCategoryHasProduct($values['category'], $lastID);

        if ($values['category2'] != 0) {
            $this->category->insertProductIntoCategoryHasProduct($values['category2'], $lastID);
        }
        if ($values['category3'] != 0) {
            $this->category->insertProductIntoCategoryHasProduct($values['category3'], $lastID);
        }

        if ($values['image_name'] != '') {
            $this->moveImage($values['folder'], $values['image_name']);
            $this->product->insertImage($lastID, $values['image_name']->getSanitizedName(), 1);
        }
        if ($values['image_name2'] != '') {
            $this->moveImage($values['folder'], $values['image_name2']);
            $this->product->insertImage($lastID, $values['image_name2']->getSanitizedName());
        }
        if ($values['image_name3'] != '') {
            $this->moveImage($values['folder'], $values['image_name3']);
            $this->product->insertImage($lastID, $values['image_name3']->getSanitizedName());
        }
        if ($values['image_name4'] != '') {
            $this->moveImage($values['folder'], $values['image_name4']);
            $this->product->insertImage($lastID, $values['image_name4']->getSanitizedName());
        }

        $this->flashMessage('Uložení proběhlo v pořádku', 'valid');
        $this->redirect('this');
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