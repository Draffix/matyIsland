<?php

namespace AdminModule;

use Nette\Application\UI\Form;
use Nette\Image;

class ImagePresenter extends BasePresenter {

    public function handleDeleteSlider($slider_id, $slider_name) {
        $this->slider->deleteSlider($slider_id);

        $targetPath = $this->context->params['wwwDir'] . '/images/info/slideshow/';
        unlink($targetPath . $slider_name);

        $this->flashMessage('Obrázek byl smazán', 'success');
        $this->redirect('this');
    }

    public function renderDefault() {
        $this->template->description = $this->setting->fetchAllSettings()->warning_description;
        $this->template->templates = $this->emailTemplate->fetchAllTemplates();
        $this->template->slider = $this->slider->fetchAllSliderImages();
    }

    public function renderEdit($id) {
        $this->template->templat = $this->emailTemplate->fetchTemplate($id);
    }

    public function createComponentWarningDescription() {
        $form = new Form();
        $form->addTextArea('warning_description')
            ->getControlPrototype()->class('mceEditor');
        $form->addCheckbox('warning_enabled')
            ->setDefaultValue($this->setting->fetchAllSettings()->warning_enabled);
        $form->addSubmit('save_change');
        $form->onSuccess[] = callback($this, 'warningDescriptionSubmitted');
        return $form;
    }

    public function warningDescriptionSubmitted(Form $form) {
        $values = $form->getValues();

        $this->setting->updateWarning($values);
        $this->flashMessage('Nastavení bylo změněno', 'success');
        $this->redirect('this');
    }

    public function createComponentEditTemplateForm() {
        $form = new Form();
        $form->addTextArea('template_content');
        $form->addText('template_subject');
        $form->addHidden('template_id');
        $form->addSubmit('save_change');
        $form->onSuccess[] = callback($this, 'editTemplateFormSubmitted');
        return $form;
    }

    public function editTemplateFormSubmitted(Form $form) {
        $values = $form->getValues();
        $this->emailTemplate->updateTemplate($values->template_id, $values);
        $this->flashMessage('Nastavení bylo změněno', 'success');
        $this->redirect('this');
    }

    /**
     * @return Form
     */
    protected function createComponentAddImageForm() {
        $form = new Form();
        $form->addUpload('image_name');
        $folder = $this->getParam('folder');
        $form->addHidden('folder', $folder);

        $form->addSubmit("save_change", "Odeslat");
        $form->onSuccess[] = $this->addImageFormSucceeded;

        return $form;
    }


    public function addImageFormSucceeded(Form $form) {
        $values = $form->getValues();

        if ($values['image_name'] == '') {
            $this->flashMessage('Nebyly vybrán obrázek', 'error');
            return;
        }

        // provedeme kontrolu obrázků
        if ($values['image_name'] != '' && $values['image_name']->isImage() == FALSE) {
            $this->flashMessage('Nebyl zadán obrázek v platném formátu JPG, PNG nebo GIF', 'error');
            return;
        }
        if ($values['image_name'] != '') {
            $exists = $this->checkIfImageAlredyExists($values['folder'], $values['image_name']);
            if ($exists == TRUE) {
                $this->flashMessage('Chyba při nahrávání. Jméno obrázku již v databázi existuje.
                Přejmenujte proto nahráváný obrázek a zkuste to znovu.', 'error');
                return;
            }
        }

        // uložíme do tabulky Image
        if ($values['image_name'] != '') {
            $this->moveImage($values['folder'], $values['image_name']);
            $this->slider->insertSliderImage($values['image_name']->getSanitizedName());
        }

        $this->flashMessage('Uložení proběhlo v pořádku', 'success');
        $this->redirect('Image:');
    }

    private function moveImage($folder, $name) {
        $filename = $name->getSanitizedName();
        $targetPath = $this->context->params['wwwDir'] . '/images/info/slideshow/';
        if ($folder !== '') {
            $targetPath .= "/$folder";
        }

        $name->move("$targetPath/$filename");
    }

    private function checkIfImageAlredyExists($folder, $name) {
        $exists = FALSE;
        $filename = $name->getSanitizedName();
        $targetPath = $this->context->params['wwwDir'] . '/images/products/';
        if ($folder !== '') {
            $targetPath .= "/$folder";
        }

        if (file_exists("$targetPath/$filename")) {
            $exists = TRUE;
        }
        return $exists;
    }

}