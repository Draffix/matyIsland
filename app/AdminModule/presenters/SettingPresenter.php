<?php

namespace AdminModule;

use Nette\Application\UI;
use Nette\Image;
use Nette\Utils\Validators;

class SettingPresenter extends BasePresenter {

    public function handleDeactivatePayment($payment_id) {
        $this->deliveryPayment->deactivatePayment($payment_id);
        $this->flashMessage('Nastavení bylo změněno', 'success');
        $this->redirect('this');
    }

    public function handleActivatePayment($payment_id) {
        $this->deliveryPayment->activatePayment($payment_id);
        $this->flashMessage('Nastavení bylo změněno', 'success');
        $this->redirect('this');
    }

    public function handleDeactivateDelivery($delivery_id) {
        $this->deliveryPayment->deactivateDelivery($delivery_id);
        $this->flashMessage('Nastavení bylo změněno', 'success');
        $this->redirect('this');
    }

    public function handleActivateDelivery($delivery_id) {
        $this->deliveryPayment->activateDelivery($delivery_id);
        $this->flashMessage('Nastavení bylo změněno', 'success');
        $this->redirect('this');
    }

    public function renderDefault() {
        $this->template->setting = $this->setting->fetchAllSettings();
        $this->template->owner = $this->setting->fetchAllOwner();
        $this->template->payment = $this->deliveryPayment->fetchAllPayment();
        $this->template->delivery = $this->deliveryPayment->fetchAllDelivery();
    }

    public function createComponentSettingForm() {
        $form = new UI\Form;
        $form->addText('eshop_name');
        $form->addText('eshop_describe');
        $form->addText('eshop_key_words');
        $form->addUpload('eshop_favicon');
        $form->addText('eshop_product_on_homepage');
        $folder = $this->getParam('folder');
        $form->addHidden('folder', $folder);
        $form->addSubmit('save_change');
        $form->onSuccess[] = callback($this, 'settingFormSubmitted');
        return $form;
    }

    public function settingFormSubmitted(UI\Form $form) {
        $values = $form->getValues();

        if (!Validators::isNumericInt($values->eshop_product_on_homepage) || $values->eshop_product_on_homepage < 0) {
            $this->flashMessage('Byl zadán špatný počet produktů na hlavní stránce', 'error');
            $this->redirect('this');
        }

        if ($values->eshop_favicon != '' && $values->eshop_favicon->isImage() == FALSE) {
            $this->flashMessage('Byl zadán špatný obrázek', 'error');
            $this->redirect('this');
        }

        if ($values->eshop_favicon != '') {
            $exists = $this->checkIfImageAlredyExists($values->folder, $values->eshop_favicon);
            if ($exists == TRUE) {
                $this->flashMessage('Chyba při nahrávání. Jméno obrázku již v databázi existuje. 
                Přejmenujte proto nahráváný obrázek a zkuste to znovu.', 'error');
                return;
            } else {
                $this->moveImage($values->folder, $values->eshop_favicon);
                $values->eshop_favicon = $values->eshop_favicon->getSanitizedName();
                $this->setting->updateFavicon($values->eshop_favicon);
            }
        }

        unset($values->folder);
        $this->setting->updateSetting($values);

        $this->flashMessage('Nastavení bylo změněno', 'success');
        $this->redirect('this');
    }

    public function createComponentOwnerForm() {
        $form = new UI\Form();
        $form->addText('owner_name');
        $form->addText('owner_name2');
        $form->addText('owner_email');
        $form->addText('owner_telefon');
        $form->addText('owner_telefon2');
        $form->addText('owner_street');
        $form->addText('owner_city');
        $form->addText('owner_psc');
        $form->addText('owner_ico');
        $form->addText('owner_dic');
        $form->addSubmit('save_change');
        $form->onSuccess[] = callback($this, 'ownerFormSubmitted');
        return $form;
    }

    public function ownerFormSubmitted(UI\Form $form) {
        $values = $form->getValues();
        $this->setting->updateOwner($values);
        $this->flashMessage('Nastavení bylo změněno', 'success');
        $this->redirect('this');
    }

    public function createComponentPaymentForm() {
        $callback = callback($this, 'paymentFormSubmitted');
        return new UI\Multiplier(function ($payment_id) use ($callback) {
                            $form = new UI\Form();
                            $form->addHidden('payment_id', $payment_id);
                            $form->addText('payment_describe');
                            $form->addText('payment_price');
                            $form->addSubmit('save_change');
                            $form->onSuccess[] = $callback;
                            return $form;
                        });
    }

    public function paymentFormSubmitted(UI\Form $form) {
        $values = $form->getValues();
        $this->deliveryPayment->updatePayment($values->payment_id, $values->payment_describe, $values->payment_price);
        $this->flashMessage('Nastavení bylo změněno', 'success');
        $this->redirect('this');
    }

    public function createComponentDeliveryForm() {
        $callback = callback($this, 'deliveryFormSubmitted');
        return new UI\Multiplier(function ($delivery_id) use ($callback) {
                            $form = new UI\Form();
                            $form->addHidden('delivery_id', $delivery_id);
                            $form->addText('delivery_describe');
                            $form->addText('delivery_price');
                            $form->addSubmit('save_change');
                            $form->onSuccess[] = $callback;
                            return $form;
                        });
    }

    public function deliveryFormSubmitted(UI\Form $form) {
        $values = $form->getValues();
        $this->deliveryPayment->updateDelivery($values->payment_id, $values->payment_describe, $values->payment_price);
        $this->flashMessage('Nastavení bylo změněno', 'success');
        $this->redirect('this');
    }

    public function createComponentDiscountForm() {
        $form = new UI\Form();
        $form->addText('eshop_discount');
        $form->addCheckbox('eshop_discount_for_registered', 'Jen pro registrované uživatele')
                ->setDefaultValue($this->setting->fetchAllSettings()->eshop_discount_for_registered);
        $form->addSubmit('save_change');
        $form->onSuccess[] = callback($this, 'discountFormSubmitted');
        return $form;
    }

    public function discountFormSubmitted(UI\Form $form) {
        $values = $form->getValues();

        if ($values->eshop_discount < 0) {
            $this->flashMessage('Byla špatně zadána sleva', 'error');
            return;
        }

        $this->setting->updateDiscount($values);
        $this->flashMessage('Nastavení bylo změněno', 'success');
        $this->redirect('this');
    }

    private function checkIfImageAlredyExists($folder, $name) {
        $exists = FALSE;
        $filename = $name->getSanitizedName();
        $targetPath = $this->context->params['wwwDir'] . '/images/info/favicon/';
        if ($folder !== '') {
            $targetPath .= "/$folder";
        }

        if (file_exists("$targetPath/$filename")) {
            $exists = TRUE;
        }
        return $exists;
    }

    private function moveImage($folder, $name) {
        $filename = $name->getSanitizedName();
        $targetPath = $this->context->params['wwwDir'] . '/images/info/favicon/';
        if ($folder !== '') {
            $targetPath .= "/$folder";
        }
        $name->move("$targetPath/$filename");

        $ico_lib = new \PHP_ICO("$targetPath/$filename", array(array(16, 16)));
        $ico_lib->save_ico("$targetPath/$filename");
    }

}