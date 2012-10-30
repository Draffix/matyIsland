<?php

use Nette\Application\UI;

/**
 * Description of RegistrationPresenter
 *
 * @author Draffix
 */
class RegistrationPresenter extends BasePresenter {

    /** @var MatyIsland\UserModel */
    private $user;

    protected function startup() {
        parent::startup();
        $this->user = $this->context->users;
    }

    protected function createComponentSignUpForm() {
        $form = new UI\Form;
        $form->addText('nick', 'Přezdívka:');
        $form->addText('email', 'Email:');
        $form->addPassword('pass', 'Heslo:');
        $form->addSubmit('register', 'Registrovat');
        $form->onSuccess[] = callback($this, 'signUpFormSubmitted');
        return $form;
    }

// volá se po úspěšném odeslání registrace
    public function signUpFormSubmitted(UI\Form $form) {
        $values = $form->getValues();
        $this->user->saveUser($values);
        $this->flashMessage('Byl jsi úspěšně zaregistrován.');
        $this->redirect('Homepage:');
    }

}