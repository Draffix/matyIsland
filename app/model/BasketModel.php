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
     * Ukládá údaje o dané session a množství daného produktu
     * Ukládá údaj o poslední vloženém ID (z tabulky basket => basket_id) a
     * id produktu
     * @param type $items
     * @param type $product
     */
    public function saveItemIntoBasket($items, $product) {
        $this->connection->query(
        'INSERT INTO `basket` ?;' , $items);
                        
        $this->connection->query(           
        'INSERT INTO basket_has_product (basket_basket_id, product_prod_id)
        VALUES(LAST_INSERT_ID(), ?)', $product);
    }

    /**
     * Aktualizuje zboží pro přihlášení z košíku do databáze
     * @param type $id
     * @param type $quantity
     */
    public function updateItemIntoBasket($id, $quantity) {
        $this->connection->query(
                    'UPDATE basket_has_product AS bp JOIN basket AS b ON b.basket_id = bp.basket_basket_id
                    JOIN product AS p ON p.prod_id = bp.product_prod_id
                    SET b.basket_quantity = ? + basket_quantity 
                    WHERE p.prod_id = ?', $quantity, $id
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
                        FROM basket_has_product AS bp JOIN basket AS b ON b.basket_id = bp.basket_basket_id 
                        JOIN product AS p ON p.prod_id = bp.product_prod_id
                        WHERE user_id = ?', $user)->fetch();
    }

    /**
     * Vrací počet řádků produktů patřící uživatelovi
     * @param type $id_product
     * @param type $user_id
     * @return type
     */
    public function findProduct($id_product, $user_id) {
        return $this->connection->query(
                        'SELECT b.* FROM basket_has_product AS bp JOIN basket AS b ON b.basket_id = bp.basket_basket_id
                        JOIN product AS p ON p.prod_id = bp.product_prod_id
                        WHERE prod_id = ?
                        AND user_id = ?', $id_product, $user_id)->rowCount();
    }

    /**
     * Aktualizování množství daného produktu
     * @param type $id
     * @param type $quantity
     */
    public function updateProduct($id, $quantity) {
        $this->connection->query(
                'UPDATE basket_has_product AS bp JOIN basket AS b ON b.basket_id = bp.basket_basket_id
                JOIN product AS p ON p.prod_id = bp.product_prod_id 
                SET b.basket_quantity = ? 
                WHERE p.prod_id = ?', $quantity, $id);
    }

    /**
     * Vrací množství daného produktu
     * @param type $id
     * @return type
     */
    public function findQuantity($id) {
        return $this->connection->query(
                        'SELECT b.basket_quantity AS quan 
                        FROM basket_has_product AS bp JOIN basket AS b ON b.basket_id = bp.basket_basket_id
                        JOIN product AS p ON p.prod_id = bp.product_prod_id
                        WHERE p.prod_id = ?', $id)->fetch();
    }

    /**
     * Zjišťuju cenu daného produktu
     * @param type $id
     * @return type
     */
    public function findPrice($id) {
        return $this->connection->query(
                        'SELECT prod_price AS price 
                        FROM `product` 
                        WHERE prod_id = ?', $id)->fetch();
    }

    
    /**
     * Zjišťuje veškeré informace o daném produktu
     * @param type $id
     * @return type
     */
    public function fetchImagesAndAll($id) {
        return $this->connection->query(
                        'SELECT * FROM product, image
             WHERE product.prod_id = image.product_prod_id 
             AND product.prod_id = ?', $id)->fetch();
    }

    /**
     * Maže údaj z tabulky basket_has_product a jeho podřízený záznam (ON DELETE CASCADE)
     * @param type $id_product
     * @param type $id_user
     */
    public function dropItemFromBasket($id_product, $id_user) {
        $this->connection->query(
                'DELETE bp, b FROM basket_has_product AS bp INNER JOIN basket AS b ON b.basket_id = bp.basket_basket_id 
                 INNER JOIN product AS p ON p.prod_id = bp.product_prod_id
                 WHERE prod_id = ?
                 AND user_id = ?', $id_product, $id_user);
    }

    /**
     * Zjišťujeme, jestli má uživatel uloženy v košíku nějaké produkty
     * @param type $user_id
     * @return type
     */
    public function findProductInBasket($user_id) {
        return $this->connection->query(
                        'SELECT b.*, p.*, i.*, SUM( b.basket_quantity * p.prod_price ) AS totalPrice 
                        FROM basket_has_product AS bp JOIN basket AS b ON b.basket_id = bp.basket_basket_id 
                        JOIN product AS p ON p.prod_id = bp.product_prod_id,
                        image AS i
                        WHERE p.prod_id = i.product_prod_id
                        AND user_id = ?
                        GROUP BY b.basket_id', $user_id);
    }

}