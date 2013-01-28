<?php

namespace AdminModule;

class HomepagePresenter extends BasePresenter {

    // odstraní objednávku
    public function handleDeleteOrder($ord_id) {
        $this->order->deleteOrder($ord_id);
        $this->flashMessage('Objednávka byla smazána', 'success');
        $this->redirect('this');
    }

    public function renderDefault() {
        $this->template->mainOrders = $this->order->fetchAllOrders();

        // celkový počet
        $this->template->countOrders = $this->order->countOrders();
        $this->template->countUsers = $this->users->countUsers();
        $this->template->countProducts = $this->product->countProducts();
        $this->template->countCategories = $this->category->countCategories();

        // nevyřízené objednávky
        $this->template->unfinishedOrders = $this->order->countUnfinishedOrders();

        // zablokovaní uživatelé
        $this->template->blockedUsers = $this->users->countBlockedUsers();

        // neaktivní produkty
        $this->template->inactiveProducts = $this->product->countInactiveProducts();
    }

}