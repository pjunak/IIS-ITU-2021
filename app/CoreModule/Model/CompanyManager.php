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
        TABLE_NAME = 'iis_firma',
        JOINED_TABLES_NAME = 'iis_firma_osoba',
        RUT_ID = 'rut_id',
        EAN = 'ean',
        NAZEV = 'nazev',
        IC = 'ic',
        DIC = 'dic',
        WEB = 'web',
        EMAIL = 'email',
        DATUM_VYTVORENI = 'datum_vytvoreni',
        ULICE = 'ulice',
        CISLO_P = 'cislo_p',
        CISLO_O = 'cislo_o',
        OBEC = 'obec',
        PSC = 'psc',
        PREDCISLI = 'predcisli',
        CISLO_UCTU = 'predcisli',
        KOD_BANKY = 'kod_banky';

    /**
     * Vrátí seznam všech firem v databázi seřazený sestupně od naposledy přidaného.
     * @return Selection seznam všech firem
     */
    public function getCompanies()
    {
        return $this->database->table(self::TABLE_NAME)->order(self::RUT_ID . ' DESC');
    }

    /**
     * Vrátí firmu z databáze podle jeji RUT ID.
     * @param string $rut RUT ID firmy
     * @return false|ActiveRow první firma, která odpovídá RUT ID nebo false pokud firma s danym RUT ID neexistuje
     */
    public function getCompany($rut)
    {
        return $this->database->table(self::TABLE_NAME)->where(self::RUT_ID, $rut)->fetch();
    }

    public function getCompaniesByUser($userID)
    {
        if($userID == NULL)
        {
            return $this->getCompanies();
        }
        else
        {
            return $this->database->query("SELECT * FROM ".self::TABLE_NAME." WHERE rut_id IN (SELECT firma FROM ".self::JOINED_TABLES_NAME." WHERE osoba = $userID)");
        }
        
    }

    /**
     * Uloží firmu do systému.
     * Pokud není nastaveno ID vloží novou firmu, jinak provede editaci firmy s daným ID.
     * @param array|ArrayHash $company firma
     */
    public function saveCompany(ArrayHash $company)
    {
        if (empty($company[self::RUT_ID])) {
            unset($company[self::RUT_ID]);
            $this->database->table(self::TABLE_NAME)->insert($company);
        } else
            $this->database->table(self::TABLE_NAME)->where(self::RUT_ID, $company[self::RUT_ID])->update($company);
    }

    /**
     * Odstraní firmu s danym RUT.
     * @param string $rut RUT firmy
     */
    public function removeCompany(string $rut)
    {
        $this->database->table(self::TABLE_NAME)->where(self::RUT_ID, $rut)->delete();
    }

    /**
     * Vrátí všechny možné kód bank v databázi
     * 
     * Zdroje:
     * https://forum.nette.org/cs/28085-formular-addselect-hodnoty
     * https://stackoverflow.com/questions/2350052/how-can-i-get-enum-possible-values-in-a-mysql-database
     */
    public function get_enum_values()
    {
        $type = $this->database->query( "SHOW COLUMNS FROM ".self::TABLE_NAME." WHERE Field = 'kod_banky'" )->fetch()->Type;
        preg_match("/^enum\(\'(.*)\'\)$/", $type, $matches);
        $enum = explode("','", $matches[1]);
        $pairs = array_combine($enum, $enum);
        return $pairs;
    }
}