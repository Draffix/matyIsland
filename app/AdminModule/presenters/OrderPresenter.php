<?php

namespace AdminModule;

use Nette\Application\UI;

class OrderPresenter extends BasePresenter {

    /** @persistent */
    public $id;

    public function renderShow($id) {
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

    protected function createComponentMainInfoForm() {
        $form = new mainInfoForm();
        $form->onSuccess[] = callback($this, 'mainInfoFormSubmitted');
        return $form;
    }

    // volá se po úspěšném odeslání registrace
    public function mainInfoFormSubmitted(UI\Form $form) {
        $values = $form->getValues();
        $this->order->updateOrder($this->id, $values);

//        $_SESSION['a'] = $values;
        $this->flashMessage('Úspěch');
        $this->redirect('this');
    }

}