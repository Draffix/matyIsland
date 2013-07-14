<?php
/**
 * User: Jaroslav Klimčík
 * Date: 13.7.13
 * Time: 23:55
 */

namespace AdminModule;

use Nette\Application\UI\Form;

class editSupplierForm extends Form {

    function __construct() {
        parent::__construct();

        $this->addText('sup_name')
            ->addRule($this::FILLED);
        $this->addText('sup_address');
        $this->addText('sup_city');
        $this->addText('sup_psc');
        $this->addText('sup_telefon');
        $this->addText('sup_mobil');
        $this->addText('sup_email');
        $this->addText('sup_website');
        $this->addText('sup_ico');
        $this->addText('sup_dic');
        $this->addTextArea('sup_describe');
        $this->addText('sup_contact_person');

        $this->addSubmit('save_change');
    }

}