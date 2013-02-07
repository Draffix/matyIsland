<?php

namespace AdminModule;

use Nette\Application\UI;
use Nette\Utils\Validators;

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

    public function createComponentEditAdministratorForm() {
        $form = new UI\Form;
        $form->addText('user_email')
                ->setDefaultValue($this->users->fetchAdmin()->user_email);
        $form->addPassword('old_password');
        $form->addPassword('user_password');
        $form->addPassword('user_confirmPassword');
        $form->addSubmit('save_change');
        $form->onSuccess[] = callback($this, 'editAdministratorFormSubmitted');
        return $form;
    }

    public function editAdministratorFormSubmitted(UI\Form $form) {
        $values = $form->getValues();

        if ($this->users->countFindByEmail($values->user_email) != 0 &&
                $this->users->find($this->getUser()->getId())->user_email != $values->user_email) {
            $this->flashMessage('Litujeme, ale zadaný email již existuje.', 'error');
            return;
        }

        //provedeme kontrolu získaných dat
        if (!Validators::isEmail($values->user_email)) {
            $this->flashMessage('Litujeme, ale není platná e-mailová adresa', 'error');
            return;
        }

        if ($values->old_password != '' && $values->user_password == '' ||
                $values->user_password != '' && $values->old_password == '') {
            $this->flashMessage('Litujeme, ale nebyly vyplněny všechny položky.', 'error');
            return;
        }

        if ($values->user_password != '' && !\Authenticator::verifyPassword($values->old_password, $this->users->find($this->getUser()->getId())->user_password)) {
            $this->flashMessage('Litujeme, ale nebylo zadáno správné stávající heslo.', 'error');
            return;
        }

        if ($values->user_password != '' && !Validators::is($values->user_password, 'string:5..')) {
            $this->flashMessage('Litujeme, ale heslo musí obsahovat minimálně pět znaků.', 'error');
            return;
        }

        if ($values->user_password != $values->user_confirmPassword) {
            $this->flashMessage('Litujeme, ale hesla se neshodují.', 'error');
            return;
        }

        unset($values['user_confirmPassword'], $values['old_password']);
        $values['user_password'] = \Authenticator::calculateHash($values['user_password']);
        $this->users->updateAdmin($values);

        $this->flashMessage('Vaše údaje byly úspěšně změněny a uloženy.', 'success');
        $this->redirect('this');
    }

}