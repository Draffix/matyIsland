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

    public function fetchAllProductsWithOffset($limit, $offset) {
        return $this->connection->table($this->image)
                        ->where('image.image_is_main = ?', 1)
                        ->limit($limit, $offset)
                        ->order('product.prod_id');
    }

    public function countProducts() {
        return $this->getTable()->count();
    }

    public function fetchProductForDetail($id) {
        return $this->getTable()
                        ->where('prod_id', $id)
                        ->fetch();
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
}