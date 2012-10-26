<?php

use Nette\Application\UI;

/**
 * Description of OrderPresenter
 *
 * @author Draffix
 */
class OrderPresenter extends BasePresenter {

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
        $form->addSubmit('use', 'Použít');
        $form->onSuccess[] = callback($this, 'ShippingFormSubmitted');
        return $form;
    }

// volá se po úspěšném odeslání registrace
    public function ShippingFormSubmitted(UI\Form $form) {
        $values = $form->getValues();
        $this->flashMessage('Byl jsi úspěšně zaregistrován.');
        $this->redirect('Homepage:');
    }

    protected function createComponentBillingForm() {
        $form = new UI\Form;
        $form->addText('name', 'Jméno:');
        $form->addText('surname', 'Příjmení:');
        $form->addText('telefon', 'Telefon:');
        $form->addText('email', 'E-mail:');
        $form->addText('street', 'Ulice a č. popisné:');
        $form->addText('city', 'Město:');
        $form->addText('psc', 'PSČ:');
        $form->addText('firmName', 'Název firmy:');
        $form->onSuccess[] = callback($this, 'signUpFormSubmitted');
        return $form;
    }

// volá se po úspěšném odeslání registrace
    public function BillingFormSubmitted(UI\Form $form) {
        $values = $form->getValues();
        $this->flashMessage('Byl jsi úspěšně zaregistrován.');
        $this->redirect('Homepage:');
    }

}

?>
