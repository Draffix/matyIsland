<?php

use Nette\Application\UI;
use Nette\Mail\Message;
use Nette\Utils\Validators;

class InfoPresenter extends BasePresenter {

    public function renderContact() {
        $this->template->owner = $this->setting->fetchAllOwner();
    }

    protected function createComponentContactForm() {
        $form = new UI\Form;
        $form->addText('name', 'Jméno:')
                ->addRule($form::FILLED);
        $form->addText('telefon', 'Telefon:');
        $form->addText('email', 'Email:')
                ->addRule($form::FILLED)
                ->setType('email');
        $form->addTextArea('message', 'Zpráva:')
                ->addRule($form::FILLED);
        $form->addSubmit('send', 'Odeslat');

        $form->onSuccess[] = $this->ContactFormSubmitted;
        return $form;
    }

    public function ContactFormSubmitted(UI\Form $form) {
        $values = $form->getValues();

        if (!Validators::isEmail($values->email)) {
            $this->flashMessage('Litujeme, ale není platná e-mailová adresa', 'wrong');
            return;
        }

        $mail = new Message;
        $mail->setFrom('MatyLand.cz <info@matyland.com>')
                ->addTo($values->email)
                ->setSubject('Kontaktujte nás')
                ->setHtmlBody($values->message)
                ->send();

        $this->flashMessage('Děkujeme, Vaše zpráva byla odeslána', 'success');
        $this->redirect('this');
    }

}