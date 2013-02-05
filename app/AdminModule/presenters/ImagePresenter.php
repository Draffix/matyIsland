<?php

namespace AdminModule;

use Nette\Application\UI\Form;

class ImagePresenter extends BasePresenter {

    public function renderDefault() {
        $this->template->description = $this->setting->fetchAllSettings()->warning_description;
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

}