<?php

namespace AdminModule;

use Nette\Application\UI;
use Nette\Mail\Message;
use \DateTime;
use OndrejBrejla\Eciovni\Eciovni;
use OndrejBrejla\Eciovni\ParticipantBuilder;
use OndrejBrejla\Eciovni\ItemImpl;
use OndrejBrejla\Eciovni\DataBuilder;
use OndrejBrejla\Eciovni\TaxImpl;

class OrderPresenter extends BasePresenter {

    /** @persistent */
    public $id;

    // změní status objednávky
    public function handleChangeStatus($id, $status) {
        $this->order->updateOrderStatus($id, $status);

        $this->flashMessage('Status byl změněn', 'success');
        $this->redirect('this');
    }

    // odstraní produkt z objednávky
    public function handleRemoveProductIntoOrder($product_id, $order_id) {
        if ($this->order->countOfSingleOrderHasProduct($order_id)->pocet == 1) {
            $this->flashMessage('V objednávce musí zůstat alespoň jeden produkt', 'error');
        } else {
            $this->order->deleteIntoOrderHasProduct($order_id, $product_id);
            $this->flashMessage('Produkt úspěšně smazán', 'success');
        }
        $this->redirect('this');
    }

    // odstraní produkt z nově vytvořené objednávky
    public function handleRemoveProductIntoNewOrder($product_id) {
        if (count($_SESSION['order']) == 1) {
            unset($_SESSION["orderTotal"], $_SESSION['order']);
        } else {
            $_SESSION["orderTotal"] -= ($_SESSION["order"][$product_id]["order_quantity"] * $this->product->findPrice($product_id)->price);
            unset($_SESSION["order"][$product_id]);
        }

        if ($this->isAjax()) {
            $this->invalidateControl('products');
        } else {
            $this->redirect('this');
        }
    }

    // odstraní objednávku
    public function handleDeleteOrder($ord_id) {
        $this->order->deleteOrder($ord_id);
        $this->flashMessage('Objednávka byla smazána', 'success');
        $this->redirect('this');
    }

    // akce pro generování PDF
    public function actionGenerate() {
        include_once(LIBS_DIR . '/MPDF54/mpdf.php');
        $mpdf = new \mPDF('utf-8');

        // Exportování připravené faktury do PDF.
        // Pro uložení faktury do souboru použijte druhý a třetí parametr, stejně jak je popsáno v dokumentaci k mPDF->Output().
        $this['generatePdf']->exportToPdf($mpdf);
    }

    public function renderDefault() {
        $this->template->orders = $this->order->fetchAllOrders();
    }

    public function renderDetail($id) {
        $this->template->order = $this->order->fetchOrder($id)->fetch();
        $this->template->orderProducts = $this->order->fetchAllOrdersWithID($id);
        $this->template->orderEditProducts = $this->order->fetchAllOrdersWithID($id);

        //pro zjištění názvu produktu a jeho ceny v přidání nové položky
        $this->template->productList = $this->product->fetchAllProducts();

        $this->template->orderAddProducts = $this->order->fetchAllOrdersWithID($id);

        // jméno a cena zvoleného doručení
        $this->template->deliveryName = $this->deliveryPayment->fetchNamePriceOfDeliveryAndPayment($id)->delivery_name;
        $this->template->deliveryPrice = $this->deliveryPayment->fetchNamePriceOfDeliveryAndPayment($id)->delivery_price;

        // jméno a cena zvoleného placení
        $this->template->paymentName = $this->deliveryPayment->fetchNamePriceOfDeliveryAndPayment($id)->payment_name;
        $this->template->paymentPrice = $this->deliveryPayment->fetchNamePriceOfDeliveryAndPayment($id)->payment_price;

        $totalPrice = 0;
        foreach ($this->order->fetchAllOrdersWithID($id) as $o) {
            $totalPrice += ($o->quantity * $o->actual_price_of_product);
        }
        $totalPrice += $this->order->fetchAllOrdersWithID($id)->fetch()->deliveryPrice;
        $this->template->totalPrice = $totalPrice;
    }

    public function renderAddOrder() {
        //pro zjištění názvu produktu a jeho ceny v přidání nové položky
        $this->template->productList = $this->product->fetchAllProducts();

        if (!isset($_SESSION["orderTotal"])) {
            $_SESSION["orderTotal"] = 0;
        }

        if (isset($_SESSION['deliveryName'])) {
            $this->template->deliveryName = $_SESSION['deliveryName'];
            $this->template->deliveryPrice = $_SESSION['deliveryPrice'];
            $this->template->paymentName = $_SESSION['paymentName'];
            $this->template->paymentPrice = $_SESSION['paymentPrice'];
        }
    }

    // upraví údaje o zákazníkovi v objednávce
    protected function createComponentEditOrderForm() {
        $form = new editOrderForm();
        $form->onSuccess[] = callback($this, 'editOrderFormSubmitted');
        return $form;
    }

    public function editOrderFormSubmitted(UI\Form $form) {
        $values = $form->getValues();
        $this->order->updateOrder($this->getParameter('id'), $values);

        $this->flashMessage('Objednávka byla uložena', 'success');
        $this->redirect('this');
    }

    // upraví produkt v objednávce
    public function createComponentEditProductIntoOrderForm() {
        $form = new UI\Form();
        $quantity = $form->addContainer('quantity');
        foreach ($this->order->fetchAllOrdersWithID($this->getParameter('id')) as $val) {
            $quantity->addText($val->prod_id)
                    ->setType('number')
                    ->setAttribute('class', 'input-mini')
                    ->setValue($val->quantity);
        }
        $form->onSuccess[] = callback($this, 'editProductIntoOrderFormSubmitted');
        return $form;
    }

    public function editProductIntoOrderFormSubmitted(UI\Form $form) {
        $values = $form->getValues();
        foreach ($values->quantity as $prod_id => $quantity) {
            $this->order->updateOrderHasProduct($this->getParameter('id'), $prod_id, $quantity);
        }
        $this->flashMessage('Položky byly upraveny', 'success');
        $this->redirect('this');
    }

    // přidá produkt do objednávky
    public function createComponentAddProductIntoOrderForm() {
        $form = new addProductIntoOrderForm();
        $form->onSuccess[] = callback($this, 'addProductIntoOrderFormSubmitted');
        return $form;
    }

    public function addProductIntoOrderFormSubmitted(UI\Form $form) {
        $values = $form->getValues();

        if (isset($this->product->findProductsID($values->prod_name)->prod_id)) {
            $productID = $this->product->findProductsID($values->prod_name)->prod_id; //podle jména zjistíme ID produktu
            if ($this->order->countOfOrderHasProduct($this->getParameter('id'), $productID)->pocet == 0) { //zda už není v objednávce
                $this->order->saveIntoOrderHasProduct($this->getParameter('id'), $productID, $values->quantity, $values->prod_price);
                $this->flashMessage('Položka byla přidána do objednávky', 'success');
            } else {
                $this->flashMessage('Produkt již v seznamu položek existuje', 'error');
            }
        } else {
            $this->flashMessage('Položka nebyla přidána do objednávky, protože neexistuje', 'error');
        }
        $this->redirect('this');
    }

    // pošle zákazníkovi email
    protected function createComponentSendEmailForm() {
        $form = new UI\Form;
        $form->addText('subject');
        $form->addText('receiver');
        $form->addTextArea('message')
                ->getControlPrototype()->class('mceEditor');
        $form->addSubmit('send', 'Odeslat');
        $form->onSuccess[] = callback($this, 'sendEmailFormSubmitted');
        return $form;
    }

    public function sendEmailFormSubmitted(UI\Form $form) {
        $values = $form->getValues();

        $mail = new Message;
        $mail->setFrom('MatyLand.cz <info@matyland.com>')
                ->addTo($values->receiver)
                ->setSubject($values->subject)
                ->setHtmlBody($values['message'])
                ->send();

        $this->flashMessage('Email byl odeslán', 'success');
        $this->redirect('this');
    }

    // změní typ dopravy nebo placení v objednávce
    public function createComponentEditDeliveryPaymentForm() {
        $form = new UI\Form();

        // výpis všech typů doručení
        $delivery = array();
        foreach ($this->deliveryPayment->fetchAllDelivery() as $key => $d) {
            $delivery[$key] = $d->delivery_name . ' - ' . $d->delivery_price . ',-Kč';
        }
        // zjistíme zvolenou dopravu
        $selectedDelivery = $this->order->fetchOrder($this->getParameter('id'))->fetch()->delivery_delivery_id;

        // výpis všech typů placení
        $payment = array();
        foreach ($this->deliveryPayment->fetchAllPayment() as $key => $p) {
            $payment[$key] = $p->payment_name . ' - ' . $p->payment_price . ',-Kč';
        }
        // zjistíme zvolené placení
        $selectedPayment = $this->order->fetchOrder($this->getParameter('id'))->fetch()->payment_payment_id;

        $form->addSelect('delivery_delivery_id', 'Doprava', $delivery)
                ->setAttribute('data-rel', 'chosen')
                ->setDefaultValue($selectedDelivery);
        $form->addSelect('payment_payment_id', 'Placení', $payment)
                ->setAttribute('data-rel', 'chosen')
                ->setDefaultValue($selectedPayment);
        $form->addSubmit('save_change');
        $form->onSuccess[] = callback($this, 'editDeliveryPaymentFormSubmitted');
        return $form;
    }

    public function editDeliveryPaymentFormSubmitted(UI\Form $form) {
        $values = $form->getValues();
        $this->order->updateDeliveryPayment($this->getParameter('id'), $values->delivery_delivery_id, $values->payment_payment_id);
        $this->flashMessage('Služba byla změněna', 'success');
        if ($this->isAjax())
            $this->invalidateControl('products');
        else {
            $this->redirect('this');
        }
    }

    // vygeneruje objednávku do PDF formátu
    public function createComponentGeneratePdf() {
        $order = $this->order->fetchAllOrdersWithID($this->id)->fetch();

        $dateNow = $order->ord_date;
        $dateExp = new DateTime();
        $dateExp->modify('+7 days');
        $variableSymbol = $order->ord_id;

        $supplierBuilder = new ParticipantBuilder('Lukáš Klimčík', 'Jiránkova', '2301', 'Pardubice', '53002');
        $supplier = $supplierBuilder->setIn('88109321')->setAccountNumber('2100166963 / 2010')->build();
        $customerBuilder = new ParticipantBuilder($order->cust_name . ' ' . $order->cust_surname, $order->cust_street, '', $order->cust_city, $order->cust_psc);
        $customer = $customerBuilder->setAccountNumber('123456789 / 1111')->build();

        $items = array();

        foreach ($this->order->fetchAllOrdersWithID($this->id) as $product) {
            $items[] = new ItemImpl($product->prod_name, $product->quantity, $product->actual_price_of_product, TaxImpl::fromPercent(20));
        }

        $dataBuilder = new DataBuilder(date('YmdHis'), 'Daňový doklad, č.', $supplier, $customer, $dateExp, $dateNow, $items);
        $dataBuilder->setVariableSymbol($variableSymbol)->setDateOfVatRevenueRecognition($dateNow);
        $data = $dataBuilder->build();

        return new Eciovni($data);
    }

    // přidá produkt do nově vytvořené objednávky
    public function createComponentAddProductIntoNewOrderForm() {
        $form = new addProductIntoOrderForm();
        $form->getElementPrototype()->class('ajax');
        $form->onSuccess[] = callback($this, 'addProductIntoNewOrderFormSubmitted');
        return $form;
    }

    public function addProductIntoNewOrderFormSubmitted(UI\Form $form) {
        $values = $form->getValues();
        if ($values->prod_name == '') {
            $this->flashMessage('Nebyl vybrán žádný produkt', 'error');
            $this->redirect('this');
        }

        if (isset($this->product->findProductsID($values->prod_name)->prod_id)) {
            $productID = $this->product->findProductsID($values->prod_name)->prod_id; //podle jména zjistíme ID produktu
        } else {
            $this->flashMessage('Zvolený produkt neexistuje', 'error');
            return;
        }

        if ($values->quantity <= 0) {
            $this->flashMessage('Bylo zvoleno špatné množství produktu', 'error');
            return;
        }

        if (!isset($_SESSION["order"][$productID])) {   // pokud již neexistuje id produktu v objednávce
            $_SESSION["order"][$productID] = $this->product->fetchAllProductForDetail($productID); //zjisti všechny informace o produktu
            if ($this->product->countProductQuantity($productID)->pocet < $values->quantity) {
                $_SESSION['order'][$productID]['order_quantity'] = $this->product->countProductQuantity($productID)->pocet;
                $this->flashMessage('Bohužel, takové množství na skladě nemáme. 
                Do košíku bylo přidáno ' . $_SESSION['order'][$productID]['order_quantity'] . ' kusů', 'info');
            } else {
                $_SESSION["order"][$productID]["order_quantity"] = $values->quantity;
            }
        }

        $_SESSION["orderTotal"] += ($this->product->findPrice($productID)->price * $_SESSION["order"][$productID]["order_quantity"]);

        if (!$this->isAjax())
            $this->redirect('this');
        else {
            $this->invalidateControl('products');
        }
    }

    // upraví produkt v nově vytvořené objednávce
    public function createComponentEditProductIntoNewOrderForm() {
        $form = new UI\Form();
        $quantity = $form->addContainer('quantity');
        foreach ($_SESSION['order'] as $val) {
            $quantity->addText($val->prod_id)
                    ->setType('number')
                    ->setAttribute('class', 'input-mini')
                    ->setValue($val->order_quantity);
        }
        $form->onSuccess[] = callback($this, 'editProductIntoNewOrderFormSubmitted');
        return $form;
    }

    public function editProductIntoNewOrderFormSubmitted(UI\Form $form) {
        $values = $form->getValues();

        foreach ($values->quantity as $prod_id => $quantity) {

            // zkotrolujeme, zda máme tolik kusů na skladě. Pokud ne, přidáme do košíku
            // tolik, kolik na skladě máme
            if ($this->product->countProductQuantity($prod_id)->pocet < $quantity) {
                $_SESSION['order'][$prod_id]['order_quantity'] = $this->product->countProductQuantity($prod_id)->pocet;
                $this->flashMessage('Bohužel, takové množství na skladě nemáme. 
                Do košíku bylo přidáno ' . $_SESSION['order'][$prod_id]['order_quantity'] . ' kusů', 'info');
                return;
            }

            // pokud je zvoleno množství 0:
            // odečte se v count celkové množství daného produktu
            // v totalPrice se provede celková suma -= množství daného produktu * cena jednoho produktu
            // zrušíme v session pole daného produktu
            if ($quantity <= 0) {
                $_SESSION["orderTotal"] -= ($_SESSION["order"][$prod_id]["order_quantity"] * $this->product->findPrice($prod_id)->price);
                unset($_SESSION["order"][$prod_id]);
                if ($_SESSION['order'] == NULL) {
                    unset($_SESSION["orderTotal"], $_SESSION['order']);
                }
            }

            // pokud je zvolené množství větší než stávající množství produktu:
            // celkové množství += zadané množství - stávající množství produktu
            // celková cena += cena jednoho produktu * (zadané množství - stávající množství)
            // změníme množství produktu podle zadaného množství
            elseif ($quantity > $_SESSION["order"][$prod_id]["order_quantity"]) {
                $_SESSION["orderTotal"] += $this->product->findPrice($prod_id)->price * ($quantity - $_SESSION["order"][$prod_id]["order_quantity"]);
                $_SESSION["order"][$prod_id]["order_quantity"] = $quantity;
            }

            // pokud je zvolené množství menší než stávající množství produktu:
            // celkové množství -= stávající množství produktu - zadané množství
            // celková cena -= cena jednoho produktu * (stávající množství - zadané množství)
            // změníme množství produktu podle zadaného množství
            elseif ($quantity < $_SESSION["order"][$prod_id]["order_quantity"]) {
                $_SESSION["orderTotal"] -= $this->product->findPrice($prod_id)->price * ($_SESSION["order"][$prod_id]["order_quantity"] - $quantity);
                $_SESSION["order"][$prod_id]["order_quantity"] = $quantity;
            }
        }

        if (!$this->isAjax())
            $this->redirect('this');
        else {
            $this->invalidateControl('products');
            $this->invalidateControl('form');
        }
    }

    // změní typ dopravy nebo placení v nově vytvořené objednávce
    public function createComponentAddDeliveryPaymentIntoNewForm() {
        $form = new UI\Form();

        // výpis všech typů doručení
        $delivery = array();
        foreach ($this->deliveryPayment->fetchAllDelivery() as $key => $d) {
            $delivery[$key] = $d->delivery_name . ' - ' . $d->delivery_price . ',-Kč';
        }

        // výpis všech typů placení
        $payment = array();
        foreach ($this->deliveryPayment->fetchAllPayment() as $key => $p) {
            $payment[$key] = $p->payment_name . ' - ' . $p->payment_price . ',-Kč';
        }

        $form->addSelect('delivery_delivery_id', 'Doprava', $delivery)
                ->setAttribute('data-rel', 'chosen');
        $form->addSelect('payment_payment_id', 'Placení', $payment)
                ->setAttribute('data-rel', 'chosen');
        $form->addSubmit('save_change');
        $form->onSuccess[] = callback($this, 'addDeliveryPaymentIntoNewFormSubmitted');
        return $form;
    }

    public function addDeliveryPaymentIntoNewFormSubmitted(UI\Form $form) {
        $values = $form->getValues(TRUE);

        $paymentName = $this->deliveryPayment->fetchPaymentType($values['payment_payment_id'])->payment_name;
        $paymentPrice = $this->deliveryPayment->fetchPaymentType($values['payment_payment_id'])->payment_price;
        $deliveryName = $this->deliveryPayment->fetchDeliveryType($values['delivery_delivery_id'])->delivery_name;
        $deliveryPrice = $this->deliveryPayment->fetchDeliveryType($values['delivery_delivery_id'])->delivery_price;

        $_SESSION['deliveryName'] = $deliveryName;
        $_SESSION['deliveryPrice'] = $deliveryPrice;
        $_SESSION['paymentPrice'] = $paymentPrice;
        $_SESSION['paymentName'] = $paymentName;

        if ($this->isAjax())
            $this->invalidateControl('products');
        else {
            $this->redirect('this');
        }
    }

    // vytvoří novou objednávku
    public function createComponentCreateNewOrderForm() {
        $form = new createNewOrder();
        $form->onSuccess[] = callback($this, 'createNewOrderFormSubmitted');
        return $form;
    }

    public function createNewOrderFormSubmitted(UI\Form $form) {
        $values = $form->getValues(TRUE);

        if ($values['cust_name'] == '' || $values['cust_surname'] == '' || $values['cust_email'] == ''
                || $values['cust_telefon'] == '' || $values['cust_street'] == ''
                || $values['cust_city'] == '' || $values['cust_psc'] == '') {
            $this->flashMessage('Nebyly vyplněny všechny povinné údaje', 'error');
            return;
        }

        if ($values['ord_date'] == '') {
            $this->flashMessage('Nebyl vyplněn datum objednávky', 'error');
            return;
        }

        if (!isset($_SESSION['deliveryName'])) {
            $this->flashMessage('Nebyl zvolen žádný typ dopravy či platby', 'error');
            return;
        }

        if (!isset($_SESSION['order'])) {
            $this->flashMessage('Nebyly vybrány žádné produkty', 'error');
            return;
        }

        // check if the billing form was filled
        if ($values['cust_bname'] == "") {
            $values['cust_bname'] = $values['cust_name'];
        }
        if ($values['cust_bsurname'] == "") {
            $values['cust_bsurname'] = $values['cust_surname'];
        }
        if ($values['cust_bstreet'] == "") {
            $values['cust_bstreet'] = $values['cust_street'];
        }
        if ($values['cust_bcity'] == "") {
            $values['cust_bcity'] = $values['cust_city'];
        }
        if ($values['cust_bpsc'] == "") {
            $values['cust_bpsc'] = $values['cust_psc'];
        }
        if ($values['cust_bfirmName'] == "") {
            $values['cust_bfirmName'] = $values['cust_firmName'];
        }

        $dateTime = $values['ord_date'];
        $newDate = new DateTime($dateTime);
        $ord_date = $newDate->format('Y-m-d H:i:s'); // datetime objekt pro databázi
        // zjistíme podle jména ID služby
        $delivery_delivery_id = $this->deliveryPayment->findDeliveryID($_SESSION['deliveryName'])->delivery_id;
        $payment_payment_id = $this->deliveryPayment->findPaymentID($_SESSION['paymentName'])->payment_id;

        $otherItems = array(
            'ord_date' => $ord_date,
            'delivery_delivery_id' => $delivery_delivery_id,
            'payment_payment_id' => $payment_payment_id);

        $values = array_merge($values, $otherItems);

        // uložíme do tabulky orders
        $orderID = $this->order->saveOrder($values);

        // uložíme do tabulky order_has_product
        foreach ($_SESSION["order"] as $value) {
            $this->order->saveIntoOrderHasProduct(
                    $orderID, $value->prod_id, $value->order_quantity, $value->prod_price); //svážeme produkty s objednávkou
            $this->product->updateProductQuantity($value->prod_id, $this->product->countProductQuantity($value->prod_id)->pocet - ($value->order_quantity)); //snížíme množství produktů na skladě
        }

        unset(
                $_SESSION['order'], $_SESSION['deliveryName'], $_SESSION['deliveryPrice'], $_SESSION['paymentName'], $_SESSION['paymentPrice'], $_SESSION['orderTotal']
        );

        $this->flashMessage('Objednávka byla uložena', 'success');
        $this->redirect('Order:');
    }

}