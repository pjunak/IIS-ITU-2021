<?php

declare(strict_types=1);

namespace App\CoreModule\Model;

use App\Model\DatabaseManager;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;
use Nette\Utils\ArrayHash;

/**
 * Model pro správu výkazů v redakčním systému.
 * @package App\CoreModule\Model
 */
class ReportManager extends DatabaseManager
{
    /** Konstanty pro práci s databází. */
    const
        TABLE_NAME = 'iis_vykaz',
        TABLE_OSOBA = 'iis_osoba',
        TABLE_VYKAZ = 'iis_vykaz',
        TABLE_VYROBNA = 'iis_vyrobna',
        ID = 'id',
        OD = 'od',
        DO = 'do',
        DATUM_CAS_ZADANI_VYKAZU = 'datum_cas_zadani_vykazu',
        SVORKOVA_VYROBA_ELEKTRINY = 'svorkova_vyroba_elektriny',
        VLASTNI_SPOTREBA_ELEKTRINY = 'vlastni_spotreba_elektriny',
        CELKOVA_KONECNA_SPOTREBA = 'celkova_konecna_spotreba',
        SPOTREBA_Z_TOHO_LOKALNI = 'spotreba_z_toho_lokalni',
        SPOTREBA_Z_TOHO_ODBER = 'spotreba_z_toho_odber';


    /**
     * Vrátí seznam všech výkazů v databázi seřazený sestupně od naposledy přidaného.
     * @return Selection seznam všech firem
     */
    public function getReports()
    {
        //return $this->database->table(self::TABLE_NAME)->order(self::ID . ' DESC');
        return $this->database->query('
        SELECT '.self::TABLE_NAME.'.*, '.self::TABLE_OSOBA.'.jmeno, '.self::TABLE_OSOBA.'.prijmeni
        FROM '.self::TABLE_VYKAZ.'
        LEFT JOIN '.self::TABLE_OSOBA.' ON '.self::TABLE_VYKAZ.'.id_osoby = '.self::TABLE_OSOBA.'.id
        ')->fetchAll();
    }

    public function getReportsWhereFactory($vyrobna)
    {
        //return $this->database->table(self::TABLE_NAME)->order(self::ID . ' DESC');
        return $this->database->query("
        SELECT ".self::TABLE_NAME.".*, ".self::TABLE_OSOBA.".jmeno, ".self::TABLE_OSOBA.".prijmeni
        FROM ".self::TABLE_VYKAZ."
        LEFT JOIN ".self::TABLE_OSOBA." ON ".self::TABLE_VYKAZ.".id_osoby = ".self::TABLE_OSOBA.".id
        WHERE ".self::TABLE_VYKAZ.".id_vyrobny = $vyrobna
        ")->fetchAll();
    }

    /**
     * Vrátí výkaz z databáze podle ID.
     * @param string $id ID firmy
     * @return false|ActiveRow první entita, která odpovídá ID nebo false pokud entita s danym ID neexistuje
     */
    public function getReport($id)
    {
        //return $this->database->table(self::TABLE_NAME)->where(self::ID, $id)->fetch();
        return $this->database->query('
            SELECT '.self::TABLE_VYKAZ.'.*, '.self::TABLE_OSOBA.'.jmeno, '.self::TABLE_OSOBA.'.prijmeni, '.self::TABLE_VYROBNA.'.kratky_nazev
            FROM '.self::TABLE_VYKAZ.'
            LEFT JOIN '.self::TABLE_OSOBA.' ON '.self::TABLE_VYKAZ.'.id_osoby = '.self::TABLE_OSOBA.'.id
            LEFT JOIN '.self::TABLE_VYROBNA.' ON '.self::TABLE_VYKAZ.'.id_osoby = '.self::TABLE_VYROBNA.'.id
            WHERE '.self::TABLE_VYKAZ.'.id = ?
        ', $id)->fetch();
    }

    /**
     * Uloží výkaz systému.
     * Pokud není nastaveno ID vloží novoý výkaz, jinak provede editaci výkazu s daným ID.
     * @param array|ArrayHash $report
     */
    public function saveReport(ArrayHash $report)
    {
        if (empty($report[self::ID])) {
            unset($report[self::ID]);
            $this->database->table(self::TABLE_NAME)->insert($report);
        } else
            $this->database->table(self::TABLE_NAME)->where(self::ID, $report[self::ID])->update($report);
    }

    /**
     * Odstraní výkaz s danym ID
     * @param string $id ID entity
     */
    public function removeReport(string $id)
    {
        $this->database->table(self::TABLE_NAME)->where(self::ID, $id)->delete();
    }
    
    /**
     * Vrátí všechny možné kód bank v databázi
     * 
     * Zdroje:
     * https://forum.nette.org/cs/28085-formular-addselect-hodnoty
     * https://stackoverflow.com/questions/2350052/how-can-i-get-enum-possible-values-in-a-mysql-database
     */
    public function get_factories($user)
    {
        $result = array();
        if ($user->isInRole('disponent'))
        {
            $result = $this->database->query("SELECT * FROM iis_vyrobna WHERE id_firmy IN (SELECT firma FROM iis_firma_osoba WHERE osoba = $user->id)")->fetchAll();
        }
        else
        {
            $result = $this->database->query("SELECT * FROM iis_vyrobna")->fetchAll();
        }
        
        if (!empty($result)) 
        {
            foreach ($result as $row) {

                $vyrobny[] = $row['kratky_nazev'];
                $id_vyrobny[] = $row['id'];
            }
        } else
        {
            return NULL;
        }
        
        $pairs = array_combine($id_vyrobny, $vyrobny);
        return $pairs;
    }
}