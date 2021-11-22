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
        $this->template->companies = $this->companyManager->getCompanies();
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
                $this->flashMessage('Článek nebyl nalezen.'); // Výpis chybové hlášky.
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
        // Vytvoření formuláře a definice jeho polí.
        $form = new Form;
        $form->addHidden('rut_id');
        $form->addInteger('ean', 'Ean')->setRequired();
        $form->addText('nazev', 'Název')->setRequired();
        $typy_firem = [
            'vitr' => 'Větrná elektrárna',
            'voda' => 'Vodní elektrárna',
            'test' => 'Solární elektrárna',
        ];
        $form->addSelect('typ_firmy', 'Typ Firmy:', $typy_firem)->setDefaultValue('test')->setRequired();
        $form->addInteger('ic', 'IČ')->setRequired();
        $form->addText('web', 'Web')->setRequired();
        $form->addEmail('email', 'Email')->setRequired();
        $form->addText('datum_vytvoreni', 'Datum Vytvoření')->setHtmlType('date')->setRequired();
        $form->addText('ulice', 'Ulice')->setRequired();
        $form->addText('cislo_p', 'Číslo popisné')->setRequired();
        $form->addText('cislo_o', 'Číslo orientační')->setRequired();
        $form->addText('obec', 'Obec')->setRequired();
        $form->addInteger('psc', 'PSČ')->setRequired();
        $form->addInteger('predcisli', 'Předčíslí')->setRequired();
        $form->addInteger('cislo_uctu', 'Číslo účtu')->setRequired();
        $kody_banky = $this->companyManager->get_enum_values();
        $form->addSelect('kod_banky', 'Kód Banky')->setItems($kody_banky)->setRequired();
        $form->addSubmit('save', 'Uložit článek');

        // Funkce se vykonaná při úspěšném odeslání formuláře a zpracuje zadané hodnoty.
        $form->onSuccess[] = function (Form $form, ArrayHash $values) {
            try {
                $this->companyManager->saveCompany($values);
                $this->flashMessage('Článek byl úspěšně uložen.');
                $this->redirect('Company:', $values->rut_id);
            } catch (UniqueConstraintViolationException $e) {
                $this->flashMessage('Článek s touto URL adresou již existuje.');
            }
        };

        return $form;
    }
}