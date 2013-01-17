<?php

namespace AdminModule;

use Nette\Application\UI;
use Nette\Mail\Message;

class OrderPresenter extends BasePresenter {

    public function handleChangeStatus($id, $status) {
        $this->order->updateOrderStatus($id, $status);

        $this->flashMessage('Status byl změněn', 'success');
        $this->redirect('this');
    }

    protected function createComponentPaginator() {
        $visualPaginator = new \VisualPaginator();
        return $visualPaginator;
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

    public function renderMainInfo($id) {
        $this->template->order = $this->order->fetchOrder($id)->fetch();
        $this->template->orderProducts = $this->order->fetchAllOrdersWithID($id);

        $totalPrice = 0;
        foreach ($this->order->fetchAllOrdersWithID($id) as $o) {
            $totalPrice += ($o->quantity * $o->actual_price_of_product) + $o->deliveryPrice;
        }
        $this->template->totalPrice = $totalPrice;
    }

    public function renderSendEmail($id) {
        $this->template->order = $this->order->fetchOrder($id)->fetch();
    }

    public function renderShow() {
        $paginator = $this['paginator']->getPaginator();
        $paginator->itemsPerPage = 30;
        $paginator->setBase(1);
        $paginator->itemCount = $this->order->countOrders();
        $orders = $this->order->fetchAllOrdersWithOffset($paginator->itemsPerPage, $paginator->offset);

        $this->template->orders = $orders;
    }

    public function renderItems($id) {
        $this->template->order = $this->order->fetchOrder($id)->fetch();
        $this->template->orderProducts = $this->order->fetchAllOrdersWithID($id);

        $paginator = $this['paginator']->getPaginator();
        $paginator->itemsPerPage = 30;
        $paginator->setBase(1);
        $paginator->itemCount = $this->product->countProducts();
        $products = $this->product->fetchAllProductsWithOffset($paginator->itemsPerPage, $paginator->offset);

        $this->template->products = $products;
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
        $_SESSION['a'] = $values;
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

    protected function createComponentSendEmailForm() {
        $form = new UI\Form;
        $form->addTextArea('text')
                ->getControlPrototype()->class('mceEditor');
        $form->addSubmit('send', 'Odeslat');
        $form->onSuccess[] = callback($this, 'sendEmailFormSubmitted');
        return $form;
    }

    public function sendEmailFormSubmitted(UI\Form $form) {
        $values = $form->getValues();

        $mail = new Message;
        $mail->setFrom('MatyLand.cz <info@matyland.com>')
                ->addTo('jerry.klimcik@gmail.com')
                ->setSubject('Změna údajů')
                ->setHtmlBody($values['text'])
                ->send();

        $this->redirect('this');
    }

}