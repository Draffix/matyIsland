<?php

use Nette\Application\UI;

/**
 * Description of OrderPresenter
 *
 * @author Draffix
 */
class OrderPresenter extends BasePresenter {

    // pokud nic není v košíku nepovolíme přístup k objednávce
    // a přesměrujeme ho na úvodní stránku
    protected function startup() {
        parent::startup();
        if(!isset($_SESSION["cart"]) || $_SESSION["cart"]==null) {
            $this->redirect('Homepage:');
        }
    }

    protected function createComponentShippingForm() {
        $form = new UI\Form;
        $form->addText('name', 'Jméno:')
                ->addRule($form::FILLED, 'Je nutné zadat jméno.');

        $form->addText('surname', 'Příjmení:')
                ->addRule($form::FILLED, 'Je nutné zadat jméno.');
        $form->addText('telefon', 'Telefon:');
        $form->addText('email', 'E-mail:');
        $form->addText('street', 'Ulice a č. popisné:');
        $form->addText('city', 'Město:');
        $form->addText('psc', 'PSČ:');
        $form->addText('firmName', 'Název firmy:');
        $form->addText('ico', 'IČO:');
        $form->addText('dic', 'DIČ:');

        // billing formulář pro fakturaci
        $form->addText('bName', 'Jméno:');
        $form->addText('bSurname', 'Příjmení:');
        $form->addText('bTelefon', 'Telefon:');
        $form->addText('bEmail', 'E-mail:');
        $form->addText('bStreet', 'Ulice a č. popisné:');
        $form->addText('bCity', 'Město:');
        $form->addText('bPsc', 'PSČ:');
        $form->addText('bFirmName', 'Název firmy:');

        $form->addSubmit('continue', 'Pokračovat k výběru platby a dopravy');
        $form->onSuccess[] = callback($this, 'ShippingFormSubmitted');
        return $form;
    }

// volá se po úspěšném odeslání registrace
    public function ShippingFormSubmitted(UI\Form $form) {
        $values = $form->getValues();
        $this->flashMessage('Byl jsi úspěšně zaregistrován.');
        $this->redirect('Homepage:');
    }

}

?>
