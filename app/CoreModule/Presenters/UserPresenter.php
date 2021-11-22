<?php

declare(strict_types=1);

namespace App\CoreModule\Presenters;

use App\CoreModule\Model\UserManager;
use App\Presenters\BasePresenter;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Database\UniqueConstraintViolationException;
use Nette\Utils\ArrayHash;

/**
 * Presenter pro vykreslování článků.
 * @package App\CoreModule\Presenters
 */
class UserPresenter extends BasePresenter
{
    /** @var string URL výchozího článku. */
    private string $defaultUserId;

    /** @var UserManager Model pro správu s článků. */
    private UserManager $userManager;

    /**
     * Konstruktor s nastavením URL výchozího článku a injektovaným modelem pro správu článků.
     * @param string         $defaultUserId URL výchozího článku
     * @param UserManager $userManager    automaticky injektovaný model pro správu článků
     */
    public function __construct(string $defaultUserId, UserManager $userManager)
    {
        parent::__construct();
        $this->defaultUserId = $defaultUserId;
        $this->userManager = $userManager;
    }

    /**
     * Načte a předá článek do šablony podle jeho URL.
     * @param string|null $id URL článku
     * @throws BadRequestException Jestliže článek s danou URL nebyl nalezen.
     */
    public function renderDefault(string $id = null)
    {
        if (!$id) $id = $this->defaultUserId; // Pokud není zadaná URL, vezme se URL výchozího článku.

        // Pokusí se načíst článek s danou URL a pokud nebude nalezen vyhodí chybu 404.
        if (!($user = $this->userManager->getUser($id)))
            $this->error(); // Vyhazuje výjimku BadRequestException.

        $this->template->user = $user; // Předá článek do šablony.
    }

    /** Načte a předá seznam článků do šablony. */
    public function renderList()
    {
        $this->template->users = $this->userManager->getUsers();
    }

    /**
     * Odstraní článek.
     * @param string|null $id URL článku
     * @throws AbortException
     */
    public function actionRemove(string $id = null)
    {
        $this->userManager->removeUser($id);
        $this->flashMessage('Uživatel byl úspěšně odstraněn.');
        $this->redirect('User:list');
    }

    /**
     * Vykresluje formulář pro editaci článku podle zadané URL.
     * Pokud URL není zadána, nebo článek s danou URL neexistuje, vytvoří se nový.
     * @param string|null $id URL adresa článku
     */
    public function actionEditor(string $id = null)
    {
        if ($id) {
            if (!($user = $this->userManager->getUser($id)))
                $this->flashMessage('Uživatel nebyl nalezen.'); // Výpis chybové hlášky.
            else $this['editorForm']->setDefaults($user); // Předání hodnot článku do editačního formuláře.
        }
    }

    /**
     * Vytváří a vrací formulář pro editaci článků.
     * @return Form formulář pro editaci článků
     */
    protected function createComponentEditorForm()
    {
        // Vytvoření formuláře a definice jeho polí.
        $form = new Form;
        //$form->addHidden('id');
        $form->addText('id', 'ID osoby')->setRequired();
        $form->addText('id_ucastnika', 'Identifikace účastníka')->setRequired();
        $form->addText('jmeno', 'Jméno')->setRequired();
        $form->addText('prijmeni', 'Příjmení')->setRequired();
        $form->addInteger('telefon', 'Telefonní číslo')->setRequired();
        $form->addEmail('email', 'E-mail')->setRequired();
        $form->addSubmit('save', 'Uložit uživatele');

        // Funkce se vykonaná při úspěšném odeslání formuláře a zpracuje zadané hodnoty.
        $form->onSuccess[] = function (Form $form, ArrayHash $values) {
            try {
                $this->userManager->saveUser($values);
                $this->flashMessage('Uživatel byl úspěšně uložen.');
                $this->redirect('User:', $values->id_id);
            } catch (UniqueConstraintViolationException $e) {
                $this->flashMessage('Uživatel s tímto ID již existuje.');
            }
        };

        return $form;
    }
}