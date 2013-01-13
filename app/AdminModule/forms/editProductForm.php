<?php

namespace AdminModule;

use Nette\Application\UI;

class editProductForm extends UI\Form {

    /**
     * @var \CategoryModel
     */
    protected $category;

    /**
     * @var \ProductModel
     */
    protected $product;
    protected $categorySelect = array();
    protected $categoryID; //ID zvolené první kategorie
    protected $selectedCat;
    protected $selectedID; //ID zvolené druhé kategorie (pokud existuje)
    protected $selectedCat2;
    protected $selectedID2; //ID zvolené třetí kategorie (pokud existuje)

    function __construct(\CategoryModel $category, \ProductModel $product, $id = NULL) {
        parent::__construct();
        $this->category = $category;
        $this->product = $product;

        //výpis všech jmen kategorií
        $name = array();
        foreach ($this->category->fetchAllCategoryNames() as $n) {
            $name[] = $n->cat_name;
        }

        //vymažeme první záznam aby se počítalo od jedničky. Pro ulehčení
        //uložení do databáze
        array_unshift($name, "toDelete");
        unset($name[0]);

        //výpis všech kategorií pro nepovinný select
        $name2 = array('Nezvoleno');
        foreach ($this->category->fetchAllCategoryNames() as $n) {
            $name2[] = $n->cat_name;
        }

        if ($id != NULL) {
            //vypíšeme všechny kategorie a zjistíme zvolený select
            foreach ($this->category->fetchAllCategoryNamesForProduct($id) as $f) {
                for ($i = 0; $i < count($name); $i++) {
                    if ($i == $f->cat_name) {
                        $this->categorySelect[] = $f->cat_name;
                    }
                }
            }

            //porovnáme hodnotu selectu se seznamem kategorií
            if (isset($this->categorySelect[1])) {
                foreach ($name2 as $n) {
                    if ($n == $this->categorySelect[1]) {
                        $this->selectedCat = $n;
                    }
                }
                $this->selectedID = $this->category->findCategoryID($this->selectedCat)->fetch()->cat_id;
            } else {
                $this->categorySelect[1] = $name2[0]; //jinak nastavíme jako "nezvoleno"
            }

            //porovnáme hodnotu selectu se seznamem kategorií
            if (isset($this->categorySelect[2])) {
                foreach ($name2 as $n) {
                    if ($n == $this->categorySelect[2]) {
                        $this->selectedCat2 = $n;
                    }
                }
                $this->selectedID2 = $this->category->findCategoryID($this->selectedCat2)->fetch()->cat_id;
            } else {
                $this->categorySelect[2] = $name2[0]; //jinak nastavíme jako "nezvoleno"
            }
        }
        if (isset($this->categorySelect[0])) {
            $this->categoryID = $this->category->findCategoryID($this->categorySelect)->fetch()->cat_id;
        }

        $prod_is_active = array(
            '1' => 'Ano',
            '0' => 'Ne'
        );

        if ($id != NULL) {
            //zjistíme jestli je produkt aktivní nebo ne a nastavíme mu defaultní hodnotu
            if ($this->product->fetchProductForDetail($id)->prod_is_active == 1) {
                $isActive = 1;
            } else {
                $isActive = 0;
            };
        }
        
        $this->addText('prod_name');
        if ($id === NULL) {
            $this->addSelect('category', 'kategorie', $name);
            $this->addSelect('category2', 'kategorie2', $name2);
            $this->addSelect('category3', 'kategorie3', $name2);
            $this->addRadioList('prod_is_active', '', $prod_is_active)
                    ->setDefaultValue('1');
        } else {
            $this->addSelect('category', 'kategorie', $name)
                    ->setDefaultValue($this->categoryID);
            $this->addSelect('category2', 'kategorie2', $name2)
                    ->setDefaultValue($this->selectedID);
            $this->addSelect('category3', 'kategorie3', $name2)
                    ->setDefaultValue($this->selectedID2);
            $this->addRadioList('prod_is_active', '', $prod_is_active)
                    ->setDefaultValue($isActive);
        }
        $this->addText('prod_on_stock');
        $this->addCheckbox('prod_isnew');
        $this->addText('prod_price');
        $this->addText('prod_code');
        $this->addTextArea('prod_describe')
                ->getControlPrototype()->class('mceEditor');
        $this->addTextArea('prod_long_describe')
                ->getControlPrototype()->class('mceEditor');
        $this->addUpload('image_name');
        $this->addUpload('image_name2');
        $this->addUpload('image_name3');
        $this->addUpload('image_name4');
        $this->addSubmit('save_change');
    }

    public function getOldCategories() {
        return array($this->categoryID, $this->selectedID, $this->selectedID2);
    }

}
