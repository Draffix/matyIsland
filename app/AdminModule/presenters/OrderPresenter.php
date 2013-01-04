<?php

namespace AdminModule;

class OrderPresenter extends BasePresenter {

    public function renderDefault() {
        
    }

    public function renderShow($id) {
        $this->template->order = $this->order->fetchOrder($id)->fetch();
        $this->template->orderProducts = $this->order->fetchAllOrdersWithID($id);

        $totalPrice = 0;
        foreach ($this->order->fetchAllOrdersWithID($id) as $o) {
            $totalPrice += ($o->quantity * $o->actual_price_of_product) + $o->deliveryPrice;
        }
        $this->template->totalPrice = $totalPrice;
    }

}