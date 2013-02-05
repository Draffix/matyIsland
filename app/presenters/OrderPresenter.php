<?php

use Nette\Application\UI;
use Nette\DateTime;
use Nette\Mail\Message;
use Nette\Utils\Html;
use Nette\Utils\Validators;

/**
 * Description of OrderPresenter
 *
 * @author Draffix
 */
class OrderPresenter extends BasePresenter {

    /** @var OrderModel */
    private $order;

    /**
     * @var ProductModel
     */
    protected $products;

    // pokud nic není v košíku nepovolíme přístup k objednávce
    // a přesměrujeme ho do košíku, kde ho upozorníme, že v něm nic nemá
    protected function startup() {
        parent::startup();
        $this->order = $this->context->order;
        $this->products = $this->context->product;
    }

    public function renderDefault() {
        if (!isset($_SESSION["cart"]) || $_SESSION["cart"] == null) {
            $this->redirect('Basket:');
        }
        $this->checkProducts();
    }

    public function renderPaymentDelivery() {
        if (!isset($_SESSION["cart"]) || $_SESSION["cart"] == null) {
            $this->redirect('Basket:');
        }
        $this->checkProducts();
    }

    public function renderSummary() {
        if (!isset($_SESSION["cart"]) || $_SESSION["cart"] == null) {
            $this->redirect('Basket:');
        }
        $this->checkProducts();
    }

    private function checkProducts() {
        // v případě, že byl právě prodán poslední kus
        foreach (array_keys($_SESSION['cart']) as $key) {
            if ($this->products->countProductQuantity($key)->pocet == 0) {
                $name = $_SESSION['cart'][$key]['prod_name']; //zjistíme si jméno produktu
                $el = Html::el('span', 'Omlouváme se, ale zboží "' . $name . '", které si hodláte koupit,
                    právě někdo koupil a jednalo se o poslední kus. V případě zájmu o produkt nás
                    můžete ');
                $el2 = Html::el('a', 'kontaktovat.')->href($this->link('Info:contact')); //vyvoříme odkaz
                $el->add($el2); // spojíme dvě zprávy
                $this->flashMessage($el, 'wrong');

                // odečteme celkovou cenu a množství v košíku
                $_SESSION["totalPrice"] -= ($_SESSION["cart"][$key]["basket_quantity"] * $this->basket->findPrice($key)->price);
                $_SESSION["count"] -= $_SESSION["cart"][$key]["basket_quantity"];
                unset($_SESSION["cart"][$key]);

                if ($this->getUser()->isLoggedIn()) {
                    $this->basket->dropItemFromBasket($key, $this->getUser()->getId());
                }
                $this->redirect('Basket:');
            }
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
        if ($this->getUser()->isLoggedIn()) {
            $form = new shippingForm($this->users, $this->getUser()->getId());
        } else {
            $form = new shippingForm($this->users);
        }
        $form->onSuccess[] = $this->ShippingFormSubmitted;
        return $form;
    }

    // volá se po úspěšném odeslání registrace
    public function ShippingFormSubmitted(UI\Form $form) {
        $values = $form->getValues(TRUE); // array 

        if ($values['cust_firmName'] != '' && $values['cust_ico'] == '') {
            $this->flashMessage('Protože byla vyplněna i firma, musí být vyplněno i firemní IČO', 'wrong');
            return;
        }

        if ($values['cust_ico'] != '' && !Validators::is($values['cust_ico'], 'string:8')) {
            $this->flashMessage('Litujeme, ale IČO musí obsahovat osm znaků.', 'wrong');
            return;
        }

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
        //výpis všech typů placení
        $payment = array();
        foreach ($this->deliveryPayment->fetchAllEnabledPayment() as $key => $p) {
            $payment[$key] = $p->payment_name . ' - ' . $p->payment_describe . ' - ' . $p->payment_price . ' Kč';
        }

        //výpis všech typů dopravy
        $delivery = array();
        foreach ($this->deliveryPayment->fetchAllEnabledDelivery() as $key => $d) {
            $delivery[$key] = $d->delivery_name . ' - ' . $d->delivery_describe . ' - ' . $d->delivery_price . ' Kč';
        }

        $form = new UI\Form;
        $form->addRadioList('payment_payment_id', 'Placení:', $payment)
                ->addRule($form::FILLED)
                ->setHtmlId('payment')
                ->setAttribute('onchange', 'toggleStatus()');
        $form->addRadioList('delivery_delivery_id', 'Doprava:', $delivery)
                ->addRule($form::FILLED)
                ->setHtmlId('delivery')
                ->setAttribute('onchange', 'toggleStatus()');

        $form->addSubmit('continue', 'Pokračovat k přehledu objednávky');
        $form->onSuccess[] = callback($this, 'paymentAndDeliveryFormSubmitted');
        return $form;
    }

    public function paymentAndDeliveryFormSubmitted(UI\Form $form) {
        $values = $form->getValues(TRUE);

        if ($values['delivery_delivery_id'] == 1 && $values['payment_payment_id'] == 2 ||
                $values['delivery_delivery_id'] == 2 && $values['payment_payment_id'] == 2 ||
                $values['delivery_delivery_id'] == 2 && $values['payment_payment_id'] == 3 ||
                $values['delivery_delivery_id'] == 3 && $values['payment_payment_id'] == 3) {
            $this->flashMessage('Tuto kombinaci bohužel nejde zvolit ', 'wrong');
            return;
        }

        $_SESSION["order"] = array_merge($_SESSION["order"], $values);

        $_SESSION["order"]["deliveryPrice"] = $this->deliveryPayment->fetchPaymentType($values['payment_payment_id'])->payment_price
                + $this->deliveryPayment->fetchDeliveryType($values['delivery_delivery_id'])->delivery_price;

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
        $orderID = $this->order->saveOrder($_SESSION["order"]); //uloží a vrací ID vložené objednávky

        $_SESSION["order_id"] = $orderID;
        foreach ($_SESSION["cart"] as $key => $value) {
            $this->order->saveIntoOrderHasProduct(
                    $orderID, $value->prod_id, $value->basket_quantity, $value->prod_price); //svážeme produkty s objednávkou
            $this->products->updateProductQuantity($value->prod_id, $this->products->countProductQuantity($value->prod_id)->pocet - $value->basket_quantity); //snížíme množství produktů na skladě
        }

        // vytvoření emailové šablony
        $template = $this->createTemplate();
        $template->setFile(__DIR__ . '/../templates/Order/orderEmail.latte');
        $template->registerFilter(new Nette\Latte\Engine);

        //vložení proměnných do šablony
        $template->orderId = $orderID;
        $template->website = $_SERVER['SERVER_NAME'];
        $template->order = $this->order->fetchOrder($orderID)->fetch();
        $template->orderProducts = $this->order->fetchAllOrdersWithID($orderID);

        // jméno a cena zvoleného doručení
        $template->deliveryName = $this->deliveryPayment->fetchNamePriceOfDeliveryAndPayment($orderID)->delivery_name;
        $template->deliveryPrice = $this->deliveryPayment->fetchNamePriceOfDeliveryAndPayment($orderID)->delivery_price;

        // jméno a cena zvoleného placení
        $template->paymentName = $this->deliveryPayment->fetchNamePriceOfDeliveryAndPayment($orderID)->payment_name;
        $template->paymentPrice = $this->deliveryPayment->fetchNamePriceOfDeliveryAndPayment($orderID)->payment_price;

        $totalPrice = 0;
        foreach ($this->order->fetchAllOrdersWithID($orderID) as $o) {
            $totalPrice += ($o->quantity * $o->actual_price_of_product);
        }
        $totalPrice += $this->order->fetchAllOrdersWithID($orderID)->fetch()->deliveryPrice;
        $template->totalPrice = $totalPrice;


        $mail = new Message;
        $mail->setFrom('MatyLand.cz <info@matyland.com>')
                ->addTo($_SESSION["order"]["cust_email"])
                ->setSubject('Potvrzení objednávky')
                ->setHtmlBody($template)
                ->send();

        $this->redirect('Order:complete');
    }

}