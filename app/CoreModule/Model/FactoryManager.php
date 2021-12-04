<?php

declare(strict_types=1);

namespace App\CoreModule\Model;

use App\Model\DatabaseManager;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;
use Nette\Utils\ArrayHash;

/**
 * Model pro správu výroben v redakčním systému.
 * @package App\CoreModule\Model
 */
class FactoryManager extends DatabaseManager
{
    /** Konstanty pro práci s databází. */
    const
        TABLE_NAME = 'iis_vyrobna',
        ID = 'id',
        ID_VYROBNIHO_ZDROJE = 'id_vyrobniho_zdroje',
        ID_SITE = 'id_site',
        KRATKY_NAZEV = 'kratky_nazev',
        ULICE = 'ulice',
        CISLO_P = 'cislo_p',
        CISLO_O = 'cislo_o',
        KRAJ = 'kraj',
        OKRES = 'okres',
        OBEC = 'obec',
        PSC = 'psc',
        PARCELA = 'parcela',
        GPS_N = 'gps_n',
        GPS_E = 'gps_e',
        DRUH_VYROBNY = 'druh_vyrobny',
        VYROBNI_EAN = 'vyrobni_EAN',
        EAN_VYROBNY= 'EAN_vyrobny',
        VYKON_ZDROJE = 'vykon_zdroje',
        NAPETOVA_HLADINA = 'napetova_hladina',
        ZPUSOB_PRIPOJENI = 'zpusob_pripojeni',
        VYKAZ_ZA_OPM = 'vykaz_za_opm',
        DRUH_PODPORY = 'druh_podpory',
        DATUM_PRVNIHO_PRIPOJENI = 'datum_prvniho_pripojeni',
        DATUM_UVEDENI_DO_PROVOZU = 'datum_uvedeni_do_provozu';
    /**
     * Vrátí seznam všech výroben v databázi seřazený sestupně od naposledy přidaného.
     * @return Selection seznam všech firem
     */
    public function getFactories()
    {
        return $this->database->table(self::TABLE_NAME)->order(self::ID . ' DESC');
    }
    public function getFactoriesWhereUser($osoba)
    {
        return $this->database->query("SELECT * FROM iis_vyrobna WHERE id_firmy IN (SELECT firma FROM iis_firma_osoba WHERE osoba = $osoba)")->fetchAll();
    }

    /**
     * Vrátí výrobnu z databáze podle ID.
     * @param string $id ID firmy
     * @return false|ActiveRow první entita, která odpovídá ID nebo false pokud entita s danym ID neexistuje
     */
    public function getFactory($id)
    {
        return $this->database->table(self::TABLE_NAME)->where(self::ID, $id)->fetch();
    }

    public function getNazevVlastniciFirmy($rut_id)
    {
        return $this->database->query("SELECT nazev FROM iis_firma WHERE rut_id = ?", $rut_id)->fetch();
    }

    public function getSeznamDostupnychFirem($userID)
    {
        return $this->database->query(
            "SELECT rut_id, nazev FROM iis_firma WHERE rut_id IN(SELECT firma FROM iis_firma_osoba WHERE osoba = ?)"
            , $userID)->fetchPairs();
    }

    public function updateStavVyrobny($factoryID, $stav)
    {
        $this->database->query('UPDATE iis_vyrobna SET', [
            'stav' => $stav,
        ], 'WHERE id = ?', $factoryID);
    }

    /**
     * Uloží výrobnu systému.
     * Pokud není nastaveno ID vloží novoý výrobnu, jinak provede editaci výrobny s daným ID.
     * @param array|ArrayHash $factory
     */
    public function saveFactory(ArrayHash $factory)
    {
        if (empty($factory[self::ID])) {
            unset($factory[self::ID]);
            $this->database->table(self::TABLE_NAME)->insert($factory);
        } else
            $this->database->table(self::TABLE_NAME)->where(self::ID, $factory[self::ID])->update($factory);
    }

    /**
     * Vrátí všechny možnosti enumu v databázi
     * 
     * Zdroje:
     * https://forum.nette.org/cs/28085-formular-addselect-hodnoty
     * https://stackoverflow.com/questions/2350052/how-can-i-get-enum-possible-values-in-a-mysql-database
     */
    public function get_enum_values($field)
    {
        $type = $this->database->query( "SHOW COLUMNS FROM ".self::TABLE_NAME." WHERE Field = '{$field}'" )->fetch()->Type;
        preg_match("/^enum\(\'(.*)\'\)$/", $type, $matches);
        $enum = explode("','", $matches[1]);
        $pairs = array_combine($enum, $enum);
        return $pairs;
    }
}