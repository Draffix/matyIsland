<?php

use Nette\Application\UI;
use Nette\Utils\Validators;
use Nette\Http\Request;

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
        $form->addAntispam();
        $form->addText('user_name', 'Jméno:')
                ->addRule($form::FILLED, 'chyba');
        $form->addText('user_surname', 'Příjmení:');
        $form->addPassword('user_password', 'Heslo:');
        $form->addPassword('user_confirmPassword', 'Ověření hesla:');
        $form->addText('user_telefon', 'Telefon:');
        $form->addText('user_email', 'E-mail:')
                ->addRule($form::FILLED);
        $form->addText('user_street', 'Ulice a číslo:');
        $form->addText('user_city', 'Město:');
        $form->addText('user_psc', 'PSČ:');
        $form->addText('user_firmName', 'Název firmy:');
        $form->addText('user_ico', 'IČO');
        $form->addText('user_dic', 'DIČ');
        $form->addSubmit('send', 'Registrovat');
        $form->onError[] = callback($this, 'formError');
        $form->onSuccess[] = callback($this, 'signUpFormSubmitted');
        return $form;
    }

    public function formError(Form $form) {
        $errors = $form->getErrors();
        foreach ($errors as $error) {
            $this->flashMessage($error, 'error');
        }
        $data = $form->getValues();
        // Zde zase můžeš do formu ty data zpátky nacpat, které chceš tou metodou $form->setDefaults();
    }

// volá se po úspěšném odeslání registrace
    public function signUpFormSubmitted(UI\Form $form) {
        $values = $form->getValues();
        unset($values['spam'], $values['form_created'], $values['user_confirmPassword']);

        if ($this->user->countFindByEmail($values->user_email) != 0) {
            $this->flashMessage('Litujeme, ale zadaný email již existuje.', 'wrong');
//            $this->redirect('this');
        }

//        try {
//            Validators::assert($values->user_telefon, 'int');
//        } catch (Exception $exc) {
//            $this->flashMessage('Litujeme, ale zadaný telefon neobsahuje číslice.', 'wrong');
//            $this->redirect('this');
//        }
//
//        $values['user_password'] = \MatyIsland\Authenticator::calculateHash($values['user_password']);
//        $this->user->saveUser($values);
//        $this->flashMessage('Byl jsi úspěšně zaregistrován.');
//        $this->redirect('Homepage:');
    }

}