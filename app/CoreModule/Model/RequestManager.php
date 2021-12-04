<?php

/*
//	Projekt do předmětu ITU - Zákaznický portál OTE, a.s.
//	Datum: 5.12.2021
//	Autor: Kristián Heřman, xherma33
*/

declare(strict_types=1);

namespace App\CoreModule\Model;

use App\Model\DatabaseManager;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;
use Nette\Utils\ArrayHash;

/**
 * Model pro správu požadavků v redakčním systému.
 * @package App\CoreModule\Model
 */
class RequestManager extends DatabaseManager
{
    /** Konstanty pro práci s databází. */
    const
        TABLE_NAME = 'iis_pozadavek',
        TABLE_OSOBA = 'iis_osoba',
        ID = 'id',
        ID_OSOBY = 'id_osoby',
        DATUM_VYTVORENI = 'datum_vytvoreni',
        DATUM_UZAVRENI = 'datum_uzavreni',
        PREDMET = 'predmet',
        STATUS = 'status',
        OBSAH_POZADAVKU = 'obsah_pozadavku',
        ODPOVED = 'odpoved';


    /**
     * Vrátí seznam všech požadavků v databázi seřazený sestupně od naposledy přidaného.
     * @return Selection seznam všech firem
     */
    public function getRequests()
    {
        return $this->database->query('
        SELECT '.self::TABLE_NAME.'.*, '.self::TABLE_OSOBA.'.jmeno, '.self::TABLE_OSOBA.'.prijmeni
        FROM '.self::TABLE_NAME.'
        LEFT JOIN '.self::TABLE_OSOBA.' ON '.self::TABLE_NAME.'.id_osoby = '.self::TABLE_OSOBA.'.id
        ')->fetchAll();
    }

    /**
     * Vrátí seznam všech požadavků v databázi od zadaného uživatele seřazený sestupně od naposledy přidaného.
     * @return Selection seznam všech firem
     */
    public function getRequestsByUser($userID)
    {
        if($userID == NULL)
        {
            return getRequests();
        }
        else
        {
            return $this->database->query(
            'SELECT '.self::TABLE_NAME.'.* FROM '.self::TABLE_NAME.' WHERE '.self::TABLE_NAME.'.id_osoby = ?', $userID)->fetchAll();
        }
    }

    /**
     * Vrátí požadavek z databáze podle ID.
     * @param string $id ID firmy
     * @return false|ActiveRow první entita, která odpovídá ID nebo false pokud entita s danym ID neexistuje
     */
    public function getRequest($id)
    {
        $request = $this->database->query('
            SELECT '.self::TABLE_NAME.'.*, '.self::TABLE_OSOBA.'.jmeno, '.self::TABLE_OSOBA.'.prijmeni
            FROM '.self::TABLE_NAME.'
            LEFT JOIN '.self::TABLE_OSOBA.' ON '.self::TABLE_NAME.'.id_osoby = '.self::TABLE_OSOBA.'.id
            WHERE '.self::TABLE_NAME.'.id = ?', $id)->fetch();

        //return $this->database->table(self::TABLE_NAME)->where(self::ID, $id)->fetch();
        return $request;
    }

    /**
     * Uloží požadavek systému.
     * Pokud není nastaveno ID vloží novoý požadavek, jinak provede editaci požadavku s daným ID.
     * @param array|ArrayHash $request
     */
    public function saveRequest(ArrayHash $request)
    {
        if (empty($request[self::ID])) {
            unset($request[self::ID]);
            $this->database->table(self::TABLE_NAME)->insert($request);
        } else
            $this->database->table(self::TABLE_NAME)->where(self::ID, $request[self::ID])->update($request);
    }

    /**
     * Odstraní požadavek s daným ID
     * @param string $id ID entity
     */
    public function removeRequest(string $id)
    {
        $this->database->table(self::TABLE_NAME)->where(self::ID, $id)->delete();
    }
}