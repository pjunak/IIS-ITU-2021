<?php

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
        TABLE_NAME = 'pozadavek',
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
        SELECT pozadavek.*, osoba.jmeno, osoba.prijmeni
        FROM pozadavek
        LEFT JOIN osoba ON pozadavek.id_osoby = osoba.id
        ')->fetchAll();
    }

    /**
     * Vrátí požadavek z databáze podle ID.
     * @param string $id ID firmy
     * @return false|ActiveRow první entita, která odpovídá ID nebo false pokud entita s danym ID neexistuje
     */
    public function getRequest($id)
    {
        $request = $this->database->query('
            SELECT pozadavek.*, osoba.jmeno, osoba.prijmeni
            FROM pozadavek
            LEFT JOIN osoba ON pozadavek.id_osoby = osoba.id
            WHERE pozadavek.id = ?
        ', $id)->fetch();

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
     * Odstraní požadavek s danym ID
     * @param string $id ID entity
     */
    public function removeRequest(string $id)
    {
        $this->database->table(self::TABLE_NAME)->where(self::ID, $id)->delete();
    }
}