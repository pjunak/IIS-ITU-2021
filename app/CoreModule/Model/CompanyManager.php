<?php

declare(strict_types=1);

namespace App\CoreModule\Model;

use App\Model\DatabaseManager;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;
use Nette\Utils\ArrayHash;

/**
 * Model pro správu firem v redakčním systému.
 * @package App\CoreModule\Model
 */
class CompanyManager extends DatabaseManager
{
    /** Konstanty pro práci s databází. */
    const
        TABLE_NAME = 'firma',
        COLUMN_RUT_ID = 'rut_id',
        COLUMN_NAME = 'nazev';
        //COLUMN_URL = 'url';

    /**
     * Vrátí seznam všech firem v databázi seřazený sestupně od naposledy přidaného.
     * @return Selection seznam všech firem
     */
    public function getCompanies()
    {
        return $this->database->table(self::TABLE_NAME)->order(self::COLUMN_RUT_ID . ' DESC');
    }

    /**
     * Vrátí firmu z databáze podle jeji RUT ID.
     * @param string $rut RUT ID firmy
     * @return false|ActiveRow první firma, která odpovídá RUT ID nebo false pokud firma s danym RUT ID neexistuje
     */
    public function getCompany($rut)
    {
        return $this->database->table(self::TABLE_NAME)->where(self::COLUMN_RUT_ID, $rut)->fetch();
    }

    /**
     * Uloží firmu do systému.
     * Pokud není nastaveno ID vloží novou firmu, jinak provede editaci firmy s daným ID.
     * @param array|ArrayHash $company firma
     */
    public function saveCompany(ArrayHash $company)
    {
        if (empty($company[self::COLUMN_RUT_ID])) {
            unset($company[self::COLUMN_RUT_ID]);
            $this->database->table(self::TABLE_NAME)->insert($company);
        } else
            $this->database->table(self::TABLE_NAME)->where(self::COLUMN_RUT_ID, $company[self::COLUMN_RUT_ID])->update($company);
    }

    /**
     * Odstraní firmu s danym RUT.
     * @param string $rut RUT firmy
     */
    public function removeCompany(string $rut)
    {
        $this->database->table(self::TABLE_NAME)->where(self::COLUMN_RUT_ID, $rut)->delete();
    }
}