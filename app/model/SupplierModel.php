<?php
/**
 * User: Jaroslav Klimčík
 * Date: 13.7.13
 * Time: 19:38
 */

class SupplierModel extends Table {

    /** @var string */
    protected $tableName = 'supplier';

    /**
    Vytáhneme si všechny údaje o dodavatelích
     */
    public function fetchAllSuppliers() {
        return $this->getTable()->order('sup_name');
    }

    /**
    Uložíme si dodavatele
     */
    public function saveSupplier($values) {
        $this->connection->table($this->tableName)
            ->insert($values);
    }

    /**
    Získáme údaje o jednom dodavateli
     */
    public function fetchSingleSupplier($sup_id) {
        return $this->connection->table($this->tableName)
            ->where('sup_id', $sup_id)
            ->fetch();
    }

    /**
    Aktualizujeme údaje o dodavateli
     */
    public function updateSupplier($sup_id, $values) {
        $this->connection->table($this->tableName)
            ->where('sup_id', $sup_id)
            ->update($values);
    }
}