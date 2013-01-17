<?php

namespace AdminModule;

class HomepagePresenter extends BasePresenter {

    public function renderDefault() {
        $mainOrders = $this->order->fetchAllOrders();
        $this->template->mainOrders = $mainOrders;
    }

}