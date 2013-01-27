<?php

namespace AdminModule;

use Nette\Application\UI\Form;

class editUserForm extends Form {

    function __construct() {
        parent::__construct();

        $this->addText('user_name', 'Jméno:');
        $this->addText('user_surname', 'Příjmení:');
        $this->addText('user_email', 'E-mail:');
        $this->addText('user_telefon', 'Telefon:');

        $this->addText('user_street', 'Ulice:');
        $this->addText('user_city', 'Město:');
        $this->addText('user_psc', 'PSČ:');
        $this->addText('user_firmName', 'Firma:');
        $this->addText('user_ico', 'IČO:');
        $this->addText('user_dic', 'DIČ:');

        $this->addSubmit('save_change');
    }

}