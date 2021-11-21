<?php

declare(strict_types=1);

namespace App\CoreModule\Model;

use App\Model\DatabaseManager;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;
use Nette\Utils\ArrayHash;

/**
 * Model pro správu článků v redakčním systému.
 * @package App\CoreModule\Model
 */
class ArticleManager extends DatabaseManager
{
    /** Konstanty pro práci s databází. */
    const
        TABLE_NAME = 'article',
        COLUMN_ID = 'article_id',
        COLUMN_URL = 'url';

    /**
     * Vrátí seznam všech článků v databázi seřazený sestupně od naposledy přidaného.
     * @return Selection seznam všech článků
     */
    public function getArticles()
    {
        return $this->database->table(self::TABLE_NAME)->order(self::COLUMN_ID . ' DESC');
    }

    /**
     * Vrátí článek z databáze podle jeho URL.
     * @param string $url URl článku
     * @return false|ActiveRow první článek, který odpovídá URL nebo false pokud článek s danou URL neexistuje
     */
    public function getArticle($url)
    {
        return $this->database->table(self::TABLE_NAME)->where(self::COLUMN_URL, $url)->fetch();
    }

    /**
     * Uloží článek do systému.
     * Pokud není nastaveno ID vloží nový článek, jinak provede editaci článku s daným ID.
     * @param array|ArrayHash $article článek
     */
    public function saveArticle(ArrayHash $article)
    {
        if (empty($article[self::COLUMN_ID])) {
            unset($article[self::COLUMN_ID]);
            $this->database->table(self::TABLE_NAME)->insert($article);
        } else
            $this->database->table(self::TABLE_NAME)->where(self::COLUMN_ID, $article[self::COLUMN_ID])->update($article);
    }

    /**
     * Odstraní článek s danou URL.
     * @param string $url URL článku
     */
    public function removeArticle(string $url)
    {
        $this->database->table(self::TABLE_NAME)->where(self::COLUMN_URL, $url)->delete();
    }
}