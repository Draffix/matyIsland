<?php

use Nette\Application\UI;
use Nette\Mail\Message;

class InfoPresenter extends BasePresenter {

    protected function createComponentContactForm() {
        $form = new UI\Form;
        $form->addText('name', 'Jméno: *')
                ->addRule($form::FILLED);
        $form->addText('telefon', 'Telefon: *')
                ->addRule($form::FILLED);
        $form->addText('email', 'Email: *')
                ->addRule($form::FILLED);
        $form->addTextArea('message', 'Zpráva: *')
                ->addRule($form::FILLED);
        $form->addSubmit('submit', 'Odeslat');

        $form->onSuccess[] = $this->ContactFormSubmitted;
        return $form;
    }

    public function ContactFormSubmitted(UI\Form $form) {
        $values = $form->getValues();

        $mail = new Message;
        $mail->setFrom('MatyLand.cz <info@matyland.com>')
                ->addTo('jerry.klimcik@gmail.com')
                ->setSubject('Kontaktujte nás')
                ->setHtmlBody($values->message)
                ->send();
    }

}