<?php

use Nette\Application\UI;
use Nette\Utils\Validators;
use Nette\Mail\Message;

/**
 * Description of RegistrationPresenter
 *
 * @author Draffix
 */
class RegistrationPresenter extends BasePresenter {

    /** @var UserModel */
    private $user;

    protected function startup() {
        parent::startup();
        $this->user = $this->context->users;
    }

    public function handleActive($hash) {
        $this->user->updateHash($hash);
        $this->flashMessage('Vaše registrace byla úspěšně dokončena. Nyní se můžete přihlásit', 'success');
        $this->redirect('Login:');
    }

    protected function createComponentPersonalForm() {
        $form = new personalForm();
        $form->addAntispam();        
        $form->addSubmit('send', 'Registrovat');
        $form->onSuccess[] = callback($this, 'personalFormSubmitted');
        return $form;
    }

    // volá se po úspěšném odeslání registrace
    public function personalFormSubmitted(UI\Form $form) {
        $values = $form->getValues();

        //provedeme kontrolu získaných dat
        if ($this->user->countFindByEmail($values->user_email) != 0) {
            $this->flashMessage('Litujeme, ale zadaný email již existuje.', 'wrong');
            return;
        }

        if (!Validators::isEmail($values->user_email)) {
            $this->flashMessage('Litujeme, ale není platná e-mailová adresa', 'wrong');
            return;
        }

        if (!Validators::is($values->user_password, 'string:5..')) {
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
            $this->flashMessage('Litujeme, ale uvedený telefonní číslo neobsahuje pět čísel.', 'wrong');
            return;
        }

        // Create a unique activation code
        $activation = md5(uniqid(rand(), true));

        unset($values['spam'], $values['form_created'], $values['user_confirmPassword']);
        $values->user_hash = $activation;
        $values['user_password'] = Authenticator::calculateHash($values['user_password']);
        $this->user->saveUser($values);

        $template = $this->createTemplate();
        $template->setFile(__DIR__ . '/../templates/Registration/regEmaill.latte');
        $template->registerFilter(new Nette\Latte\Engine);
        $template->hash = $activation;
        $template->website = $_SERVER['SERVER_NAME'];

        $mail = new Message;
        $mail->setFrom('MatyLand.cz <info@matyland.com>')
                ->addTo($values->user_email)
                ->setSubject('Aktivace registrace')
                ->setHtmlBody($template)
                ->send();

        $this->flashMessage('Registrační údaje byly úspěšně odeslány. Registraci dokončíte pomocí odkazu,
            který byl odeslán na Váš email.', 'success');
        $this->redirect('Registration:complete');
    }

}