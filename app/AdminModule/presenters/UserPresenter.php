<?php

namespace AdminModule;

use Nette\Application\UI\Form;

class UserPresenter extends BasePresenter {

    /** @persistent */
    public $id;

    public function renderDefault() {
        $this->template->users = $this->users->fetchAllUsers();
    }

    public function renderEdit($id) {
        $this->template->user = $this->users->fetchUser($id);
        $this->template->userOrders = $this->order->fetchAllUserOrders($id);
    }

    public function createComponentEditUserForm() {
        $form = new editUserForm;
        $form->onSuccess[] = callback($this, 'editUserFormSubmitted');
        return $form;
    }

    public function editUserFormSubmitted(Form $form) {
        $values = $form->getValues();
        $this->users->updateUser($values, $this->id);
        $this->flashMessage('Údaje byly změněny', 'success');
        $this->redirect('this');
    }

}