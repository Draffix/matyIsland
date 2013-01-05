<?php

namespace AdminModule;

use Nette\Application\UI;

class mainInfoForm extends UI\Form {

    function __construct() {
        parent::__construct();

        $ord_status = array(
            'Expedováno' => 'Expedováno',
            'Nevyřízeno' => 'Nevyřízeno',
            'Vyřízeno' => 'Vyřízeno',
            'Stornováno' => 'Stornováno',
            'Vráceno' => 'Vráceno',
        );

        $this->addSelect('ord_status', 'Stav objednávky:', $ord_status);
        $this->addText('cust_name', 'Jméno:');
        $this->addText('cust_surname', 'Příjmení:');
        $this->addText('cust_email', 'E-mail:');
        $this->addText('cust_telefon', 'Telefon:');

        $this->addText('cust_street', 'Ulice:');
        $this->addText('cust_city', 'Město:');
        $this->addText('cust_psc', 'PSČ:');
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
