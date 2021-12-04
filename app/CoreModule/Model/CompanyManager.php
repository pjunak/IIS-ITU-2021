<?php

/*
//	Projekt do předmětu ITU - Zákaznický portál OTE, a.s.
//	Datum: 5.12.2021
//	Autor: Dalibor Čásek, xcasek01
*/

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

    public function getCompanyUsers($rut)
    {
        return $this->database->query("SELECT * FROM iis_osoba WHERE iis_osoba.id IN (SELECT osoba FROM iis_firma_osoba WHERE firma = ?)", $rut);
    }
    public function getOtherUsers($rut)
    {
        return $this->database->query("SELECT * FROM iis_osoba WHERE iis_osoba.typ_osoby = 'disponent' AND NOT iis_osoba.id IN (SELECT osoba FROM iis_firma_osoba WHERE firma = ?)", $rut);
    }

    public function addUserToCompany($rut, $id)
    {
        $this->database->table('iis_firma_osoba')->insert([
            'osoba' => $id,
            'firma' => $rut
        ]);
    }

    public function removeUserFromCompany($rut, $id)
    {
        $this->database->query("DELETE FROM iis_firma_osoba WHERE iis_firma_osoba.osoba = ? AND iis_firma_osoba.firma = ?", $id, $rut);
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
}