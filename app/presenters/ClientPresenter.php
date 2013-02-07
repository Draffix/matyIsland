<?php

use Nette\Application\UI;
use Nette\Utils\Validators;

class ClientPresenter extends BasePresenter {

    /** @var UserModel */
    private $user;

    protected function startup() {
        parent::startup();
        $this->user = $this->context->users;
    }

    public function renderDefault() {
        
    }

    protected function createComponentPersonalForm() {
        $user = $this->users->find($this->getUser()->getId());
        $form = new personalForm();

        //přidáme možnost změny hesla
        $form['user_password']->caption = 'Nové heslo:';
        $form['user_confirmPassword']->caption = 'Nové heslo (kotrola):';
        $form->addPassword('old_password', 'Stávající heslo:');

        //nastavíme hodnoty z databáze
        $form['user_name']->setDefaultValue($user->user_name);
        $form['user_surname']->setDefaultValue($user->user_surname);
        $form['user_telefon']->setDefaultValue($user->user_telefon);
        $form['user_email']->setDefaultValue($user->user_email);
        $form['user_street']->setDefaultValue($user->user_street);
        $form['user_city']->setDefaultValue($user->user_city);
        $form['user_psc']->setDefaultValue($user->user_psc);
        $form['user_firmName']->setDefaultValue($user->user_firmName);
        $form['user_ico']->setDefaultValue($user->user_ico);
        $form['user_dic']->setDefaultValue($user->user_dic);


        $form->addSubmit('send', 'Změnit');
        $form->onSuccess[] = callback($this, 'personalFormSubmitted');
        return $form;
    }

    public function personalFormSubmitted(UI\Form $form) {
        $values = $form->getValues();

        if ($this->user->countFindByEmail($values->user_email) != 0 &&
                $this->users->find($this->getUser()->getId())->user_email != $values->user_email) {
            $this->flashMessage('Litujeme, ale zadaný email již existuje.', 'wrong');
            return;
        }

        //provedeme kontrolu získaných dat
        if (!Validators::isEmail($values->user_email)) {
            $this->flashMessage('Litujeme, ale není platná e-mailová adresa', 'wrong');
            return;
        }

        if ($values->old_password != '' && $values->user_password == '' ||
                $values->user_password != '' && $values->old_password == '') {
            $this->flashMessage('Litujeme, ale nebyly vyplněny všechny položky.', 'wrong');
            return;
        }

        if ($values->user_password != '' && !Authenticator::verifyPassword($values->old_password, $this->users->find($this->getUser()->getId())->user_password)) {
            $this->flashMessage('Litujeme, ale nebylo zadáno správné stávající heslo.', 'wrong');
            return;
        }

        if ($values->user_password != '' && !Validators::is($values->user_password, 'string:5..')) {
            $this->flashMessage('Litujeme, ale heslo musí obsahovat minimálně pět znaků.', 'wrong');
            return;
        }

        if ($values->user_password != $values->user_confirmPassword) {
            $this->flashMessage('Litujeme, ale hesla se neshodují.', 'wrong');
            return;
        }

        if (!Validators::isNumericInt($values->user_telefon)) {
            $this->flashMessage('Litujeme, ale uvedený telefon není číslo.', 'wrong');
            return;
        }

        if (!Validators::is($values->user_telefon, 'string:9')) {
            $this->flashMessage('Litujeme, ale uvedené telefon není v platném formátu.', 'wrong');
            return;
        }

        if (!Validators::isNumericInt($values->user_psc)) {
            $this->flashMessage('Litujeme, ale uvedené PSČ není číslo.', 'wrong');
            return;
        }

        if (!Validators::is($values->user_psc, 'string:5')) {
            $this->flashMessage('Litujeme, ale uvedené telefonní číslo neobsahuje pět čísel.', 'wrong');
            return;
        }

        unset($values['user_confirmPassword'], $values['old_password']);
        $values['user_password'] = Authenticator::calculateHash($values['user_password']);
        $this->user->updateUser($values, $this->getUser()->getId());

        $this->flashMessage('Vaše údaje byly úspěšně změněny a uloženy.', 'success');
        $this->redirect('this');
    }

}