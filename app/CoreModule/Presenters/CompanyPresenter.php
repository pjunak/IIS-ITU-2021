<?php

/*
//	Projekt do předmětu ITU - Zákaznický portál OTE, a.s.
//	Datum: 5.12.2021
//	Autor: Dalibor Čásek, xcasek01
*/

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
use Nette\Utils\DateTime;
use Nette\Utils\Html;
/**
 * Presenter pro vykreslování firem.
 * @package App\CoreModule\Presenters
 */
class CompanyPresenter extends BasePresenter
{
    /** @var string URL výchozí firmy. */
    private string $defaultCompanyRut;

    /** @var CompanyManager Model pro správu s firem. */
    private CompanyManager $companyManager;

    /** @var user Pro identifikaci uživatele */
    private $user;

    /**
     * Konstruktor s nastavením URL výchozí firmy a injektovaným modelem pro správu firem.
     * @param string         $defaultCompanyRut URL výchozí firmy
     * @param CompanyManager $companyManager    automaticky injektovaný model pro správu firem
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
     * Načte a předá firmu do šablony podle jeho URL.
     * @param string|null $rut URL firmy
     * @throws BadRequestException Jestliže firma s danou URL nebyla nalezen.
     */
    public function renderDefault(string $rut = null)
    {
        if (!$rut) $rut = $this->defaultCompanyRut; // Pokud není zadaná URL, vezme se URL výchozíh firmy.

        // Pokusí se načíst firmu s danou URL a pokud nebude nalezen vyhodí chybu 404.
        if (!($company = $this->companyManager->getCompany($rut)))
            $this->error(); // Vyhazuje výjimku BadRequestException.

        $this->template->company = $company; // Předá firmu do šablony.
        $this->template->companyUsers = $this->companyManager->getCompanyUsers($rut);
        $this->template->otherUsers = $this->companyManager->getotherUsers($rut);
    }

    /** Načte a předá seznam firem do šablony. */
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

    /*
    public function actionKontrolaDostupnosti($factoryID)
    {
        if (in_array($factoryID, $this->companyManager->getCompaniesByUser($this->user->getID($this->user->getID()))
        {
            $this->template->aviable = false;
        }else
        {
            return FALSE;
        }
    }
    */

    /**
     * Odstraní firmu.
     * @param string|null $rut firmy
     * @throws AbortException
     */
    public function actionRemove(string $rut = null)
    {
        $this->companyManager->removeCompany($rut);
        $this->flashMessage('Firma byla úspěšně odstraněn.');
        $this->redirect('Company:list');
    }

    /**
     * Vykresluje formulář pro editaci firmy podle zadané URL.
     * Pokud URL není zadána, nebo formu s danou URL neexistuje, vytvoří se nový.
     * @param string|null $rut URL adresa firmy
     */
    public function actionEditor(string $rut = null)
    {
        if ($rut) {
            if (!($company = $this->companyManager->getCompany($rut)))
                $this->flashMessage('Firma nebyla nalezena.'); // Výpis chybové hlášky.
            else 
            {
                $this->template->company = $company;
                $this['editorForm']->setDefaults($company); // Předání hodnot firmy do editačního formuláře.
                $this['editorForm']['datum_vytvoreni']->setDefaultValue($company->datum_vytvoreni->format('Y-m-d'));
            }
        }
    }

    /**
     * Fnkce pro přidávání osoby do firmy
     * @param string|null $rut firmy
     * @throws AbortException
     */
    public function actionAdd(string $rut = null)
    {
        $this->renderDefault($rut);  
        if ($rut) {
            if (!($company = $this->companyManager->getCompany($rut)))
                $this->flashMessage('Firma nebyla nalezena.'); // Výpis chybové hlášky.
            else 
            {
                $this->template->company = $company;
            }
        }
    }

    /**
     * Přidá zživatele do firmy
     * @param string $rut ID firmy
     * @param string $id ID uživatele
     */
    public function handleAddUser(string $rut = null, string $id = null)
    {
        $this->companyManager->addUserToCompany($rut, $id);
        $this->handleUpdate($rut);
        $this->flashMessage('Uživatel úspěšně přidán do firmy.');
    }

    /**
     * Odebere uživatele z firmy
     * @param string $rut ID firmy
     * @param string $id ID uživatele
     */
    public function handleRemoveUser(string $rut = null, string $id = null)
    {
        $this->companyManager->removeUserFromCompany($rut, $id);
        $this->handleUpdate($rut);
        $this->flashMessage('Uživatel úspěšně odebrán z firmy.');
    }

    /**
     * Obnoví seznam uživatelů ve firmě a mimo firmu
     * @param string $rut ID firmy
     */
    public function handleUpdate(string $rut = null)
	{
		$this->template->companyUsers = $this->companyManager->getCompanyUsers($rut);
        $this->template->otherUsers = $this->companyManager->getotherUsers($rut);
        $this->redrawControl('companyUsers'); // TODO - zdá se, že z nějakého důvodu není potřeba
        $this->redrawControl('otherUsers'); // TODO - zdá se, že z nějakého důvodu není potřeba
	}

    /**
     * Vytváří a vrací formulář pro editaci firem.
     * @return Form formulář pro editaci firem
     */
    protected function createComponentEditorForm()
    {
        $helparr = array();

        // Vytvoření formuláře a definice jeho polí.
        $form = new Form;
        $form->addGroup('Základní údaje');
        $form->addHidden('rut_id');
        array_push($helparr, $form->addInteger('ean', Html::el()->setHtml('EAN <span data-toggle="tooltip" data-placement="top" title="Kód EAN (European Article Number) je mezinárodní číslo obchodní doložky. Najdete ho ve faktuře u adresy odběrného místa. Slouží k jednoznačné identifikaci odběrného místa, resp. místa spotřeby energie. Jde o 18místné číslo. "><i class="fas fa-info-circle"></i></span>'))
        ->setHtmlAttribute('placeholder', '123456789')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',18));
        $form->addText('nazev', 'Název')->setRequired('%label je nutné vyplnit')->setHtmlAttribute('placeholder', 'Jméno firmy')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',128);
        array_push($helparr, $form->addInteger('ic', Html::el()->setHtml('IČ <span data-toggle="tooltip" data-placement="top" title="Identifikační číslo osoby (správně zkratkou IČO, někdy také uváděno IČ) je v České republice unikátní osmimístné identifikační číslo právnické osoby, podnikající fyzické osoby nebo organizační složky státu."><i class="fas fa-info-circle"></i></span>'))
        ->setHtmlAttribute('placeholder', '12345678')->addRule($form::LENGTH, 'Délka %label je %d',8));
        $form->addtext('dic', 'DIČ')->setHtmlAttribute('placeholder', '12345678')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',11);
        $form->addText('web', 'Web')->setHtmlAttribute('placeholder', 'www.mujweb.cz')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',64);
        $form->addEmail('email', 'Email')->setHtmlAttribute('placeholder', 'muj.email@email.cz')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',64);
        $dateTime = new DateTime;
        $date = $dateTime->format('Y-m-d');
        $form->addText('datum_vytvoreni', 'Datum Vytvoření')->setHtmlType('date')->setDefaultValue($date)->setRequired('%label je nutné vyplnit');

        $form->addGroup('Adresa firmy');
        $form->addText('ulice', 'Ulice')->setHtmlAttribute('placeholder', 'Ulicová')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',32);
        $form->addText('cislo_p', 'Číslo popisné')->setHtmlAttribute('placeholder', '123-b')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',8);
        $form->addText('cislo_o', 'Číslo orientační')->setHtmlAttribute('placeholder', '123-bo4')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',8);
        $form->addText('obec', 'Obec')->setHtmlAttribute('placeholder', 'Brno')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',32);
        $form->addInteger('psc', 'PSČ')->setHtmlAttribute('placeholder', '77700')->addRule($form::LENGTH, 'Délka %label je %d',5);

        $form->addGroup('Bankovní spojení');
        $form->addInteger('predcisli', Html::el()->setHtml('Předčíslí <span data-toggle="tooltip" data-placement="top" title="Ne všechny účty mají předčíslí."><i class="fas fa-info-circle"></i></span>'))
        ->setHtmlAttribute('placeholder', '000000')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',6);
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
            }else
            {
                $unit->setDisabled();
            }
        }
        // Funkce se vykonaná při úspěšném odeslání formuláře a zpracuje zadané hodnoty.
        $form->onSuccess[] = function (Form $form, ArrayHash $values) {
            try {
                $this->companyManager->saveCompany($values);
                $this->flashMessage('Firma byla úspěšně uložena.');
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