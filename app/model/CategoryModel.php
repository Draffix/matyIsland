<?php

class CategoryModel extends Table {

    /** @var string */
    protected $tableName = 'category';

    public function categoryFilter($id, $limit, $offset) {
        return $this->connection->query(
                        'SELECT * FROM category, product, `category_has_product`, image 
                WHERE category.cat_id = category_has_product.category_cat_id 
                AND product.prod_id = category_has_product.product_prod_id
                AND product.prod_id = image.product_prod_id
                AND cat_id = ?
                AND product.prod_is_active = ?
                GROUP BY product.prod_id
                LIMIT ? OFFSET ?', $id, 1, $limit, $offset);
    }

    public function countCategoryFilter($id) {
        return $this->connection->query(
                        'SELECT COUNT(*) AS pocet 
                FROM category, product, `category_has_product`, image 
                WHERE category.cat_id = category_has_product.category_cat_id 
                AND product.prod_id = category_has_product.product_prod_id
                AND product.prod_id = image.product_prod_id
                AND cat_id = ?
                AND product.prod_is_active = ?
                GROUP BY product.prod_id', $id, 1)->fetch();
    }

    public function fetchAllCategoryNames() {
        return $this->connection->table('category')
                        ->select('cat_name');
    }

}
