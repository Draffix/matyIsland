<?php

/**
 * Description of UserModel
 */
class UserModel extends Table {

    /** @var string */
    protected $tableName = 'user';

    /**
     * Uloží uživatele
     * @param type $values
     * @return type
     */
    public function saveUser($values) {
        return $this->getTable()->insert($values);
    }

    /**
     * Upraví uživatelovy údaje
     * @param type $values
     * @param type $userID
     * @return type
     */
    public function updateUser($values, $userID) {
        return $this->getTable()
            ->where('user_id', $userID)
            ->update($values);
    }

    /**
     * Podle emailu zjistí uživatelovy údaje
     * @param type $useremail
     * @return type
     */
    public function findByName($useremail) {
        return $this->findAll()
            ->where('user_email', $useremail)
            ->where('user_is_active', 1)
            ->fetch();
    }

    //zjišťujeme jestli již existuje zadaný email (pokud 0 tak neexistuje)
    public function countFindByEmail($useremail) {
        return $this->findAll()
            ->where('user_email', $useremail)
            ->count();
    }

    // zjišťuje, zda se zadaný email neshoduje s tím starým (Admin\OrderPresenter)
    public function findExistsEmail($user_id) {
        return $this->findAll()
            ->where('user_id', $user_id)
            ->select('user_email')
            ->fetch();
    }

    /**
     * Po úspěšné registraci vymaže hash
     * @param type $hash
     * @return type
     */
    public function updateHash($hash) {
        return $this->getTable()
            ->where('user_hash', $hash)
            ->update(array('user_hash' => NULL,
                'user_is_active' => 1));
    }

    /**
     * Změní uživatelovi heslo
     * @param type $id
     * @param type $password
     */
    public function setPassword($id, $password) {
        $this->getTable()->
            where(array('user_id' => $id))
            ->update(array(
                'user_password' => Authenticator::calculateHash($password)
            ));
    }

    /**
     * Pro výpis všech uživatelů a jejich údajů
     * @return type
     */
    public function fetchAllUsers() {
        return $this->findAll();
    }

    public function fetchAllMembers() {
        return $this->findAll()
            ->where('user_role', 'member');
    }

    public function fetchAdmin() {
        return $this->getTable()
            ->where('user_role', 'admin')
            ->fetch();
    }

    public function updateAdmin($values) {
        return $this->getTable()
            ->where('user_role', 'admin')
            ->update($values);
    }

    /**
     * Pro získání všech údajů jednoho uživatele
     * @param type $user_id
     * @return type
     */
    public function fetchUser($user_id) {
        return $this->getTable()
            ->where('user_id', $user_id)
            ->fetch();
    }

    /**
     * Zablokuje uživatele
     * @param type $user_id
     * @return type
     */
    public function blockUser($user_id) {
        return $this->getTable()
            ->where('user_id', $user_id)
            ->update(array('user_is_active' => 0));
    }

    /**
     * Odblokuje uživatele
     * @param type $user_id
     * @return type
     */
    public function unblockUser($user_id) {
        return $this->getTable()
            ->where('user_id', $user_id)
            ->update(array('user_is_active' => 1));
    }

    /**
     * Smaže uživatele
     * @param type $user_id
     * @return type
     */
    public function deleteUser($user_id) {
        return $this->getTable()
            ->where('user_id', $user_id)
            ->delete();
    }

    /**
     * Celkový počet uživatelů
     * @return type
     */
    public function countUsers() {
        return $this->getTable()
            ->where(array('user_role' => 'member'))
            ->count();
    }

    public function countBlockedUsers() {
        return $this->getTable()
            ->where('user_is_active', 0)
            ->count();
    }

}
