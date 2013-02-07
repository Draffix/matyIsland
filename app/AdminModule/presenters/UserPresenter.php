<?php

namespace AdminModule;

use Nette\Application\UI\Form;
use Nette\Utils\Validators;
use Nette\Mail\Message;

class UserPresenter extends BasePresenter {

    /** @persistent */
    public $id;

    public function handleActive($hash) {
        $this->users->updateHash($hash);
        $this->flashMessage('Vaše registrace byla úspěšně dokončena. Nyní se můžete přihlásit', 'success');
        $this->redirect(':Login:');
    }

    public function handleBlockUser($user_id) {
        $this->users->blockUser($user_id);
        $this->flashMessage('Uživatel je nyní zablokován a nemůže se přihlásit ke svému účtu', 'success');
        $this->redirect('this');
    }

    public function handleUnblockUser($user_id) {
        $this->users->unblockUser($user_id);
        $this->flashMessage('Uživatel je nyní odblokován a může se přihlásit ke svému účtu', 'success');
        $this->redirect('this');
    }

    public function handleDeleteUser($user_id) {
        $this->users->deleteUser($user_id);
        $this->flashMessage('Uživatel byl smazán', 'success');
        $this->redirect('this');
    }

    public function renderDefault() {
        $this->template->users = $this->users->fetchAllMembers();
    }

    public function renderEdit($id) {
        $this->template->user = $this->users->fetchUser($id);
        $this->template->userOrders = $this->order->fetchAllUserOrders($id);
    }

    public function renderAddUser() {
        return;
    }

    public function createComponentEditUserForm() {
        $form = new editUserForm;
        $form->onSuccess[] = callback($this, 'editUserFormSubmitted');
        return $form;
    }

    public function editUserFormSubmitted(Form $form) {
        $values = $form->getValues();

        if ($this->users->findExistsEmail($this->getParameter('id'))->user_email != $values->user_email
                && $this->users->countFindByEmail($values->user_email) != 0) {
            $this->flashMessage('Litujeme, ale zadaný email již existuje.', 'error');
            return;
        }

        $this->users->updateUser($values, $this->id);
        $this->flashMessage('Údaje byly změněny', 'success');
        $this->redirect('this');
    }

    public function createComponentAddUserForm() {
        $form = new editUserForm;
        $form->onSuccess[] = callback($this, 'addUserFormSubmitted');
        return $form;
    }

    public function addUserFormSubmitted(Form $form) {
        $values = $form->getValues();

        //provedeme kontrolu získaných dat
        if ($this->users->countFindByEmail($values->user_email) != 0) {
            $this->flashMessage('Litujeme, ale zadaný email již existuje.', 'warning');
            return;
        }

        if (!Validators::isEmail($values->user_email)) {
            $this->flashMessage('Litujeme, ale není platná e-mailová adresa', 'warning');
            return;
        }

        if (!Validators::isNumericInt($values->user_telefon)) {
            $this->flashMessage('Litujeme, ale uvedený telefon není číslo.', 'warning');
            return;
        }

        if (!Validators::is($values->user_telefon, 'string:9')) {
            $this->flashMessage('Litujeme, ale uvedené telefon není v platném formátu.', 'warning');
            return;
        }

        if (!Validators::isNumericInt($values->user_psc)) {
            $this->flashMessage('Litujeme, ale uvedené PSČ není číslo.', 'warning');
            return;
        }

        if (!Validators::is($values->user_psc, 'string:5')) {
            $this->flashMessage('Litujeme, ale uvedený telefonní číslo neobsahuje pět čísel.', 'warning');
            return;
        }
        // Create a unique activation code
        $activation = md5(uniqid(rand(), true));

        // Create a unique login password
        $pass = $this->randomPassword();
        $values['user_password'] = \Authenticator::calculateHash($pass);
        $values->user_hash = $activation;
        $values->user_role = 'member';

        $this->users->saveUser($values);

        $template = $this->createTemplate();
        $template->setFile(__DIR__ . '/../templates/User/email.latte');
        $template->registerFilter(new \Nette\Latte\Engine);
        $template->hash = $activation;
        $template->pass = $pass;
        $template->email = $values->user_email;

        $mail = new Message;
        $mail->setFrom('MatyLand.cz <info@matyland.com>')
                ->addTo($values->user_email)
                ->setSubject('Aktivace registrace')
                ->setHtmlBody($template)
                ->send();

        $this->flashMessage('Uživatel byl přidán. Aktivační odkaz a náhodně vygenerované heslo
            byly odeslány na uživatelův email.', 'success');
        $this->redirect('User:');
    }

    private function randomPassword() {
        $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

}