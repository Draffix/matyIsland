<?php

namespace AdminModule;

use Nette\Application\UI\Form;

class ImagePresenter extends BasePresenter {

    public function renderDefault() {
        $this->template->description = $this->setting->fetchAllSettings()->warning_description;
        $this->template->templates = $this->emailTemplate->fetchAllTemplates();
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

}