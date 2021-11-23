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
        TABLE_NAME = 'vykaz',
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
        return $this->database->table(self::TABLE_NAME)->order(self::ID . ' DESC');
    }

    /**
     * Vrátí výkaz z databáze podle ID.
     * @param string $id ID firmy
     * @return false|ActiveRow první entita, která odpovídá ID nebo false pokud entita s danym ID neexistuje
     */
    public function getReport($id)
    {
        return $this->database->table(self::TABLE_NAME)->where(self::ID, $id)->fetch();
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
}