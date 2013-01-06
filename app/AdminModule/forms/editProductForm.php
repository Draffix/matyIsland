<?php

namespace AdminModule;

use Nette\Application\UI;

class editProductForm extends UI\Form {

    /**
     * @var \CategoryModel
     */
    protected $category;

    function __construct(\CategoryModel $category) {
        parent::__construct();
        $this->category = $category;

        $name = array();
        foreach ($this->category->fetchAllCategoryNames() as $n) {
            $name[] = $n->cat_name;
        }

        $prod_is_active = array(
            '1' => 'Ano',
            '0' => 'Ne'
        );

        $this->addText('prod_name');
//                ->addRule($this::FILLED);
        $this->addSelect('category', 'kategorie', $name);
        $this->addText('prod_on_stock');
//                ->addRule($this::FILLED);
        $this->addCheckbox('prod_isnew');
        $this->addRadioList('prod_is_active', '', $prod_is_active)
                ->setDefaultValue('1');
        $this->addText('prod_price');
//                ->addRule($this::FILLED);
        $this->addText('prod_code');
//                ->addRule($this::FILLED);
        $this->addTextArea('prod_describe')
                ->addRule($this::FILLED)
                ->getControlPrototype()->class('mceEditor');
        $this->addTextArea('prod_long_describe')
                ->addRule($this::FILLED)
                ->getControlPrototype()->class('mceEditor');
        $this->addUpload('image', 'chyba');
        $this->addSubmit('save_change');
    }

}
