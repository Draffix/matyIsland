<?php

namespace AdminModule;

use Nette\Application\UI;

class ProductPresenter extends BasePresenter {

    protected $oldCategories;

    public function handlePokus($imageID) {
        dump($imageID);
    }

    protected function createComponentPaginator() {
        $visualPaginator = new \VisualPaginator();
        return $visualPaginator;
    }

    public function renderDefault() {
        $this->template->products = $this->product->fetchAllProductsWithImage();
    }

    protected function createComponentProductGrid($name) {
        $grid = new \Grido\Grid($this, $name);
        $grid->setModel($this->product->pokus());
        $grid->addColumn('image_id', 'ID');
        $grid->addFilter('image_id', 'Birthday');
        $grid->addColumn('image_name', 'name');
        $grid->addColumn('prod_name', 'namee');
    }

    public function renderAddProduct() {
        $name = array();
        foreach ($this->category->fetchAllCategoryNames() as $n) {
            $name[] = $n->cat_name;
        }
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

    // odstraníme obrázek
    public function handleRemoveImage($id_image, $id_product, $name) {
        if ($this->product->fetchSingleMainImage($id_image, $id_product)->image_is_main == 1) { //pokud je to hlavní obrázek
            $this->product->deleteImage($id_image);
            $min = $this->product->findMinimumImageID($id_product)->min; // zjistíme další obrázek produktu (ten s menším id -> je nejblíže v pořadí)
            $this->product->updateImageWithHisID($min, $id_product);
        } else {
            $this->product->deleteImage($id_image);
        }
        $targetPath = $this->context->params['wwwDir'] . '/images/products/';
        unlink("$targetPath/$name");
        $this->redirect('Product:edit', $id_product);
    }

    // nastavíme obrázek jako hlavní
    public function handleMakeImageMain($id_product, $id_image) {
        $idMain = $this->product->findMainImageOfProduct($id_product)->image_id;
        $this->product->updateMainImageOfProduct($idMain, $id_image);
    }

    protected function createComponentAddProductForm() {
        $form = new editProductForm($this->category, $this->product, $this->getParameter('id'));
        $folder = $this->getParam('folder');
        $form->addHidden('folder', $folder);
        $form->onSuccess[] = callback($this, 'editProductFormSubmitted');
        return $form;
    }

    public function addProductFormSubmitted(UI\Form $form) {
        $values = $form->getValues();

        if ($values['prod_name'] == '' || $values['prod_on_stock'] == '' ||
                $values['prod_price'] == '' || $values['prod_describe'] == '' ||
                $values['prod_long_describe'] == '') {
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

    protected function createComponentEditProductForm() {
        $form = new editProductForm($this->category, $this->product, $this->getParameter('id'));
        $folder = $this->getParam('folder');
        $form->addHidden('folder', $folder);

        $this->oldCategories = $form->getOldCategories();

        $form->onSuccess[] = callback($this, 'editProductFormSubmitted');
        return $form;
    }

    public function editProductFormSubmitted(UI\Form $form) {
        $values = $form->getValues();

        if ($values['prod_name'] == '' || $values['prod_on_stock'] == '' ||
                $values['prod_price'] == '' || $values['prod_describe'] == '' ||
                $values['prod_long_describe'] == '') {
            $this->flashMessage('Nebyly vyplněny všechny povinné údaje', 'error');
            return;
        }

        // provedeme kontrolu obrázků
        if ($values['image_name2'] != '' && $values['image_name2']->isImage() == FALSE ||
                $values['image_name3'] != '' && $values['image_name3']->isImage() == FALSE ||
                $values['image_name4'] != '' && $values['image_name4']->isImage() == FALSE) {
            $this->flashMessage('Nebyl zadán obrázek v platném formátu JPG, PNG nebo GIF', 'error');
            $this->redirect('this');
        }

        $_SESSION['b'] = $this->oldCategories;

        // uložíme do tabulky Product
        $this->product->updateProduct($values, $this->getParameter('id'));

        // uložíme do tabulky Category_has_product
        if ($values['category'] == $values['category2'] || $values['category'] == $values['category3']) {
            $this->flashMessage('Produkt nemůže být ve dvou nebo více stejných kategoriích', 'error');
            $this->redirect('this');
        } else {
            $this->category->updateProductIntoCategoryHasProduct($this->oldCategories[0], $values['category'], $this->getParameter('id'));
        }

        if ($values['category2'] != 0) {
            if ($values['category2'] == $values['category'] || $values['category2'] == $values['category3']) {
                $this->flashMessage('Produkt nemůže být ve dvou nebo více stejných kategoriích', 'error');
                $this->redirect('this');
            }
            if (!isset($this->oldCategories[1]) || $this->oldCategories[1] == NULL) {
                $this->category->insertProductIntoCategoryHasProduct($values['category2'], $this->getParameter('id'));
            } else {
                $this->category->updateProductIntoCategoryHasProduct($this->oldCategories[1], $values['category2'], $this->getParameter('id'));
            }
        } else {
            $this->category->deleteProductIntoCategoryHasProduct($this->oldCategories[1], $this->getParameter('id'));
        }

        if ($values['category3'] != 0) {
            if ($values['category3'] == $values['category'] || $values['category3'] == $values['category2']) {
                $this->flashMessage('Produkt nemůže být ve dvou nebo více stejných kategoriích', 'error');
                $this->redirect('this');
            }
            if (!isset($this->oldCategories[2]) || $this->oldCategories[2] == NULL) {
                $this->category->insertProductIntoCategoryHasProduct($values['category3'], $this->getParameter('id'));
            } else {
                $this->category->updateProductIntoCategoryHasProduct($this->oldCategories[2], $values['category3'], $this->getParameter('id'));
            }
        } else {
            $this->category->deleteProductIntoCategoryHasProduct($this->oldCategories[2], $this->getParameter('id'));
        }



        if ($values['image_name2'] != '') {
            $this->moveImage($values['folder'], $values['image_name2']);
            $this->product->insertImage($this->getParameter('id'), $values['image_name2']->getSanitizedName());
        }
        if ($values['image_name3'] != '') {
            $this->moveImage($values['folder'], $values['image_name3']);
            $this->product->insertImage($this->getParameter('id'), $values['image_name3']->getSanitizedName());
        }
        if ($values['image_name4'] != '') {
            $this->moveImage($values['folder'], $values['image_name4']);
            $this->product->insertImage($this->getParameter('id'), $values['image_name4']->getSanitizedName());
        }

        $this->flashMessage('Uložení proběhlo v pořádku', 'valid');
        $this->redirect('this');
    }

    private function moveImage($folder, $name) {
        $filename = $name->getSanitizedName();
        $targetPath = $this->context->params['wwwDir'] . '/images/products/';
        if ($folder !== '') {
            $targetPath .= "/$folder";
        }
        // @TODO vyřešit kolize
        $name->move("$targetPath/$filename");
    }

}