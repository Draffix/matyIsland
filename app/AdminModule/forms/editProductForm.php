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

    /**
     * @var \SupplierModel
     */
    protected $supplier;

    protected $selectedCategories; //ID zvolené první kategorie

    function __construct(\CategoryModel $category, \ProductModel $product, \SupplierModel $supplier, $id = NULL) {
        parent::__construct();
        $this->category = $category;
        $this->product = $product;
        $this->supplier = $supplier;

        //výpis všech jmen kategorií a získání ID do tagu option
        foreach ($this->category->fetchAllCategoryNames() as $key => $n) {
            $name[$key] = \Nette\Utils\Html::el('option')->value($key)->setText($n->cat_name);
        }

        unset($name[1]); // odstraníme kořenovou kategorii - "Hračky Matyland"

        foreach ($this->category->fetchAllCategoryNamesForProduct($id) as $val) {
            $this->selectedCategories[] = $val->category_cat_id; //vložíme zvolené kategorie
        }

        // výpis všech jmen dodavatelů a získání ID do tagu option
        foreach ($this->supplier->fetchAllSuppliers() as $key => $value) {
            $sup[$key] = \Nette\Utils\Html::el('option')->value($key)->setText($value->sup_name);
        }

        // zjistíme si zvoleného dodavatele
        if ($id != NULL) {
            $sup_id = $this->product->fetchProductForDetail($id);
            foreach ($this->supplier->fetchAllSuppliers() as $key => $value) {
                if ($sup_id->supplier_sup_id == $value['sup_id']) {
                    $selected_sup_id = $value['sup_id'];
                }
            }
        }

        if ($id === NULL) {
            $this->addMultiSelect('category', 'kategorie', $name);
            $this->addSelect('supplier', 'dodavatel', $sup);
        } else {
            $this->addMultiSelect('category', 'kategorie', $name)
                ->setDefaultValue($this->selectedCategories);
            $this->addSelect('supplier', 'dodavatel', $sup)
                ->setDefaultValue($selected_sup_id);
        }
        $this->addText('prod_name')
            ->addRule($this::FILLED);
        $this->addCheckbox('prod_is_active');
        $this->addText('prod_on_stock')
            ->addRule($this::FILLED);
        $this->addCheckbox('prod_isnew');
        $this->addText('prod_purchase_price');
        $this->addText('prod_price')
            ->addRule($this::FILLED);
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
