<?php

namespace MatyIsland;

/**
 * Description of ProductModel
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
            image.image_id, image.image_name, image.product_prod_id, image.image_is_main
            FROM product, image 
            WHERE product.prod_id = image.product_prod_id 
            AND product.prod_isnew = 1
            AND image.image_is_main = 1
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
        $images = $this->connection->table('image')->where(array('product_prod_id' => $id, 'image_is_main' => 1))->fetch();
        return $images;

//        return $this->connection->query(
//                        'SELECT * FROM product, image 
//             WHERE product.prod_id = image.product_prod_id 
//             AND product.prod_id = ?', $id)->fetch();
    }

    public function fetchAllProductsImages($id) {
        $images = $this->connection->table('image')->where('product_prod_id', $id);
        return $images;
    }

    public function pokus($id) {
        $images = $this->connection->table("image")->where("product_prod_id", $id)->fetch();
        \Nette\Diagnostics\Debugger::barDump($images->product->prod_producer);
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
     * Vrací záznamy podle hledaného slova a podle limitu a offsetu
     * daného v paginatoru
     * @param type $text
     * @param type $limit
     * @param type $offset
     * @return type
     */
    public function searchProduct($text, $limit, $offset) {
        return $this->connection->query(
                        'SELECT product.prod_id, product.prod_name, product.prod_price, 
                        product.prod_describe, product.prod_producer,
                        image.image_id, image.image_name, image.product_prod_id
                        FROM product
                        LEFT JOIN image ON product.prod_id = image.product_prod_id
                        WHERE product.prod_name LIKE ?
                        OR product.prod_describe LIKE ?
                        OR product.prod_producer LIKE ?
                        ORDER BY product.prod_name ASC LIMIT ? OFFSET ?
                        ', $text, $text, $text, $limit, $offset);
    }

    /**
     * Vrací počet nalezených záznamů podle hledaného slova
     * @param type $text
     * @return type
     */
    public function countSearchProduct($text) {
        return $this->connection->query(
                        'SELECT COUNT(*) AS pocet 
                        FROM product, image
                        WHERE product.prod_id = image.product_prod_id
                        AND (product.prod_name LIKE ?
                        OR product.prod_describe LIKE ?
                        OR product.prod_producer LIKE ?)
                        ', $text, $text, $text)->fetch();
    }

    public function fetchRankValues($productID) {
        return $this->connection->query('
                        SELECT total_votes, total_value, used_ips 
                        FROM product 
                        WHERE prod_id = ?'
                        , $productID)->fetch();
    }

    public function insertRankIfNotExists($totalVotes = 0, $totalValue = 0, $usedIPs = '', $productID) {
        return $this->connection->query('
                        UPDATE product 
                        SET total_votes = ?, total_value = ?, used_ips = ? 
                        WHERE prod_id = ?'
                        , $totalVotes, $totalValue, $usedIPs, $productID);
    }

    public function whetherUserVoted($ip, $productID) {
        return $this->connection->query('
                        SELECT used_ips 
                        FROM product 
                        WHERE used_ips 
                        LIKE ? AND prod_id= ?
                        ', $ip, $productID)->rowCount();
    }

    public function updateRank($totalVotes, $totalValue, $usedIPs, $productID) {
        return $this->connection->query('
                        UPDATE product 
                        SET total_votes = ?, total_value = ?, used_ips = ? 
                        WHERE prod_id = ?'
                        , $totalVotes, $totalValue, $usedIPs, $productID);
    }

}