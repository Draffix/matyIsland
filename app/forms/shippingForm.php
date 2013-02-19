<?php

use Nette\Application\UI;

class shippingForm extends UI\Form {

    /**
     * @var UserModel
     */
    protected $users;

    function __construct(UserModel $users, $id = NULL) {
        parent::__construct();
        $this->users = $users;

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
        $cust_bsurname = '';
        $cust_bstreet = '';
        $cust_bcity = '';
        $cust_bpsc = '';

        if ($id != NULL) {
            $userInfo = $this->users->fetchUser($id);

            $cust_name = $userInfo->user_name;
            $cust_surname = $userInfo->user_surname;
            $cust_telefon = $userInfo->user_telefon;
            $cust_email = $userInfo->user_email;
            $cust_street = $userInfo->user_street;
            $cust_city = $userInfo->user_city;
            $cust_psc = $userInfo->user_psc;
            $cust_firmName = $userInfo->user_firmName;
            $cust_ico = $userInfo->user_ico;
            $cust_dic = $userInfo->user_dic;
        } else {

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
                $cust_bsurname = $_SESSION["order"]["cust_bSurname"];
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
        }

        $this->addText('cust_name', 'Jméno: *')
                ->setDefaultValue($cust_name)
                ->addRule($this::FILLED)
                ->setAttribute('tabindex', '1')
                ->setAttribute('title', 'Položka musí být vyplněna');
        $this->addText('cust_surname', 'Příjmení: *')
                ->setDefaultValue($cust_surname)
                ->addRule($this::FILLED)
                ->setAttribute('tabindex', '2')
                ->setAttribute('title', 'Položka musí být vyplněna');
        $this->addText('cust_firmName', 'Název firmy:')
                ->setDefaultValue($cust_firmName)
                ->setAttribute('tabindex', '3');
        $this->addText('cust_email', 'E-mail: *')
                ->setDefaultValue($cust_email)
                ->addRule($this::FILLED)
                ->setAttribute('tabindex', '4')
                ->setAttribute('title', 'Položka musí být vyplněna');
        $this->addText('cust_telefon', 'Telefon: *')
                ->setDefaultValue($cust_telefon)
                ->addRule($this::FILLED)
                ->setAttribute('tabindex', '5')
                ->setAttribute('title', 'Položka musí být vyplněna');
        $this->addText('cust_street', 'Ulice a č. popisné: *')
                ->setDefaultValue($cust_street)
                ->addRule($this::FILLED)
                ->setAttribute('tabindex', '6')
                ->setAttribute('title', 'Položka musí být vyplněna');
        $this->addText('cust_city', 'Město: *')
                ->setDefaultValue($cust_city)
                ->addRule($this::FILLED)
                ->setAttribute('tabindex', '7')
                ->setAttribute('title', 'Položka musí být vyplněna');
        $this->addText('cust_psc', 'PSČ: *')
                ->setDefaultValue($cust_psc)
                ->addRule($this::FILLED)
                ->setAttribute('tabindex', '8')
                ->setAttribute('title', 'Položka musí být vyplněna');
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
                ->setDefaultValue($cust_bsurname);
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
