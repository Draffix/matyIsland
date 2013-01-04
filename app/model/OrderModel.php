<?php

/**
 * Description of ProductModel
 */
class OrderModel extends Table {

    /** @var string */
    protected $tableName = 'orders';

    /**
     * Uložíme objednávku a zjistíme ID poslední vložené objednávky
     * @param type $items
     * @return type
     */
    public function saveOrder($items) {
        $row = $this->connection->table($this->tableName)->insert($items);
        return $row->ord_id;
    }

    /**
     * Uložíme do spojovací tabulky OrderHasProduct objednávku s produktem
     * @param type $id_order
     * @param type $id_product
     * @param type $quantity
     * @param type $price
     */
    public function saveIntoOrderHasProduct($id_order, $id_product, $quantity, $price) {
        $this->connection->query(
                'INSERT INTO order_has_product (order_ord_id, product_prod_id, quantity, actual_price_of_product, totalPrice)
                VALUES (?, ?, ?, ?, ?)', $id_order, $id_product, $quantity, $price, $quantity*$price);
    }

    /**
     * Zjistíme vše o dané objednávce
     * @param type $id
     * @return type
     */
    public function fetchOrder($id) {
        return $this->connection->query(
                        'SELECT *, sum(op.actual_price_of_product) AS sum_price, 
                            sum(op.quantity) AS sum_quantity 
                 FROM order_has_product AS op JOIN orders AS o ON o.ord_id = op.order_ord_id 
                 JOIN product AS p ON p.prod_id = op.product_prod_id
                 WHERE ord_id = ?', $id);
    }

    /**
     * Vrací počet objednávek seskupené podle jejich ID
     * @return type
     */
    public function countOrders() {
        return $this->connection->table('order_has_product')
                        ->group('order_ord_id')
                        ->count();
    }

    /**
     * Vrací všechny objednávky s omezením limitu a offsetu
     * @param type $limit
     * @param type $offset
     * @return type
     */
    public function fetchAllOrdersWithOffset($limit, $offset) {
        return $this->connection->query(
                        'SELECT *, SUM(totalPrice) AS sum_price, 
                            sum(op.quantity) AS sum_quantity FROM order_has_product AS op JOIN orders AS o ON o.ord_id = op.order_ord_id 
                 JOIN product AS p ON p.prod_id = op.product_prod_id
                 GROUP BY ord_id
                 ORDER BY ord_date DESC
                 LIMIT ?
                OFFSET ?', $limit, $offset);
    }

    /**
     * Vrací všechny objednávky z dané ID spolu s obrázky a bez jakýchkoliv
     * sum - pro výpis produktů
     * @param type $id
     * @return type
     */
    public function fetchAllOrdersWithID($id) {
        return $this->connection->query(
                        'SELECT *
                FROM order_has_product AS op 
                JOIN orders AS o ON o.ord_id = op.order_ord_id 
                JOIN product AS p ON p.prod_id = op.product_prod_id
                JOIN image AS i ON i.product_prod_id = p.prod_id
                WHERE i.image_is_main = 1
                AND ord_id = ?', $id);
    }
}
