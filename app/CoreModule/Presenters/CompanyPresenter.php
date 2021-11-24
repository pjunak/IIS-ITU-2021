<?php

declare(strict_types=1);

namespace App\CoreModule\Presenters;

use App\CoreModule\Model\CompanyManager;
use App\Presenters\BasePresenter;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Database\UniqueConstraintViolationException;
use Nette\Utils\ArrayHash;
use Nette\Security\User;
/**
 * Presenter pro vykreslování článků.
 * @package App\CoreModule\Presenters
 */
class CompanyPresenter extends BasePresenter
{
    /** @var string URL výchozího článku. */
    private string $defaultCompanyRut;

    /** @var CompanyManager Model pro správu s článků. */
    private CompanyManager $companyManager;

    /** @var user Pro identifikaci uživatele */
    private $user;

    /**
     * Konstruktor s nastavením URL výchozího článku a injektovaným modelem pro správu článků.
     * @param string         $defaultCompanyRut URL výchozího článku
     * @param CompanyManager $companyManager    automaticky injektovaný model pro správu článků
     */
    public function __construct(string $defaultCompanyRut, CompanyManager $companyManager)
    {
        parent::__construct();
        $this->defaultCompanyRut = $defaultCompanyRut;
        $this->companyManager = $companyManager;
    }

    public function startup()
    {
        parent::startup();
        $this->user = $this->getUser();
        if (!$this->user->isLoggedIn())
        {
            $this->redirect(':Sign:in');
        }
    }

    /**
     * Načte a předá článek do šablony podle jeho URL.
     * @param string|null $rut URL článku
     * @throws BadRequestException Jestliže článek s danou URL nebyl nalezen.
     */
    public function renderDefault(string $rut = null)
    {
        if (!$rut) $rut = $this->defaultCompanyRut; // Pokud není zadaná URL, vezme se URL výchozího článku.

        // Pokusí se načíst článek s danou URL a pokud nebude nalezen vyhodí chybu 404.
        if (!($company = $this->companyManager->getCompany($rut)))
            $this->error(); // Vyhazuje výjimku BadRequestException.

        $this->template->company = $company; // Předá článek do šablony.
    }

    /** Načte a předá seznam článků do šablony. */
    public function renderList()
    {
        if($this->user->isInRole('disponent'))
        {
            $this->template->companies = $this->companyManager->getCompanyByUser($this->user->getID());
        }
        else
        {
            $this->template->companies = $this->companyManager->getCompanyByUser(NULL);
        }
    }

    /**
     * Odstraní článek.
     * @param string|null $rut URL článku
     * @throws AbortException
     */
    public function actionRemove(string $rut = null)
    {
        $this->companyManager->removeCompany($rut);
        $this->flashMessage('Článek byl úspěšně odstraněn.');
        $this->redirect('Company:list');
    }

    /**
     * Vykresluje formulář pro editaci článku podle zadané URL.
     * Pokud URL není zadána, nebo článek s danou URL neexistuje, vytvoří se nový.
     * @param string|null $rut URL adresa článku
     */
    public function actionEditor(string $rut = null)
    {
        if ($rut) {
            if (!($company = $this->companyManager->getCompany($rut)))
                $this->flashMessage('Firma nebyla nalezena.'); // Výpis chybové hlášky.
            else 
            {
                $this['editorForm']->setDefaults($company); // Předání hodnot článku do editačního formuláře.
                $this['editorForm']['datum_vytvoreni']->setDefaultValue($company->datum_vytvoreni->format('Y-m-d'));
            }
        }
    }

    /**
     * Vytváří a vrací formulář pro editaci článků.
     * @return Form formulář pro editaci článků
     */
    protected function createComponentEditorForm()
    {
        $helparr = array();

        // Vytvoření formuláře a definice jeho polí.
        $form = new Form;
        $form->addHidden('rut_id');
        array_push($helparr, $form->addInteger('ean', 'Ean'));
        $form->addText('nazev', 'Název')->setRequired();
        array_push($helparr, $form->addInteger('ic', 'IČ')->setRequired());
        array_push($helparr, $form->addInteger('dic', 'DIČ'));
        $form->addText('web', 'Web')->setRequired();
        $form->addEmail('email', 'Email')->setRequired();
        $form->addText('datum_vytvoreni', 'Datum Vytvoření')->setHtmlType('date')->setRequired();
        $form->addText('ulice', 'Ulice')->setRequired();
        $form->addText('cislo_p', 'Číslo popisné');
        $form->addText('cislo_o', 'Číslo orientační')->setRequired();
        $form->addText('obec', 'Obec')->setRequired();
        $form->addInteger('psc', 'PSČ')->setRequired();
        $form->addInteger('predcisli', 'Předčíslí');
        $form->addInteger('cislo_uctu', 'Číslo účtu')->setRequired();
        $kody_banky = $this->companyManager->get_enum_values();
        $form->addSelect('kod_banky', 'Kód Banky')->setItems($kody_banky)->setRequired();
        $form->addSubmit('save', 'Uložit článek');

        foreach($helparr as $unit)
        {
            if($this->user->isInRole('urednik') || $this->user->isInRole('reditel'))
            {
                $unit->setRequired();
            }else
            {
                $unit->setDisabled();
            }
        }
        // Funkce se vykonaná při úspěšném odeslání formuláře a zpracuje zadané hodnoty.
        $form->onSuccess[] = function (Form $form, ArrayHash $values) {
            try {
                $this->companyManager->saveCompany($values);
                $this->flashMessage('Článek byl úspěšně uložen.');
                if(isset($values->rut_id))
                {
                    $this->redirect('Company:', $values->rut_id);
                }else
                {
                    $this->redirect('Company:list');
                }
            } catch (UniqueConstraintViolationException $e) {
                $this->flashMessage('Firma s tímto ID již existuje.');
            }
        };

        return $form;
    }
}