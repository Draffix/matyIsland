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

    public function saveIntoOrderHasProduct($id_order, $id_product, $quantity, $price) {
        $this->connection->query(
                'INSERT INTO order_has_product (order_ord_id, product_prod_id, quantity, actual_price_of_product)
                VALUES (?, ?, ?, ?)', $id_order, $id_product, $quantity, $price);
    }

}
