<?php

namespace AdminModule;

use Nette\Application\UI\Form;

class editUserForm extends Form {

    function __construct() {
        parent::__construct();

        $this->addText('user_name', 'Jméno:')
                ->addRule($this::FILLED);
        $this->addText('user_surname', 'Příjmení:')
                ->addRule($this::FILLED);
        $this->addText('user_email', 'E-mail:')
                ->addRule($this::FILLED);
        $this->addText('user_telefon', 'Telefon:')
                ->addRule($this::FILLED);

        $this->addText('user_street', 'Ulice:')
                ->addRule($this::FILLED);
        $this->addText('user_city', 'Město:')
                ->addRule($this::FILLED);
        $this->addText('user_psc', 'PSČ:')
                ->addRule($this::FILLED);
        $this->addText('user_firmName', 'Firma:');
        $this->addText('user_ico', 'IČO:');
        $this->addText('user_dic', 'DIČ:');

        $this->addSubmit('save_change');
    }

}