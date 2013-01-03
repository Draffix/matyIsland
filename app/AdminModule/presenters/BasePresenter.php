<?php

namespace AdminModule;

class BasePresenter extends \Nette\Application\UI\Presenter {

    protected function startup() {
        parent::startup();
    }

    public function handleSignOut() {
        $this->getUser()->logout(TRUE); //odhlásí i identitu
        $this->redirect(':Homepage:default');
    }

}