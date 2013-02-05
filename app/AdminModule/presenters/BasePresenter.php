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

    /** @var \DeliveryPaymentModel */
    protected $deliveryPayment;

    /** @var \UserModel */
    protected $users;

    /** @var \SettingModel */
    protected $setting;

    protected function startup() {
        parent::startup();
        $this->order = $this->context->order;
        $this->category = $this->context->category;
        $this->product = $this->context->product;
        $this->deliveryPayment = $this->context->deliveryPayment;
        $this->users = $this->context->users;
        $this->setting = $this->context->setting;

        // zahájíme session a potlačíme E_NOTICE při znovu zavolání startupu
        @session_start();
    }

    public function beforeRender() {
        $this->setLayout('layoutAdmin');
        $this->template->favicon = $this->setting->fetchAllSettings()->eshop_favicon;

        if ($this->isAjax()) {
            $this->presenter->invalidateControl('flashMessages');
        }

        Debugger::barDump($_SESSION);
    }

    public function handleSignOut() {
        $this->getUser()->logout(TRUE); //odhlásí i identitu
        $this->redirect(':Homepage:default');
    }

}