<?php

declare(strict_types=1);

namespace App\Model;

use Nette\Database\Explorer;
use Nette\SmartObject;

/**
 * Základní model pro všechny ostatní databázové modely aplikace.
 * Poskytuje přístup k práci s databází.
 * @package App\Model
 */
class DatabaseManager
{
    use SmartObject;

    /** @var Explorer Služba pro práci s databází. */
    protected Explorer $database;

    /**
     * Konstruktor s injektovanou službou pro práci s databází.
     * @param Explorer $database Automaticky injektovaná Nette služba pro práci s databází
     */
    public function __construct(Explorer $database)
    {
        $this->database = $database;
    }
}