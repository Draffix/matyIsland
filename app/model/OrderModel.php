<?php

namespace MatyIsland;

use Nette\DateTime;

/**
 * Description of ProductModel
 *
 * @author Draffix
 */
class OrderModel extends Table {

    /** @var string */
    protected $tableName = 'order';

    public function saveOrder($items) {
        $row = $this->connection->table($this->tableName)->insert($items);
        return $row->ord_id; //zjistíme ID vložené objednávky
    }

}
