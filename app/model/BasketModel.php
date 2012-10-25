<?php

namespace MatyIsland;

/**
 * Description of ProductModel
 *
 * @author Draffix
 */
class BasketModel extends Table {

    /** @var string */
    protected $tableName = 'basket';

    /**
     * Ukládá vložený produkt do tabulky 'basket'
     * @param type $items
     */
    public function saveItemIntoBasket($items) {
        $this->connection->table($this->tableName)->insert($items);
    }
    
        public function updateItemIntoBasket($id, $quantity) {
        $this->connection->query(
                    'UPDATE `basket` 
                    SET basket_quantity = ? + basket_quantity 
                    WHERE product_prod_id = ?',$quantity, $id
                );
    }

    /**
     * Vrací celkový počet produktů a celkovou cenu * množství kusů
     * podle id uživatele
     * @param type $user
     * @return type
     */
    public function fetchItemsFromBasket($user) {
        return $this->connection->query(
                        'SELECT SUM(basket_quantity * prod_price) AS totalPrice, 
                        SUM( basket_quantity ) AS totalCount
                        FROM product, basket
                        WHERE product.prod_id = basket.product_prod_id 
                        AND user_id = ?', $user)->fetch();
    }

    /**
     * Vrací počet řádků produktů patřící uživatelovi
     * @param type $id_product
     * @param type $user_id
     * @return type
     */
    public function findProduct($id_product, $user_id) {
        return $this->connection->query(
                        'SELECT * FROM `basket` 
                        WHERE product_prod_id = ? 
                        AND user_id = ?', $id_product, $user_id)->rowCount();
    }

    /**
     * Aktualizování množství daného produktu
     * @param type $id
     * @param type $quantity
     */
    public function updateProduct($id, $quantity) {
        $this->connection->query(
                'UPDATE basket 
                        SET basket_quantity = ? 
                        WHERE product_prod_id = ?', $quantity, $id);
    }

    /**
     * Vrací množství daného produktu
     * @param type $id
     * @return type
     */
    public function findQuantity($id) {
        return $this->connection->query(
                        'SELECT basket_quantity AS quan 
                        FROM `basket` 
                        WHERE product_prod_id = ?', $id)->fetch();
    }

    public function findPrice($id) {
        return $this->connection->query(
                        'SELECT prod_price AS price 
                        FROM `product` 
                        WHERE prod_id = ?', $id)->fetch();
    }

    public function fetchImagesAndAll($id) {
        return $this->connection->query(
                        'SELECT * FROM product, image
             WHERE product.prod_id = image.product_prod_id 
             AND product.prod_id = ?', $id)->fetch();
    }

    public function dropItemFromBasket($id_product, $id_user) {
        $this->connection->query(
                'DELETE FROM `basket` 
                 WHERE `product_prod_id` = ? 
                 AND `user_id` = ?', $id_product, $id_user);
    }

    public function findProductInBasket($user_id) {
        return $this->connection->query(
                        'SELECT basket.product_prod_id AS product_id, basket . * , product . * , image . * , 
                            SUM( basket_quantity * product.prod_price ) AS totalPrice
                        FROM basket, product, image
                        WHERE product.prod_id = basket.product_prod_id
                        AND product.prod_id = image.product_prod_id
                        AND user_id = ?
                        GROUP BY basket_id', $user_id);
    }
}
