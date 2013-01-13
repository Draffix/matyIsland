<?php

namespace AdminModule;

use Nette\Application\UI;
use Nette\Mail\Message;

class OrderPresenter extends BasePresenter {

    protected function createComponentPaginator() {
        $visualPaginator = new \VisualPaginator();
        return $visualPaginator;
    }

    public function renderDetail($id) {
        $this->template->order = $this->order->fetchOrder($id)->fetch();
        $this->template->orderProducts = $this->order->fetchAllOrdersWithID($id);

        $totalPrice = 0;
        foreach ($this->order->fetchAllOrdersWithID($id) as $o) {
            $totalPrice += ($o->quantity * $o->actual_price_of_product) + $o->deliveryPrice;
        }
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

    public function handleRemoveProductIntoOrder($product_id, $order_id) {
        if ($this->order->countOfSingleOrderHasProduct($order_id)->pocet == 1) {
            $this->flashMessage('V objednávce musí zůstat alespoň jeden produkt', 'error');
        } else {
            $this->order->deleteIntoOrderHasProduct($order_id, $product_id);
            $this->flashMessage('Produkt úspěšně smazán', 'valid');
        }
        $this->redirect('this');
    }

    protected function createComponentMainInfoForm() {
        $form = new mainInfoForm();
        $form->onSuccess[] = callback($this, 'mainInfoFormSubmitted');
        return $form;
    }

    // volá se po úspěšném odeslání registrace
    public function mainInfoFormSubmitted(UI\Form $form) {
        $values = $form->getValues();
        $this->order->updateOrder($this->getParameter('id'), $values);

        $this->flashMessage('Objednávka byla uložena', 'valid');
        $this->redirect('this');
    }

    public function createComponentAddItemForm() {
        $form = new UI\Form;
        $form->addText('quantity');
        $form->addHidden('product_id');
        $form->addHidden('order_id');
        $form->addHidden('price');
        $form->addSubmit('add_item');
        $form->onSuccess[] = callback($this, 'addItemFormSubmitted');
        return $form;
    }

    public function addItemFormSubmitted(UI\Form $form) {
        $values = $form->getValues();

//        $_SESSION['b'] = $this->order->countOfOrderHasProduct($values['order_id'], $values['product_id']);
        if ($this->order->countOfOrderHasProduct($values['order_id'], $values['product_id'])->pocet == 0) {
            $this->order->saveIntoOrderHasProduct($values['order_id'], $values['product_id'], $values['quantity'], $values['price']);
            $this->flashMessage('Produkt úspěšně přidán', 'valid');
        } else {
            $this->flashMessage('Produkt již v seznamu položek existuje', 'error');
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