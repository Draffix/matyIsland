<?php

namespace AdminModule;

use Nette\Application\UI;
use Nette\Mail\Message;

class OrderPresenter extends BasePresenter {

    /** @persistent */
    public $id;

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
        
    }

    public function renderShow() {
        $paginator = $this['paginator']->getPaginator();
        $paginator->itemsPerPage = 30;
        $paginator->setBase(1);
        $paginator->itemCount = $this->order->countOrders();
        $orders = $this->order->fetchAllOrdersWithOffset($paginator->itemsPerPage, $paginator->offset);

        $this->template->orders = $orders;
    }

    protected function createComponentMainInfoForm() {
        $form = new mainInfoForm();
        $form->onSuccess[] = callback($this, 'mainInfoFormSubmitted');
        return $form;
    }

    // volá se po úspěšném odeslání registrace
    public function mainInfoFormSubmitted(UI\Form $form) {
        $values = $form->getValues();
        $this->order->updateOrder($this->id, $values);

        $this->flashMessage('Objednávka byla uložena', 'valid');
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