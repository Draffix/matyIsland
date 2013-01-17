<?php

namespace AdminModule;

use Nette\Application\UI;

class addProductIntoOrderForm extends UI\Form {

    function __construct() {
        parent::__construct();

        $this->addText('prod_name');
        $this->addText('quantity');
        $this->addText('prod_price');

        $this->addSubmit('save_change');
    }

}
