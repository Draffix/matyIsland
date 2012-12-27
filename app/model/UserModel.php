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
        return $this->findAll()->where('user_email', $useremail)
                ->where('user_is_active', 1)
                ->fetch();
    }

    //zjišťujeme jestli již existuje zadaný email (pokud 0 tak neexistuje)
    public function countFindByEmail($useremail) {
        return $this->findAll()->where('user_email', $useremail)->count();
    }

    public function updateHash($hash) {
        return $this->getTable()
                ->where('user_hash', $hash)
                ->update(array('user_hash' => NULL,
                    'user_is_active' => 1));
    }

    public function setPassword($id, $password) {
        $this->getTable()->where(array('user_id' => $id))->update(array(
            'user_password' => Authenticator::calculateHash($password)
        ));
    }

}
