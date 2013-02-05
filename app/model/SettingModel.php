<?php

class SettingModel extends Table {

    /** @var string */
    protected $tableName = 'setting';

    /** @var string */
    protected $ownerTable = 'owner';

    public function fetchAllSettings() {
        return $this->getTable()
                        ->where('ID', 1)
                        ->fetch();
    }

    public function fetchAllOwner() {
        return $this->connection->table($this->ownerTable)
                        ->where('ID', 1)
                        ->fetch();
    }

    public function updateSetting($values) {
        $this->getTable()
                ->where('ID', 1)
                ->update(array('eshop_name' => $values->eshop_name,
                    'eshop_describe' => $values->eshop_describe,
                    'eshop_key_words' => $values->eshop_key_words,
                    'eshop_product_on_homepage' => $values->eshop_product_on_homepage));
    }

    public function updateFavicon($values) {
        $this->getTable()
                ->where('ID', 1)
                ->update(array('eshop_favicon' => $values));
    }

    public function updateOwner($values) {
        $this->connection->table($this->ownerTable)
                ->where('ID', 1)
                ->update($values);
    }

    public function updateDiscount($values) {
        $this->getTable()
                ->where('ID', 1)
                ->update($values);
    }

    public function updateWarning($values) {
        $this->getTable()
                ->where('ID', 1)
                ->update($values);
    }

}
