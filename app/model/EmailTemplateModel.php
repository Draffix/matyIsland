<?php

class EmailTemplateModel extends Table {

    /** @var string */
    protected $tableName = 'email_template';

    public function fetchAllTemplates() {
        return $this->getTable();
    }

    public function fetchTemplate($template_id) {
        return $this->getTable()
                        ->where('template_id', $template_id)
                        ->fetch();
    }

    public function updateTemplate($template_id, $values) {
        $this->getTable()
                ->where('template_id', $template_id)
                ->update($values);
    }

}
