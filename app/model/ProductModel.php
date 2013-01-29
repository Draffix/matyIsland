<?php

/**
 * Description of ProductModel
 */
class ProductModel extends Table {

    /** @var string */
    protected $tableName = 'product';
    protected $image = 'image';

    /**
     * Vrací řádky, které obsahují nové produkty a jeji primární obrázky. 
     * Je dán limit a offset pro stránkování
     * @return type
     */
    public function fetchImagesAndNews($limit, $offset) {
        return $this->connection->table($this->image)
                        ->where('product.prod_isnew = ?
                            AND product.prod_is_active = ?
                            AND image.image_is_main = ?', array(1, 1, 1))
                        ->order('product.prod_id DESC')
                        ->limit($limit, $offset);
    }

    /**
     * Vrací počet všech nejnovějších produktů pro stránkování
     * @return type
     */
    public function countNews() {
        return $this->getTable()
                        ->select('COUNT(*) AS pocet')
                        ->where('product.prod_isnew = ?
                            AND product.prod_is_active = ?', array(1, 1))
                        ->fetch();
    }

    /**
     * Vrací všechny informace o produktu i s jeho primárním obrázkem (boolean
     * TRUE)
     * @param int $id
     * @return \Nette\Database\Table\ActiveRow|FALSE
     */
    public function fetchImagesAndAll($id) {
        return $this->connection->table($this->image)
                        ->where('product_prod_id = ?
                    AND product.prod_is_active = ?
                    AND image_is_main = ?', array($id, 1, 1))
                        ->fetch();
    }

    /**
     * Vrací pouze všechny obrázky které patří k produktu
     * @param type $id
     * @return type
     */
    public function fetchAllProductsImages($id) {
        return $this->connection->table($this->image)
                        ->where('product_prod_id', $id);
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
        return $this->connection->table($this->image)
                        ->where('product.prod_is_active = ?
                    AND (product.prod_name LIKE ?
                    OR product.prod_describe LIKE ?
                    OR product.prod_producer LIKE ?)', array(1, $text, $text, $text))
                        ->group('product.prod_id')
                        ->limit($limit, $offset);
    }

    /**
     * Vrací počet nalezených záznamů podle hledaného slova
     * @param type $text
     * @return type
     */
    public function countSearchProduct($text) {
        return $this->getTable()
                        ->select('COUNT(*) AS pocet')
                        ->where('product.prod_is_active = ?
                    AND (product.prod_name LIKE ?
                    OR product.prod_describe LIKE ?
                    OR product.prod_producer LIKE ?)', array(1, $text, $text, $text))->fetch();
    }

    public function fetchRankValues($productID) {
        return $this->getTable()
                        ->where('product.prod_id', $productID)
                        ->fetch();
    }

    public function insertRankIfNotExists($totalVotes = 0, $totalValue = 0, $usedIPs = '', $productID) {
        return $this->getTable()
                        ->where('prod_id', $productID)
                        ->update(array('total_votes' => $totalVotes,
                            'total_value' => $totalValue,
                            'used_ips' => $usedIPs));
    }

    public function whetherUserVoted($ip, $productID) {
        return $this->getTable()
                        ->select('used_ips')
                        ->where('used_ips LIKE ?
                    AND prod_id = ?', array($ip, $productID))
                        ->count();
    }

    public function updateRank($totalVotes, $totalValue, $usedIPs, $productID) {
        return $this->getTable()
                        ->where('prod_id', $productID)
                        ->update(array('total_votes' => $totalVotes,
                            'total_value' => $totalValue,
                            'used_ips' => $usedIPs));
    }

    public function randomProduct() {
        return $this->connection->table($this->image)
                        ->where('product.prod_isnew = ?
                            AND product.prod_is_active = ?
                            AND image.image_is_main = ?', array(0, 1, 1))
                        ->order('RAND()')
                        ->limit(1)
                        ->fetch();
    }

    public function countProductQuantity($id_product) {
        return $this->getTable()
                        ->select('product.prod_on_stock AS pocet')
                        ->where('product.prod_id', $id_product)
                        ->fetch();
    }

    public function updateProductQuantity($id_product, $quantityInOrder) {
        return $this->getTable()
                        ->where('product.prod_id', $id_product)
                        ->update(array('prod_on_stock' => $quantityInOrder));
    }

    public function insertProduct($values) {
        return $this->getTable()
                        ->insert(array('prod_name' => $values['prod_name'],
                            'prod_price' => $values['prod_price'],
                            'prod_code' => $values['prod_code'],
                            'prod_describe' => $values['prod_describe'],
                            'prod_long_describe' => $values['prod_long_describe'],
                            'prod_isnew' => $values['prod_isnew'],
                            'prod_on_stock' => $values['prod_on_stock'],
                            'prod_is_active' => $values['prod_is_active']));
    }

    public function insertImage($productID, $name, $isMain = 0) {
        return $this->connection->table($this->image)
                        ->insert(array('product_prod_id' => $productID,
                            'image_name' => $name,
                            'image_is_main' => $isMain));
    }

    public function updateImage($productID, $name, $isMain = 0) {
        return $this->connection->table($this->image)
                        ->update(array('product_prod_id' => $productID,
                            'image_name' => $name,
                            'image_is_main' => $isMain));
    }

    /**
     * Vrací všechny produkty a jeho hlavní obrázek
     * @return type
     */
    public function fetchAllProductsWithImage() {
        return $this->connection->table($this->image)
                        ->where('image.image_is_main = 1')
                        ->group('image.product_prod_id')
                        ->order('product.prod_id');
    }

    /**
     * Celkový počet produktů
     * @return type
     */
    public function countProducts() {
        return $this->getTable()->count();
    }

    public function fetchProductForDetail($id) {
        return $this->getTable()
                        ->where('prod_id', $id)
                        ->fetch();
    }

    /**
     * Vrací všechno pro uložení do session
     * @param type $id
     * @return type
     */
    public function fetchAllProductForDetail($id) {
        return $this->connection->query(
                        'SELECT * FROM product
             WHERE product.prod_id = ?', $id)->fetch();
    }

    public function updateProduct($values, $id_product) {
        return $this->getTable()
                        ->where('prod_id', $id_product)
                        ->update(array('prod_name' => $values['prod_name'],
                            'prod_price' => $values['prod_price'],
                            'prod_code' => $values['prod_code'],
                            'prod_describe' => $values['prod_describe'],
                            'prod_long_describe' => $values['prod_long_describe'],
                            'prod_isnew' => $values['prod_isnew'],
                            'prod_on_stock' => $values['prod_on_stock'],
                            'prod_is_active' => $values['prod_is_active']));
    }

    public function deleteImage($id) {
        return $this->connection->table($this->image)
                        ->where('image_id', $id)
                        ->delete();
    }

    /**
     * Zjistíme vše o daném obrázku
     * @param type $image_id
     * @param type $product_id
     * @return type
     */
    public function fetchSingleMainImage($image_id, $product_id) {
        return $this->connection->table($this->image)
                        ->where(array('image_id' => $image_id,
                            'product_prod_id' => $product_id))
                        ->fetch();
    }

    /**
     * Najdeme si hlavní obrázek produktu
     * @param type $product_id
     * @return type
     */
    public function findMainImageOfProduct($product_id) {
        return $this->connection->table($this->image)
                        ->where(array('product_prod_id' => $product_id,
                            'image_is_main' => 1))
                        ->fetch();
    }

    /**
     * Změníme obrázek jako hlavní
     * @param type $oldMainID
     * @param type $newMainID
     */
    public function updateMainImageOfProduct($oldMainID, $newMainID) {
        $this->connection->table($this->image)
                ->where(array('image_id' => $oldMainID))
                ->update(array('image_is_main' => 0));

        $this->connection->table($this->image)
                ->where(array('image_id' => $newMainID))
                ->update(array('image_is_main' => 1));
    }

    public function findMinimumImageID($product_id) {
        return $this->connection->table($this->image)
                        ->select('MIN(image_id) AS min')
                        ->where('product_prod_id', $product_id)
                        ->group('product_prod_id')
                        ->fetch();
    }

    public function updateImageWithHisID($image_id, $product_id) {
        return $this->connection->table($this->image)
                        ->where(array('image_id' => $image_id,
                            'product_prod_id' => $product_id))
                        ->update(array('image_is_main' => 1));
    }

    /**
     * Vrací počet obrázků patřící produktu
     * @param type $product_id
     * @return type
     */
    public function countImagesOfProduct($product_id) {
        return $this->connection->table($this->image)
                        ->where(array('product_prod_id' => $product_id))
                        ->count();
    }

    /**
     * Vrací všechno z tabulky products
     * @return type
     */
    public function fetchAllProducts() {
        return $this->getTable();
    }

    public function findProductsID($name) {
        return $this->getTable()
                        ->where('prod_name', $name)
                        ->select('prod_id')
                        ->fetch();
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

    public function updateProductToInactive($prod_id) {
        $this->getTable()
                ->where('prod_id', $prod_id)
                ->update(array('prod_is_active' => 0));
    }

    public function updateProductToActive($prod_id) {
        $this->getTable()
                ->where('prod_id', $prod_id)
                ->update(array('prod_is_active' => 1));
    }

    public function countInactiveProducts() {
        return $this->getTable()
                        ->where('prod_is_active', 0)
                        ->count();
    }

    public function categoryOfProduct($prod_id) {
        return $this->connection->query('
                        SELECT c.cat_name, c.cat_id
                        FROM category_has_product AS cp
                        JOIN category AS c ON cp.category_cat_id = c.cat_id
                        JOIN product AS p ON cp.product_prod_id = p.prod_id
                        WHERE p.prod_id = ?', $prod_id);
    }

}