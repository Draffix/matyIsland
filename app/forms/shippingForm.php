<?php

use Nette\Application\UI;

class shippingForm extends UI\Form {

    function __construct() {
        parent::__construct();

        $cust_name = '';
        $cust_surname = '';
        $cust_telefon = '';
        $cust_email = '';
        $cust_street = '';
        $cust_city = '';
        $cust_psc = '';
        $cust_firmName = '';
        $cust_ico = '';
        $cust_dic = '';

        $cust_bname = '';
        $cust_bsurnname = '';
        $cust_bstreet = '';
        $cust_bcity = '';
        $cust_bpsc = '';

        if (isset($_SESSION["order"]["cust_name"])) {
            $cust_name = $_SESSION["order"]["cust_name"];
        }

        if (isset($_SESSION["order"]["cust_surname"])) {
            $cust_surname = $_SESSION["order"]["cust_surname"];
        }

        if (isset($_SESSION["order"]["cust_telefon"])) {
            $cust_telefon = $_SESSION["order"]["cust_telefon"];
        }

        if (isset($_SESSION["order"]["cust_email"])) {
            $cust_email = $_SESSION["order"]["cust_email"];
        }

        if (isset($_SESSION["order"]["cust_street"])) {
            $cust_street = $_SESSION["order"]["cust_street"];
        }

        if (isset($_SESSION["order"]["cust_city"])) {
            $cust_city = $_SESSION["order"]["cust_city"];
        }

        if (isset($_SESSION["order"]["cust_psc"])) {
            $cust_psc = $_SESSION["order"]["cust_psc"];
        }

        if (isset($_SESSION["order"]["cust_firmName"])) {
            $cust_firmName = $_SESSION["order"]["cust_firmName"];
        }

        if (isset($_SESSION["order"]["cust_ico"])) {
            $cust_ico = $_SESSION["order"]["cust_ico"];
        }

        if (isset($_SESSION["order"]["cust_dic"])) {
            $cust_dic = $_SESSION["order"]["cust_dic"];
        }

        if (isset($_SESSION["order"]["cust_bName"])) {
            $cust_bname = $_SESSION["order"]["cust_bName"];
        }

        if (isset($_SESSION["order"]["cust_bSurname"])) {
            $cust_bsurnname = $_SESSION["order"]["cust_bSurname"];
        }

        if (isset($_SESSION["order"]["cust_bStreet"])) {
            $cust_bstreet = $_SESSION["order"]["cust_bStreet"];
        }

        if (isset($_SESSION["order"]["cust_bCity"])) {
            $cust_bcity = $_SESSION["order"]["cust_bCity"];
        }

        if (isset($_SESSION["order"]["cust_bPsc"])) {
            $cust_bpsc = $_SESSION["order"]["cust_bPsc"];
        }

        $this->addText('cust_name', 'Jméno: *')
                ->setDefaultValue($cust_name)
                ->addRule($this::FILLED)
                ->setAttribute('tabindex', '1');
        $this->addText('cust_surname', 'Příjmení: *')
                ->setDefaultValue($cust_surname)
                ->addRule($this::FILLED)
                ->setAttribute('tabindex', '2');
        $this->addText('cust_firmName', 'Název firmy:')
                ->setDefaultValue($cust_firmName)
                ->setAttribute('tabindex', '3');
        $this->addText('cust_email', 'E-mail: *')
                ->setDefaultValue($cust_email)
                ->addRule($this::FILLED)
                ->setAttribute('tabindex', '4');
        $this->addText('cust_telefon', 'Telefon: *')
                ->setDefaultValue($cust_telefon)
                ->addRule($this::FILLED)
                ->setAttribute('tabindex', '5');
        $this->addText('cust_street', 'Ulice a č. popisné: *')
                ->setDefaultValue($cust_street)
                ->addRule($this::FILLED)
                ->setAttribute('tabindex', '6');
        $this->addText('cust_city', 'Město: *')
                ->setDefaultValue($cust_city)
                ->addRule($this::FILLED)
                ->setAttribute('tabindex', '7');
        $this->addText('cust_psc', 'PSČ: *')
                ->setDefaultValue($cust_psc)
                ->addRule($this::FILLED)
                ->setAttribute('tabindex', '8');
        $this->addText('cust_ico', 'IČO:')
                ->setDefaultValue($cust_ico)
                ->setAttribute('tabindex', '9');
        $this->addText('cust_dic', 'DIČ:')
                ->setDefaultValue($cust_dic);
        ;

        // billing formulář pro fakturaci
        $this->addText('cust_bName', 'Jméno:')
                ->setDefaultValue($cust_bname);
        $this->addText('cust_bSurname', 'Příjmení:')
                ->setDefaultValue($cust_bsurnname);
        $this->addText('cust_bStreet', 'Ulice a č. popisné:')
                ->setDefaultValue($cust_bstreet);
        $this->addText('cust_bCity', 'Město:')
                ->setDefaultValue($cust_bcity);
        $this->addText('cust_bPsc', 'PSČ:')
                ->setDefaultValue($cust_bpsc);

        $this->addCheckbox('isGift')
                ->setAttribute('tabindex', '10');

        $this->addSubmit('continue')
                ->setAttribute('tabindex', '11');
    }

}
