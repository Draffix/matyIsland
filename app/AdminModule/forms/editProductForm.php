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
    protected $selectedCategories; //ID zvolené první kategorie

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

        foreach ($this->category->fetchAllCategoryNamesForProduct($id) as $val) {
            $this->selectedCategories[] = $val->category_cat_id; //vložíme zvolené kategorie
        }

        if ($id === NULL) {
            $this->addMultiSelect('category', 'kategorie', $name);
        } else {
            $this->addMultiSelect('category', 'kategorie', $name)
                    ->setDefaultValue($this->selectedCategories);
        }
        $this->addText('prod_name');
        $this->addCheckbox('prod_is_active');
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
        return $this->selectedCategories;
    }

}
