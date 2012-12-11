<?php

use Nette\Application\UI;
use Nette\DateTime;
use Nette\Mail\Message;

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
        $this->order = $this->context->order;
    }

    public function renderDefault() {
        if (!isset($_SESSION["cart"]) || $_SESSION["cart"] == null) {
            $this->redirect('Basket:');
        }
    }

    public function renderPaymentDelivery() {
        if (!isset($_SESSION["cart"]) || $_SESSION["cart"] == null) {
            $this->redirect('Basket:');
        }
    }

    public function renderSummary() {
        if (!isset($_SESSION["cart"]) || $_SESSION["cart"] == null) {
            $this->redirect('Basket:');
        }
    }

    public function renderComplete() {
        if ($_SESSION["totalPrice"] == 0) {
            $this->redirect('Homepage:');
        }

        $date = Date("j.m.Y", Time());

        $this->template->orderID = $_SESSION["order_id"];
        $this->template->date = $date;

        $this->template->order = $this->order->fetchOrder($_SESSION["order_id"]);
        $this->template->totalPrice = $_SESSION["totalPrice"];
        $this->template->deliveryPrice = $_SESSION["order"]["deliveryPrice"];
        $this->template->total = $_SESSION["totalPrice"] + $_SESSION["order"]["deliveryPrice"];

        $_SESSION["totalPrice"] = 0;
        $_SESSION["count"] = 0;
        unset($_SESSION["cart"], $_SESSION["order"]);
        unset($_SESSION["order_id"]);
    }

    protected function createComponentShippingForm() {
        $form = new UI\Form;
        $form->addText('cust_name', 'Jméno: *')
                ->addRule($form::FILLED);
        $form->addText('cust_surname', 'Příjmení: *')
                ->addRule($form::FILLED);
        $form->addText('cust_telefon', 'Telefon: *');
        $form->addText('cust_email', 'E-mail: *');
        $form->addText('cust_street', 'Ulice a č. popisné: *');
        $form->addText('cust_city', 'Město: *');
        $form->addText('cust_psc', 'PSČ: *');
        $form->addText('cust_firmName', 'Název firmy:');
        $form->addText('cust_ico', 'IČO:');
        $form->addText('cust_dic', 'DIČ:');

        // billing formulář pro fakturaci
        $form->addText('cust_bName', 'Jméno:');
        $form->addText('cust_bSurname', 'Příjmení:');
        $form->addText('cust_bStreet', 'Ulice a č. popisné:');
        $form->addText('cust_bCity', 'Město:');
        $form->addText('cust_bPsc', 'PSČ:');

        $form->addCheckbox('isGift');

        $form->addSubmit('continue');
        $form->onSuccess[] = $this->ShippingFormSubmitted;
        return $form;
    }

    // volá se po úspěšném odeslání registrace
    public function ShippingFormSubmitted(UI\Form $form) {
        $values = $form->getValues(TRUE); // array      
        $_SESSION["order"] = $values;

        // check if the billing form was filled
        if ($_SESSION["order"]["cust_bName"] == "") {
            $_SESSION["order"]["cust_bName"] = $_SESSION["order"]["cust_name"];
        }
        if ($_SESSION["order"]["cust_bSurname"] == "") {
            $_SESSION["order"]["cust_bSurname"] = $_SESSION["order"]["cust_surname"];
        }
        if ($_SESSION["order"]["cust_bStreet"] == "") {
            $_SESSION["order"]["cust_bStreet"] = $_SESSION["order"]["cust_street"];
        }
        if ($_SESSION["order"]["cust_bCity"] == "") {
            $_SESSION["order"]["cust_bCity"] = $_SESSION["order"]["cust_city"];
        }
        if ($_SESSION["order"]["cust_bPsc"] == "") {
            $_SESSION["order"]["cust_bPsc"] = $_SESSION["order"]["cust_psc"];
        }

        // change from TRUE to 1 due to MySQL
        if ($_SESSION["order"]["isGift"] == "TRUE") {
            $_SESSION["order"]["isGift"] = 1;
        } else {
            $_SESSION["order"]["isGift"] = 0;
        }

        $this->redirect('Order:paymentDelivery');
    }

    protected function createComponentPaymentAndDeliveryForm() {
        $payment = array(
            'directDebit' => 'Bankovní převod',
            'cash' => 'Hotově',
            'cashOnDelivery' => 'Dobírka (+ 30 Kč)'
        );

        $delivery = array(
            'post' => 'Česká pošta - balík do ruky, doručení do 1-2 pracovní dnů - 89 Kč',
            'postWithCashOnDelivery' => 'Kurýr DPD - dodání do 1-2 pracovních dnů - 89 Kč',
            'personalCollection' => 'Osobní převzetí - 0 Kč'
        );

        $form = new UI\Form;
        $form->addRadioList('cust_payment', 'Placení:', $payment)
                ->addRule($form::FILLED)
                ->setHtmlId('payment')
                ->setAttribute('onchange', 'toggleStatus()');
        $form->addRadioList('cust_delivery', 'Doprava:', $delivery)
                ->addRule($form::FILLED)
                ->setHtmlId('delivery')
                ->setAttribute('onchange', 'toggleStatus()');

        $form->addSubmit('continue', 'Pokračovat k přehledu objednávky');
        $form->onSuccess[] = callback($this, 'paymentAndDeliveryFormSubmitted');
        return $form;
    }

    public function paymentAndDeliveryFormSubmitted(UI\Form $form) {
        $values = $form->getValues(TRUE);
        $_SESSION["order"] = array_merge($_SESSION["order"], $values);

        switch ($_SESSION["order"]["cust_delivery"]) {
            case "post":
                $_SESSION["order"]["deliveryPrice"] = 89;
                break;
            case "postWithCashOnDelivery":
                $_SESSION["order"]["deliveryPrice"] = 89;
                break;
            case "personalCollection":
                $_SESSION["order"]["deliveryPrice"] = 0;
                break;
            default:
                break;
        }

        if ($_SESSION["order"]["cust_payment"] == "cashOnDelivery") {
            $_SESSION["order"]["deliveryPrice"] += 30;
        }

        $this->redirect('Order:summary');
    }

    protected function createComponentUpdateForm() {
        $form = new UI\Form;
        $form->addText('quantity', 'Množství');
        $form->addHidden('product_id');
        $form->addSubmit('update', 'Upravit');
        $form->onSuccess[] = callback($this, 'updateFormSubmitted');
        return $form;
    }

    protected function createComponentCommentForm() {
        $form = new UI\Form;
        $form->addTextArea('cust_note', 'Vaše poznámka k objednávce:');
        $form->addSubmit('complete', 'Dokončit a potvrdit objednávku');
        $form->addCheckbox('agree', 'Souhlasím s podmínkami')
                ->addRule($form::FILLED);
        $form->onSuccess[] = callback($this, 'commentFormSubmitted');
        return $form;
    }

    public function commentFormSubmitted(UI\Form $form) {
        $values = $form->getValues(TRUE);

        unset($values["agree"]); // odstraníme z pole zbytečný prvek checkbox
        $dateTime = array("ord_date" => new DateTime); // zjistíme aktuální čas a datum

        $_SESSION["order"] = array_merge($_SESSION["order"], $dateTime, $values); // doplníme o nové údaje

        $orderID = $this->order->saveOrder($_SESSION["order"]); //vrací ID vložené objednávky

        $_SESSION["order_id"] = $orderID;

        foreach ($_SESSION["cart"] as $key => $value) {
            $this->order->saveIntoOrderHasProduct(
                    $orderID, $value->prod_id, $value->basket_quantity, $value->prod_price);
        }

        $template = $this->createTemplate();
        $template->setFile(__DIR__ . '/../templates/Order/orderEmail.latte');
        $template->registerFilter(new Nette\Latte\Engine);
        $template->orderId = $orderID;

//        $mail = new Message;
//        $mail->setFrom('MatyLand.cz <info@matyland.com>')
//                ->addTo('jerry.klimcik@gmail.com')
//                ->setSubject('Potvrzení objednávky')
//                ->setHtmlBody($template)
//                ->send();

        $this->redirect('Order:complete');
    }

}