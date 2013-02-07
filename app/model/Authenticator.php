<?php

use Nette\Security as NS;

/**
 * Users authenticator.
 */
class Authenticator extends Nette\Object implements NS\IAuthenticator {

    /**
     * @var UserModel
     */
    private $users;

    /**
     * @param UserModel $users
     */
    public function __construct(UserModel $users) {
        $this->users = $users;
    }

    /**
     * Performs an authentication
     * @param  array
     * @return Nette\Security\Identity
     * @throws Nette\Security\AuthenticationException
     */
    public function authenticate(array $credentials) {
        list($useremail, $password) = $credentials;
        $row = $this->users->findByName($useremail);

        if (!$row) {
            throw new NS\AuthenticationException("User '$useremail' not found.", self::IDENTITY_NOT_FOUND);
        }

        $bcrypt = new Bcrypt();
        if ($row->user_password != $bcrypt->verify($password, $row->user_password)) {
            throw new NS\AuthenticationException("Invalid password.", self::INVALID_CREDENTIAL);
        }

        unset($row->password);
        return new NS\Identity($row->user_id, $row->user_role);
    }

    /**
     * Computes salted password hash.
     * @param  string
     * @return string
     */
    public static function calculateHash($password) {
        $bcrypt = new Bcrypt();
        return $bcrypt->hash($password);
    }

    public static function verifyPassword($userPass, $databasePass) {
        $bcrypt = new Bcrypt();
        if ($databasePass == $bcrypt->verify($userPass, $databasePass)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

}
