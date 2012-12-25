<?php

namespace MatyIsland;

/**
 * Description of UserModel
 */
class UserModel extends Table {

    /** @var string */
    protected $tableName = 'user';

    public function saveUser($values) {
        return $this->getTable()->insert($values);
    }

    public function findByName($useremail) {
        return $this->findAll()->where('user_email', $useremail)->fetch();
    }

    public function countFindByEmail($useremail) {
        return $this->findAll()->where('user_email', $useremail)->count();
    }

    public function setPassword($id, $password) {
        $this->getTable()->where(array('id' => $id))->update(array(
            'password' => Authenticator::calculateHash($password)
        ));
    }

}
