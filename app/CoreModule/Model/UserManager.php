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

    public function getSeznamFirem()
    {
        return $this->database->query("SELECT rut_id, nazev FROM iis_firma")->fetchPairs();
    }

    /**
     * Uloží entitu systému.
     * Pokud není nastaveno ID vloží novou entitu, jinak provede editaci entity s daným ID.
     * @param array|ArrayHash $user firma
     */
    public function saveUser(ArrayHash $user)
    {
        if (empty($user[self::ID]))
        {//nový uživatel
            unset($user[self::ID]);
            if($user['typ_osoby'] == 'disponent')
            {
                $this->database->table(self::TABLE_NAME)->insert([
                    'id_ucastnika' => $user['id_ucastnika'],
                    'typ_osoby' => $user['typ_osoby'],
                    'jmeno' => $user['jmeno'],
                    'prijmeni' => $user['prijmeni'],
                    'telefon' => $user['telefon'],
                    'email' => $user['email'],
                    'login' => $user['login'],
                    'heslo' => $user['heslo']
                ]);
                $osobaID = $this->database->query("SELECT id FROM iis_osoba WHERE login = ?", $user['login']);
                /*$this->database->table('iis_firma_osoba')->insert([
                    'osoba' => $osobaID,
                    'firma' => $user['id_firmy']
                ]);*/
            }
            else
            {
                $this->database->table(self::TABLE_NAME)->insert($user);
            }
            
        } else
        {//aktualizace stávajícího uživatele
            $this->database->table(self::TABLE_NAME)->where(self::ID, $user[self::ID])->update($user);
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