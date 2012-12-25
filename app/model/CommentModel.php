<?php

namespace MatyIsland;

class CommentModel extends Table {

    /** @var String * */
    protected $tableName = 'comments';

    public function insertComment($values) {
        $row = $this->getTable()->insert($values);
        return $row->com_id;
    }

    public function fetchAllComments($product_id) {
        return $this->connection->query(
                        'SELECT comments.*, user.user_name, user.user_surname
                        FROM comments, user
                        WHERE comments.product_prod_id = ?
                        AND user.user_id = comments.user_user_id
                        ORDER BY comments.com_date ASC
                        ', $product_id);
    }

    public function countAllComments($id) {
        $row = $this->getTable()->where('product_prod_id', $id)->count();
        return $row;
    }

}

