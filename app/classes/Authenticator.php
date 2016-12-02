<?php
/**
 * Authenticator 
 */

namespace Model;

use Nette;
use \Nette\Security\Identity;
use \Nette\Object;
use \Nette\Security\IAuthenticator;
use \Nette\Security\AuthenticationException;
use \Kdyby\Doctrine\EntityDao;
use \Majkl578\NetteAddons\Doctrine2Identity\Security\FakeIdentity;

/**
 * Authenticator class
 */
class Authenticator extends Object implements IAuthenticator
{
    /**
     * @var \Kdyby\Doctrine\EntityDao $dao
     */
    private $dao;

    /**
     * @var \Majkl578\NetteAddons\Doctrine2Identity\Http\UserStorage $storage
     */
    private $storage;

    /**
     * @param EntityDao $dao
     * @param \Majkl578\NetteAddons\Doctrine2Identity\Http\UserStorage $storage
     */
    public function __construct(EntityDao $dao,
                                \Majkl578\NetteAddons\Doctrine2Identity\Http\UserStorage $storage)
    {
        $this->dao     = $dao;
        $this->storage = $storage;
    }

    /**
     * provede přihlášení
     * @param  array
     * @return Nette\Security\Identity
     * @throws Nette\Security\AuthenticationException
     */
    public function authenticate(array $credentials)
    {
        list($username, $password) = $credentials;
        $account = $this->dao->findOneBy(array("username" => $username));

        if ($account !== NULL) {
            if (password_verify($password, $account->password) === FALSE) {
                throw new AuthenticationException('Chybně zadané heslo.', self::INVALID_CREDENTIAL);
            }
            
            if ($account->blocked == 1) {
                throw new AuthenticationException("Váš účet je zablokován. Důvod: {$account->blocked_reason}", self::NOT_APPROVED);
            }
        } else {
            throw new AuthenticationException('Přihlašovací jméno není platné.', self::INVALID_CREDENTIAL);
        }
       
        return new FakeIdentity($account->id, '\Entity\User');
    }
}