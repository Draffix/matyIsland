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

        $totalPrice = 0;
        foreach ($this->order->fetchAllOrdersWithID($id) as $o) {
            $totalPrice += ($o->quantity * $o->actual_price_of_product);
        }
        $totalPrice += $this->order->fetchAllOrdersWithID($id)->fetch()->deliveryPrice;
        $this->template->totalPrice = $totalPrice;
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

}