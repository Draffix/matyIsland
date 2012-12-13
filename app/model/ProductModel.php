<?php

namespace MatyIsland;

/**
 * Description of ProductModel
 *
 * @author Draffix
 */
class ProductModel extends Table {

    /** @var string */
    protected $tableName = 'product';

    /**
     * Vrací řádky, které obsahují nové produkty a jejich obrázky. Je dán limit
     * a offset pro stránkování
     * @return type
     */
    public function fetchImagesAndNews($limit, $offset) {
        return $this->connection->query(
                        'SELECT product.prod_id, product.prod_name, product.prod_price, product.prod_describe,
            image.image_id, image.image_path, image.product_prod_id
            FROM product, image 
            WHERE product.prod_id = image.product_prod_id 
            AND product.prod_isnew = 1
            ORDER BY product.prod_id DESC LIMIT ? OFFSET ?', $limit, $offset);
    }

    /**
     * Vrací počet všech nejnovějších produktů pro stránkování
     * @return type
     */
    public function countNews() {
        return $this->connection->query(
                        'SELECT COUNT(*) AS pocet FROM product, image 
            WHERE product.prod_id = image.product_prod_id 
            AND product.prod_isnew = 1'
                )->fetch();
    }

    /**
     * Vrací záznam s daným primárním klíčem
     * @param int $id
     * @return \Nette\Database\Table\ActiveRow|FALSE
     */
    public function fetchImagesAndAll($id) {
        return $this->connection->query(
                        'SELECT * FROM product, image 
             WHERE product.prod_id = image.product_prod_id 
             AND product.prod_id = ?', $id)->fetch();
    }

    /**
     * Vrací cenu auktálního produktu podle jeho ID
     * @param type $id
     * @return type
     */
    public function fetchPrice($id) {
        return $this->connection->query(
                        'SELECT prod_price 
                        FROM `product` 
                        WHERE prod_id = ?', $id)->fetch();
    }

    /**
     * Ukládá vložený produkt do tabulky 'basket'
     * @param type $items
     */
    public function saveItemIntoBasket($items) {
        $this->connection->table('basket')->insert($items);
    }

    /**
     * Vrací celkový počet produktů a celkovou cenu košíku
     * podle id uživatele
     * @param type $user
     * @return type
     */
    public function fetchItemsFromBasket($user) {
        return $this->connection->query(
                        'SELECT SUM(prod_price) AS totalPrice, SUM( basket_quantity ) AS totalCount
                        FROM product, basket
                        WHERE product.prod_id = basket.product_prod_id 
                        AND user_id = ?', $user)->fetch();
    }

    public function searchProduct($text) {
        return $this->connection->query(
                        'SELECT * FROM product, image 
                        WHERE product.prod_id = image.product_prod_id
                        AND prod_describe LIKE ? 
                        OR prod_name LIKE ?
                        OR prod_producer LIKE ?
                        GROUP BY prod_id
                        ', $text, $text, $text)->fetchAll();
    }

}

?>
