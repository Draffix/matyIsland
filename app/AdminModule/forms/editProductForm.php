<?php

namespace AdminModule;

use Nette\Application\UI;

class editProductForm extends UI\Form {

    /**
     * @var \CategoryModel
     */
    protected $category;
    protected $categorySelect = array();

    function __construct(\CategoryModel $category, $id = NULL) {
        parent::__construct();
        $this->category = $category;

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

        //vypíšeme všechny kategorie a zjistíme zvolený select
        foreach ($this->category->fetchAllCategoryNamesForProduct($id) as $f) {
            for ($i = 0; $i < count($name); $i++) {
                if ($i == $f->cat_name) {
                    $this->categorySelect[] = $f->cat_name;
                }
            }
        }
        if (!isset($this->categorySelect[1])) {
            $this->categorySelect[1] = $name2[0];
        }

        if (!isset($this->categorySelect[2])) {
            $this->categorySelect[2] = $name2[0];
        }

        $prod_is_active = array(
            '1' => 'Ano',
            '0' => 'Ne'
        );

        $this->addText('prod_name');
        if ($id === NULL) {
            $this->addSelect('category', 'kategorie', $name);
            $this->addSelect('category2', 'kategorie2', $name2);
            $this->addSelect('category3', 'kategorie3', $name2);
        } else {
            $this->addSelect('category', 'kategorie', $name)
                    ->setPrompt($this->categorySelect[0]);
            $this->addSelect('category2', 'kategorie2', $name2)
                    ->setPrompt($this->categorySelect[1]);
            $this->addSelect('category3', 'kategorie3', $name2)
                    ->setPrompt($this->categorySelect[2]);
        }
        $this->addText('prod_on_stock');
        $this->addCheckbox('prod_isnew');
        $this->addRadioList('prod_is_active', '', $prod_is_active)
                ->setDefaultValue('1');
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

}
