<?php

namespace AdminModule;

class HomepagePresenter extends BasePresenter {

    /** @var \OrderModel */
    protected $order;

    protected function startup() {
        parent::startup();
        $this->order = $this->context->order;
    }

    protected function createComponentPaginator() {
        $visualPaginator = new \VisualPaginator();
        return $visualPaginator;
    }

    public function renderDefault() {
        $paginator = $this['paginator']->getPaginator();
        $paginator->itemsPerPage = 6;
        $paginator->setBase(1);
        $paginator->itemCount = $this->order->countOrders();
        $mainOrders = $this->order->fetchAllOrdersWithOffset($paginator->itemsPerPage, $paginator->offset);

        $this->template->mainOrders = $mainOrders;
    }

}