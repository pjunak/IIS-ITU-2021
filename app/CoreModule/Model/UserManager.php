<?php

/*
//	Projekt do předmětu ITU - Zákaznický portál OTE, a.s.
//	Datum: 5.12.2021
//	Autor: Petr Junák, xjunak01
*/

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
                    'heslo' => password_hash($user['heslo'], PASSWORD_DEFAULT)
                ]);
                $osobaID = $this->database->query("SELECT id FROM iis_osoba WHERE login = ?", $user['login'])->fetch();
                $this->database->table('iis_firma_osoba')->insert([
                    'osoba' => $osobaID->id,
                    'firma' => $user['id_firmy']
                ]);
            }
            else
            {
                $this->database->table(self::TABLE_NAME)->insert($user);
            }
            
        } else
        {//aktualizace stávajícího uživatele
            if($user['typ_osoby'] == 'disponent')
            {
                $this->database->query('UPDATE iis_osoba SET', [
                    'id_ucastnika' => $user['id_ucastnika'],
                    'typ_osoby' => $user['typ_osoby'],
                    'jmeno' => $user['jmeno'],
                    'prijmeni' => $user['prijmeni'],
                    'telefon' => $user['telefon'],
                    'email' => $user['email'],
                    'login' => $user['login'],
                    'heslo' => password_hash($user['heslo'], PASSWORD_DEFAULT)
                ], 'WHERE id = ?', $user['id']);

                $this->database->query('UPDATE iis_firma_osoba SET', [
                    'firma' => $user['id_firmy']
                ], 'WHERE osoba = ?', $user['id']);
            }
            else
            {
                $this->database->table(self::TABLE_NAME)->where(self::ID, $user[self::ID])->update($user);
            }
            
        }
            
    }

    public function updateUser(ArrayHash $user)
    {
        //aktualizace stávajícího uživatele
        if($user['typ_osoby'] == 'disponent')
        {
            $this->database->query('UPDATE iis_osoba SET', [
                'id_ucastnika' => $user['id_ucastnika'],
                'typ_osoby' => $user['typ_osoby'],
                'jmeno' => $user['jmeno'],
                'prijmeni' => $user['prijmeni'],
                'telefon' => $user['telefon'],
                'email' => $user['email'],
            ], 'WHERE id = ?', $user['id']);
        }
    }

    /**
     * Aktualizuje heslo stávajícího uživatele
     * @param string ArrayHash $user obsahuje heslo_new, které se nastaví jako nové heslo
     */
    public function updateUserPassword(ArrayHash $user)
    {
        $this->database->query('UPDATE iis_osoba SET iis_osoba.heslo = \''.password_hash($user['heslo_new'], PASSWORD_DEFAULT).'\' WHERE id = '.$user['id']);
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