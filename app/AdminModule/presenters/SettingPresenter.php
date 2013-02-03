<?php

namespace AdminModule;

use Nette\Application\UI;
use Nette\Image;
use Nette\Utils\Validators;

class SettingPresenter extends BasePresenter {

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
            }
        }

        unset($values->folder);
        $img = array('.jpg', '.jpeg', '.gif', '.png');
        $values->eshop_favicon = str_replace($img, '.ico', $values->eshop_favicon->getSanitizedName());
        $this->setting->updateSetting($values);

        $this->flashMessage('Nastavení bylo změněno', 'success');
        $this->redirect('this');
    }

    private function checkIfImageAlredyExists($folder, $name) {
        $exists = FALSE;
        $filename = $name->getSanitizedName();
        $targetPath = $this->context->params['wwwDir'] . '/images/info/';
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
        $targetPath = $this->context->params['wwwDir'] . '/images/info/';
        if ($folder !== '') {
            $targetPath .= "/$folder";
        }
        $name->move("$targetPath/$filename");

        $ico_lib = new \PHP_ICO("$targetPath/$filename", array(array(16, 16)));
        $ico_lib->save_ico("$targetPath/$filename");
    }

}