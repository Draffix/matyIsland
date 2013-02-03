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
                ->update($values);
    }

}
