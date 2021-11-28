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
        $this->template->companyUsers = $this->companyManager->getCompanyUsers($rut);
    }

    /** Načte a předá seznam článků do šablony. */
    public function renderList()
    {
        if($this->user->isInRole('disponent'))
        {
            $this->template->companies = $this->companyManager->getCompaniesByUser($this->user->getID());
        }
        else
        {
            $this->template->companies = $this->companyManager->getCompaniesByUser(NULL);
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
                $this->template->company = $company;
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
        $form->addGroup('Základní údaje');
        $form->addHidden('rut_id');
        array_push($helparr, $form->addInteger('ean', 'Ean')->setHtmlAttribute('placeholder', '123456789')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',11));
        $form->addText('nazev', 'Název')->setRequired('%label je nutné vyplnit')->setHtmlAttribute('placeholder', 'Jméno firmy')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',128);
        array_push($helparr, $form->addInteger('ic', 'IČ')->setHtmlAttribute('placeholder', '12345678')->addRule($form::LENGTH, 'Délka %label je %d',8));
        $form->addInteger('dic', 'DIČ')->setHtmlAttribute('placeholder', '12345678')->addRule($form::LENGTH, 'Maximální délka %label je %d',8);
        $form->addText('web', 'Web')->setRequired('%label je nutné vyplnit')->setHtmlAttribute('placeholder', 'www.mujweb.cz')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',64);
        $form->addEmail('email', 'Email')->setRequired('%label je nutné vyplnit')->setHtmlAttribute('placeholder', 'muj.email@email.cz')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',64);
        $form->addText('datum_vytvoreni', 'Datum Vytvoření')->setHtmlType('date')->setRequired('%label je nutné vyplnit');

        $form->addGroup('Adresa firmy');
        $form->addText('ulice', 'Ulice')->setRequired('%label je nutné vyplnit')->setHtmlAttribute('placeholder', 'Ulicová')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',32);
        $form->addText('cislo_p', 'Číslo popisné')->setHtmlAttribute('placeholder', '123-b')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',8);
        $form->addText('cislo_o', 'Číslo orientační')->setHtmlAttribute('placeholder', '123-bo4')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',8);
        $form->addText('obec', 'Obec')->setRequired('%label je nutné vyplnit')->setHtmlAttribute('placeholder', 'Brno')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',32);
        $form->addInteger('psc', 'PSČ')->setRequired('%label je nutné vyplnit')->setHtmlAttribute('placeholder', '77700')->addRule($form::LENGTH, 'Délka %label je %d',5);

        $form->addGroup('Bankovní spojení');
        $form->addInteger('predcisli', 'Předčíslí')->setHtmlAttribute('placeholder', '000000')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',6);
        $form->addInteger('cislo_uctu', 'Číslo účtu')->setRequired('%label je nutné vyplnit')->setHtmlAttribute('placeholder', '1234567890')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',10);
        $kody_banky = [
            '0100' => '0100',
            '0300' => '0300',
            '0600' => '0600',
            '0710' => '0710',
            '0800' => '0800',
            '2010' => '2010',
            '2070' => '2070',
            '2100' => '2100',
            '2250' => '2250',
            '2260' => '2260',
            '2600' => '2600',
            '2700' => '2700',
            '3030' => '3030',
            '3040' => '3040',
            '3050' => '3050',
            '3500' => '3500',
            '4000' => '4000',
            '4300' => '4300',
            '5500' => '5500',
            '5800' => '5800',
            '6000' => '6000',
            '6100' => '6100',
            '6210' => '6210',
            '6300' => '6300',
            '6700' => '6700',
            '6800' => '6800',
            '7950' => '7950',
            '7960' => '7960',
            '7970' => '7970',
            '7980' => '7980',
            '7990' => '7990',
            '8060' => '8060',
            '8090' => '8090',
            '8211' => '8211'
        ];
        
        $form->addSelect('kod_banky', 'Kód Banky')->setItems($kody_banky)->setRequired('%label je nutné vyplnit');
        $form->addSubmit('save', 'Uložit úpravy');

        foreach($helparr as $unit)
        {
            if($this->user->isInRole('urednik') || $this->user->isInRole('reditel'))
            {
                $unit->setRequired('%label je nutné vyplnit');
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