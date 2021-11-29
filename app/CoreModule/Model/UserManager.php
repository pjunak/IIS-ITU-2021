<?php

declare(strict_types=1);

namespace App\CoreModule\Model;

use App\Model\DatabaseManager;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;
use Nette\Utils\ArrayHash;

use Nette\Utils;
use Nette\Security\Passwords;

/**
 * Model pro správu osob v redakčním systému.
 * @package App\CoreModule\Model
 */
class UserManager extends DatabaseManager
{
    /** Konstanty pro práci s databází. */
    const
        TABLE_NAME = 'iis_osoba',
        ID = 'id',
        ID_UCASTNIKA = 'id_ucastnika',
        TYP_OSOBY = 'typ_osoby',
        JMENO = 'jmeno',
        PRIJMENI = 'prijmeni',
        TELEFON = 'telefon',
        EMAIL = 'email',
        HESLO = 'heslo',
        LOGIN = 'login',
        ULICE = 'ulice',
        CISLO_P = 'cislo_p',
        CISLO_O = 'cislo_o',
        OBEC = 'obec',
        PSC = 'psc',
        KANCELAR = 'kancelar',
        POZICE = 'pozice',
        PLAT = 'plat';

   
    
    /*
    private Passwords $passwords;

    private Nette\Database\Explorer $database;

    
    public function __construct(Passwords $passwords)
	{
		$this->database = $database;
		$this->passwords = $passwords;
	}
    */

    /**
     * Vrátí seznam všech entit v databázi seřazený sestupně od naposledy přidaného.
     * @return Selection seznam všech firem
     */
    public function getUsers()
    {
        return $this->database->table(self::TABLE_NAME)->order(self::ID . ' DESC');
    }

    /**
     * Vrátí entitu z databáze podle ID.
     * @param string $id ID firmy
     * @return false|ActiveRow první entita, která odpovídá ID nebo false pokud entita s danym ID neexistuje
     */
    public function getUser($id)
    {
        return $this->database->table(self::TABLE_NAME)->where(self::ID, $id)->fetch();
    }

    /**
     * Uloží entitu systému.
     * Pokud není nastaveno ID vloží novou entitu, jinak provede editaci entity s daným ID.
     * @param array|ArrayHash $user firma
     */
    public function saveUser(ArrayHash $user)
    {
        add();
        if (empty($user[self::ID])) {
            unset($user[self::ID]);
            /*
            //$this->passwords = new Passwords(PASSWORD_DEFAULT, ['cost' => 12]);
            $this->database->table(self::TABLE_NAME)->insert([
                self::ID_UCASTNIKA => $user['id_ucastnika'],
                self::JMENO => $user['jmeno'],
                self::JMENO => $user['prijmeni'],
                self::PRIJMENI => $user['telefon'],
                self::EMAIL => $user['email'],
                self::LOGIN => $user['login'],
                //self::HESLO => $this->passwords->hash($user['password']),
            ]);
            */
            $this->database->table(self::TABLE_NAME)->insert($user);
        } else
            $this->database->table(self::TABLE_NAME)->where(self::ID, $user[self::ID])->update($user);


            Nette\Utils\Validators::assert($email, 'email');
		try {
			$this->database->table(self::TABLE_NAME)->insert([
				self::COLUMN_NAME => $username,
				self::COLUMN_PASSWORD_HASH => $this->passwords->hash($password),
				self::COLUMN_EMAIL => $email,
			]);
		} catch (Nette\Database\UniqueConstraintViolationException $e) {
			throw new DuplicateNameException;
		}
    }

    /**
     * Odstraní entitu s danym ID
     * @param string $id ID entity
     */
    public function removeUser(string $id)
    {
        $this->database->table(self::TABLE_NAME)->where(self::ID, $id)->delete();
    }
}