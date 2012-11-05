<?php

namespace MatyIsland;

/**
 * Description of ProductModel
 *
 * @author Draffix
 */
class OrderModel extends Table {

    /** @var string */
    protected $tableName = 'basket';
    
    public function saveOrder($order, $payment, $delivery, $comment) {
        $this->connection->query(
                'INSERT INTO `order` (cust_name) VALUES (?)', $_SESSION["order"]['name']
                );
    }

}
