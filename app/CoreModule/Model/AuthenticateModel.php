<?php

namespace Models\Admin;
use Nette\Security as NS;

/**
 * Users authenticator model.
 */
class Authenticate extends Base implements NS\IAuthenticator
{
    /**
     * Performs an authentication
     * @param  array
     * @return Nette\Security\Identity
     * @throws Nette\Security\AuthenticationException
     */
    public function authenticate(array $credentials) {
        list($username, $password) = $credentials;
        $row = $this->database->query('SELECT * FROM users WHERE login = ? AND pass = MD5(?)', $username, $password)->fetch();

        if (!$row) {
            throw new NS\AuthenticationException("Wrong login or password.", self::IDENTITY_NOT_FOUND);
        }

        return new NS\Identity($row->id, $row->role, array('username' => $row->login));
    }

}