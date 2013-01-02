<?php

use Nette\Application\UI;

class personalForm extends UI\Form {

    function __construct() {
        parent::__construct();
        $this->addText('user_name', 'Jméno:')
                ->addRule($this::FILLED);
        $this->addText('user_surname', 'Příjmení:')
                ->addRule($this::FILLED);
        $this->addPassword('user_password', 'Heslo:');                
        $this->addPassword('user_confirmPassword', 'Ověření hesla:');
        $this->addText('user_telefon', 'Telefon:')
                ->addRule($this::FILLED);
        $this->addText('user_email', 'E-mail:')
                ->addRule($this::FILLED);
        $this->addText('user_street', 'Ulice a číslo:')
                ->addRule($this::FILLED);
        $this->addText('user_city', 'Město:')
                ->addRule($this::FILLED);
        $this->addText('user_psc', 'PSČ:')
                ->addRule($this::FILLED);
        $this->addText('user_firmName', 'Název firmy:');
        $this->addText('user_ico', 'IČO');
        $this->addText('user_dic', 'DIČ');
    }
}
