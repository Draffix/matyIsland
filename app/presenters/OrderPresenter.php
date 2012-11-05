<?php

use Nette\Application\UI;
use Nette\Diagnostics\Debugger;

/**
 * Description of OrderPresenter
 *
 * @author Draffix
 */
class OrderPresenter extends BasePresenter {
    
    /** @var MatyIsland\OrderModel */
    private $order;

// pokud nic není v košíku nepovolíme přístup k objednávce
// a přesměrujeme ho do košíku, kde ho upozorníme, že v něm nic nemá
    protected function startup() {
        parent::startup();
        if (!isset($_SESSION["cart"]) || $_SESSION["cart"] == null) {
            $this->redirect('Basket:');
        }
        $this->order = $this->context->order;
    }

    protected function createComponentShippingForm() {
        $form = new UI\Form;
        $form->addText('cust_name', 'Jméno:')
                ->addRule($form::FILLED);
        $form->addText('cust_surname', 'Příjmení:')
                ->addRule($form::FILLED, 'Je nutné zadat jméno.');
        $form->addText('cust_telefon', 'Telefon:');
        $form->addText('cust_email', 'E-mail:');
        $form->addText('cust_street', 'Ulice a č. popisné:');
        $form->addText('cust_city', 'Město:');
        $form->addText('cust_psc', 'PSČ:');
        $form->addText('cust_firmName', 'Název firmy:');
        $form->addText('cust_ico', 'IČO:');
        $form->addText('cust_dic', 'DIČ:');

// billing formulář pro fakturaci
        $form->addText('cust_bName', 'Jméno:');
        $form->addText('cust_bSurname', 'Příjmení:');
        $form->addText('cust_bTelefon', 'Telefon:');
        $form->addText('cust_bEmail', 'E-mail:');
        $form->addText('cust_bStreet', 'Ulice a č. popisné:');
        $form->addText('cust_bCity', 'Město:');
        $form->addText('cust_bPsc', 'PSČ:');
        $form->addText('cust_bFirmName', 'Název firmy:');

        $form->addSubmit('continue', 'Pokračovat k výběru platby a dopravy');
        $form->onSuccess[] = $this->ShippingFormSubmitted;
        return $form;
    }

// volá se po úspěšném odeslání registrace
    public function ShippingFormSubmitted(UI\Form $form) {
        $values = $form->getValues();
        $_SESSION["order"] = $values;
        $this->redirect('Order:paymentDelivery');
    }

    protected function createComponentPaymentAndDeliveryForm() {
        $payment = array(
            'directDebit' => 'Převodem na účet (0,00 CZK)',
            'cash' => 'Hotově (0,00 CZK)',
            'cashOnDelivery' => 'Dobírkou (130,00 CZK) '
        );

        $delivery = array(
            'post' => 'Česká pošta (79,00 CZK)',
            'postWithCashOnDelivery' => 'Česká pošta-dobírkou (130,00 CZK)',
            'personalCollection' => 'Osobní převzetí (0,00 CZK)'
        );

        $form = new UI\Form;
        $form->addRadioList('payment', 'Placení:', $payment)
                ->addRule($form::FILLED);
        $form->addRadioList('delivery', 'Doprava:', $delivery)
                ->addRule($form::FILLED);
        $form->addSubmit('continue', 'Pokračovat k přehledu objednávky');
        $form->onSuccess[] = callback($this, 'paymentAndDeliveryFormSubmitted');
        return $form;
    }

    public function paymentAndDeliveryFormSubmitted(UI\Form $form) {
        $values = $form->getValues();
        $_SESSION["payment"] = $values->payment;
        $_SESSION["delivery"] = $values->delivery;
        array_push($_SESSION["cart"], $values->payment);
        $this->redirect('Order:summary');
    }

    protected function createComponentCommentForm() {
        $form = new UI\Form;
        $form->addTextArea('note', 'Vaše poznámka k objednávce:');
        $form->addSubmit('complete', 'Dokončit a potvrdit objednávku');
        $form->addCheckbox('agree', 'Souhlasím s podmínkami')
                ->addRule($form::FILLED);
        $form->onSuccess[] = callback($this, 'commentFormSubmitted');
        return $form;
    }

    public function commentFormSubmitted(UI\Form $form) {
        $values = $form->getValues();
        
        $this->order->saveOrder($_SESSION["values"], $_SESSION["payment"], $_SESSION["delivery"],$values->note);
        
        $this->redirect('Order:complete');
    }
}
