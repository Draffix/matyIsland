<?php

namespace AdminModule;

use Nette\Application\UI;
use Nette\Image;

class ProductPresenter extends BasePresenter {

    protected $oldCategories;

    // odstraníme obrázek
    public function handleRemoveImage($id_product, $id_image) {
        if ($this->product->countImagesOfProduct($id_product) == 1) {
            $this->flashMessage('Produkt musí mít alespoň jeden obrázek', 'error');
            $this->redirect('this');
        }
        $name = $this->product->fetchSingleMainImage($id_image, $id_product)->image_name; // zjistíme jméno
        if ($this->product->fetchSingleMainImage($id_image, $id_product)->image_is_main == 1) { //pokud je to hlavní obrázek
            $this->product->deleteImage($id_image);
            $min = $this->product->findMinimumImageID($id_product)->min; // zjistíme další obrázek produktu (ten s menším id -> je nejblíže v pořadí)
            $this->product->updateImageWithHisID($min, $id_product); // nastavíme jako hlavní
        } else {
            $this->product->deleteImage($id_image);
        }
        $targetPath = $this->context->params['wwwDir'] . '/images/products/';
        unlink("$targetPath/$name");
        $thumbnailPath = $this->context->params['wwwDir'] . '/images/products/thumbnail';
        unlink("$thumbnailPath/$name");
        $this->flashMessage('Obrázek byl smazán', 'success');
        $this->redirect('Product:edit', $id_product);
    }

    // nastavíme obrázek jako hlavní
    public function handleMakeImageMain($id_product, $id_image) {
        $idMain = $this->product->findMainImageOfProduct($id_product)->image_id;
        $this->product->updateMainImageOfProduct($idMain, $id_image);
        $this->flashMessage('Obrázek byl zvolen jako hlavní', 'success');
        $this->redirect('this');
    }

    // udělá produkt neaktivní
    public function handleInactiveProduct($prod_id) {
        $this->product->updateProductToInactive($prod_id);
        $this->flashMessage('Produkt je nyní neaktivní', 'success');
        $this->redirect('this');
    }

    // udělá produkt aktivní
    public function handleActiveProduct($prod_id) {
        $this->product->updateProductToActive($prod_id);
        $this->flashMessage('Produkt je nyní aktivní', 'success');
        $this->redirect('this');
    }

    public function renderDefault() {
        $this->template->products = $this->product->fetchAllProductsWithImage();
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

    protected function createComponentAddProductForm() {
        $form = new editProductForm($this->category, $this->product, $this->supplier);
        $folder = $this->getParam('folder');
        $form->addHidden('folder', $folder);
        $form->onSuccess[] = callback($this, 'addProductFormSubmitted');
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
        if ($values['image_name2'] != '' && $values['image_name2']->isImage() == FALSE ||
                $values['image_name3'] != '' && $values['image_name3']->isImage() == FALSE ||
                $values['image_name4'] != '' && $values['image_name4']->isImage() == FALSE) {
            $this->flashMessage('Nebyl zadán obrázek v platném formátu JPG, PNG nebo GIF', 'error');
            return;
        }
        if ($values['image_name2'] == '') {
            $this->flashMessage('Nebyl zadán alespoň jeden obrázek, nebo jste
                pro nahrání obrázku nezvolili první možnost', 'error');
            return;
        }
        if ($values['image_name2'] != '') {
            $exists = $this->checkIfImageAlredyExists($values['folder'], $values['image_name2']);
            if ($exists == TRUE) {
                $this->flashMessage('Chyba při nahrávání. Jméno obrázku již v databázi existuje.
                Přejmenujte proto nahráváný obrázek a zkuste to znovu.', 'error');
                return;
            }
        }
        if ($values['image_name3'] != '') {
            $exists = $this->checkIfImageAlredyExists($values['folder'], $values['image_name3']);
            if ($exists == TRUE) {
                $this->flashMessage('Chyba při nahrávání. Jméno obrázku již v databázi existuje.
                Přejmenujte proto nahráváný obrázek a zkuste to znovu.', 'error');
                return;
            }
        }
        if ($values['image_name4'] != '') {
            $exists = $this->checkIfImageAlredyExists($values['folder'], $values['image_name4']);
            if ($exists == TRUE) {
                $this->flashMessage('Chyba při nahrávání. Jméno obrázku již v databázi existuje.
                Přejmenujte proto nahráváný obrázek a zkuste to znovu.', 'error');
                return;
            }
        }

        // uložíme do tabulky Product
        $lastID = $this->product->insertProduct($values);

        // uložíme do tabulky Category_has_product
        foreach ($values->category as $value) {
            $this->category->insertProductIntoCategoryHasProduct($value, $lastID);
        }

        // uložíme do tabulky Image
        if ($values['image_name2'] != '') {
            $this->moveImage($values['folder'], $values['image_name2']);
            $this->product->insertImage($lastID, $values['image_name2']->getSanitizedName(), 1);
        }
        if ($values['image_name3'] != '') {
            $this->moveImage($values['folder'], $values['image_name3']);
            $this->product->insertImage($lastID, $values['image_name3']->getSanitizedName());
        }
        if ($values['image_name4'] != '') {
            $this->moveImage($values['folder'], $values['image_name4']);
            $this->product->insertImage($lastID, $values['image_name4']->getSanitizedName());
        }

        $this->flashMessage('Uložení proběhlo v pořádku', 'success');
        $this->redirect('Product:');
    }

    protected function createComponentEditProductForm() {
        $form = new editProductForm($this->category, $this->product, $this->supplier, $this->getParameter('id'));
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
            return;
        }
        if ($values['image_name2'] != '') {
            $exists = $this->checkIfImageAlredyExists($values['folder'], $values['image_name2']);
            if ($exists == TRUE) {
                $this->flashMessage('Chyba při nahrávání. Jméno obrázku již v databázi existuje. 
                Přejmenujte proto nahráváný obrázek a zkuste to znovu.', 'error');
                return;
            }
        }
        if ($values['image_name3'] != '') {
            $exists = $this->checkIfImageAlredyExists($values['folder'], $values['image_name3']);
            if ($exists == TRUE) {
                $this->flashMessage('Chyba při nahrávání. Jméno obrázku již v databázi existuje. 
                Přejmenujte proto nahráváný obrázek a zkuste to znovu.', 'error');
                return;
            }
        }
        if ($values['image_name4'] != '') {
            $exists = $this->checkIfImageAlredyExists($values['folder'], $values['image_name4']);
            if ($exists == TRUE) {
                $this->flashMessage('Chyba při nahrávání. Jméno obrázku již v databázi existuje. 
                Přejmenujte proto nahráváný obrázek a zkuste to znovu.', 'error');
                return;
            }
        }

        // uložíme do tabulky Product
        $this->product->updateProduct($values, $this->getParameter('id'));

        // uložíme do tabulky Category_has_product
        $this->category->deleteAllProductIntoCategoryHasProduct($this->getParameter('id'));
        foreach ($values->category as $value) {
            $this->category->insertProductIntoCategoryHasProduct($value, $this->getParameter('id'));
        }
        // uložíme do tabulky Image
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

        $this->flashMessage('Uložení proběhlo v pořádku', 'success');
        $this->redirect('this');
    }

    private function moveImage($folder, $name) {
        $filename = $name->getSanitizedName();
        $targetPath = $this->context->params['wwwDir'] . '/images/products/';
        if ($folder !== '') {
            $targetPath .= "/$folder";
        }

        $name->move("$targetPath/$filename");

        $image = Image::fromFile("$targetPath/$filename");
        $image->resize(135, NULL);
        $thumbnailPath = $this->context->params['wwwDir'] . '/images/products/thumbnail/';
        $image->save("$thumbnailPath/$filename");
    }

    private function checkIfImageAlredyExists($folder, $name) {
        $exists = FALSE;
        $filename = $name->getSanitizedName();
        $targetPath = $this->context->params['wwwDir'] . '/images/products/';
        if ($folder !== '') {
            $targetPath .= "/$folder";
        }

        if (file_exists("$targetPath/$filename")) {
            $exists = TRUE;
        }
        return $exists;
    }

}