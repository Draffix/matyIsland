<?php

namespace AdminModule;

class BasePresenter extends \Nette\Application\UI\Presenter {

    /** @var \OrderModel */
    protected $order;

    protected function startup() {
        parent::startup();
        $this->order = $this->context->order;
    }

    public function handleSignOut() {
        $this->getUser()->logout(TRUE); //odhlásí i identitu
        $this->redirect(':Homepage:default');
    }

}