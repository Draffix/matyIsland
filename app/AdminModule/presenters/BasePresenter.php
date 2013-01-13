<?php

namespace AdminModule;

use Nette\Diagnostics\Debugger;

class BasePresenter extends \Nette\Application\UI\Presenter {

    /** @var \OrderModel */
    protected $order;

    /** @var \CategoryModel */
    protected $category;

    /** @var \ProductModel */
    protected $product;

    protected function startup() {
        parent::startup();
        $this->order = $this->context->order;
        $this->category = $this->context->category;
        $this->product = $this->context->product;
    }

    public function beforeRender() {
            Debugger::barDump($_SESSION);
    }

    public function handleSignOut() {
        $this->getUser()->logout(TRUE); //odhlásí i identitu
        $this->redirect(':Homepage:default');
    }

}