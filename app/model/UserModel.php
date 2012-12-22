<?php

namespace MatyIsland;

/**
 * Description of UserModel
 */
class UserModel extends Table {

    /** @var string */
    protected $tableName = 'user';

    /** @var Nette\Database\Connection */
    private $database;


    public function saveUser($values) {
        $this->connection->exec('INSERT INTO user', array(
            'user_login' => $values->nick,
            'user_email' => $values->email,
            'user_password' => Authenticator::calculateHash($values->pass)
        ));
    }

    public function findByName($username) {
        return $this->findAll()->where('user_login', $username)->fetch();
    }

    public function setPassword($id, $password) {
        $this->getTable()->where(array('id' => $id))->update(array(
            'password' => Authenticator::calculateHash($password)
        ));
    }

}
