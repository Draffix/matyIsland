<?php

namespace AdminModule;

use Nette\Application\UI;

class editOrderForm extends UI\Form {

    function __construct() {
        parent::__construct();

        $this->addText('cust_name', 'Jméno:')
                ->addRule($this::FILLED);
        $this->addText('cust_surname', 'Příjmení:')
                ->addRule($this::FILLED);
        $this->addText('cust_email', 'E-mail:')
                ->addRule($this::FILLED);
        $this->addText('cust_telefon', 'Telefon:')
                ->addRule($this::FILLED);

        $this->addText('cust_street', 'Ulice:')
                ->addRule($this::FILLED);
        $this->addText('cust_city', 'Město:')
                ->addRule($this::FILLED);
        $this->addText('cust_psc', 'PSČ:')
                ->addRule($this::FILLED);
        $this->addText('cust_firmName', 'Firma:');
        $this->addText('cust_ico', 'IČO:');
        $this->addText('cust_dic', 'DIČ:');

        $this->addText('cust_bname', 'Jméno:');
        $this->addText('cust_bsurname', 'Příjmení:');
        $this->addText('cust_bemail', 'E-mail:');
        $this->addText('cust_btelefon', 'Telefon:');

        $this->addText('cust_bstreet', 'Ulice:');
        $this->addText('cust_bcity', 'Město:');
        $this->addText('cust_bpsc', 'PSČ:');
        $this->addText('cust_bfirmName', 'Firma:');

        $this->addTextArea('cust_note', 'Poznámka uživatele:');
        $this->addTextArea('seller_note', 'Poznámka prodejce:');

        $this->addSubmit('save_change');
    }

}
