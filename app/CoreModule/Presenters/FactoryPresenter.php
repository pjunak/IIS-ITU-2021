<?php

/*
//	Projekt do předmětu ITU - Zákaznický portál OTE, a.s.
//	Datum: 5.12.2021
//	Autor: Dalibor Čásek, xcasek01
*/

declare(strict_types=1);

namespace App\CoreModule\Presenters;

use App\CoreModule\Model\FactoryManager;
use App\Presenters\BasePresenter;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Database\UniqueConstraintViolationException;
use Nette\Utils\ArrayHash;
use Nette\Utils\DateTime;
use Nette\Utils\Html;

/**
 * Presenter pro vykreslování výroben.
 * @package App\CoreModule\Presenters
 */
class FactoryPresenter extends BasePresenter
{
    /** @var string URL výchozí výrobny. */
    private string $defaultFactoryId;

    /** @var FactoryManager Model pro správu výroben. */
    private FactoryManager $factoryManager;

    /** @var user Pro identifikaci uživatele */
    private $user;

    /**
     * Konstruktor s nastavením URL výchozí výrobny a injektovaným modelem pro správu výroben.
     * @param string         $defaultFactoryId URL výchozí výrobny
     * @param FactoryManager $factoryManager    automaticky injektovaný model pro správu výroben
     */
    public function __construct(string $defaultFactoryId, FactoryManager $factoryManager)
    {
        parent::__construct();
        $this->defaultFactoryId = $defaultFactoryId;
        $this->factoryManager = $factoryManager;
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
     * Načte a předá výrobnu do šablony podle jeho URL.
     * @param string|null $id URL výrobny
     * @throws BadReportException Jestliže výrobna s danou URL nebyl nalezena.
     */
    public function renderDefault(string $id = null)
    {
        if (!$id) $id = $this->defaultFactoryId; // Pokud není zadaná URL, vezme se URL výchozí výrobny.

        // Pokusí se načíst výrobnu s danou URL a pokud nebude nalezen vyhodí chybu 404.
        if (!($factory = $this->factoryManager->getFactory($id)))
            $this->error(); // Vyhazuje výjimku BadReportException.

        $this->template->factory = $factory; // Předá výrobnu do šablony.
        $this->template->nazevVlastniciFirmy = $this->factoryManager->getNazevVlastniciFirmy($factory->id_firmy);
    }

    /** Načte a předá seznam výroben do šablony. */
    public function renderList()
    {
        $result = array();
        if ($this->user->isInRole('disponent'))
        {
            $this->template->factories = $this->factoryManager->getFactoriesWhereUser($this->user->id);
        }
        else
        {
            $this->template->factories = $this->factoryManager->getFactories();
        }
    }

    /**
     * Odstraní Výrobnu.
     * @param string|null $id URL výrobny
     * @throws AbortException
     */
    public function handleRemove(string $id = null)
    {
        //$this->factoryManager->removeFactory($id);
        $this->flashMessage('Výrobna byla úspěšně odstraněna.');
        $this->redirect('Factory:list');
    }

    /**
     * Vykresluje formulář pro editaci výrobny podle zadané URL.
     * Pokud URL není zadána, nebo výrobna s danou URL neexistuje, vytvoří se novou.
     * @param string|null $id URL adresa výrobny
     */
    public function actionEditor(string $id = null)
    {
        if ($id) {
            if (!($factory = $this->factoryManager->getFactory($id)))
                $this->flashMessage('Výrobna nebyla nalezena.'); // Výpis chybové hlášky.
            else 
            {
                $this->template->factory = $factory;
                $this['editorForm']->setDefaults($factory); // Předání hodnot výrobny do editačního formuláře.
                $this['editorForm']['datum_prvniho_pripojeni']->setDefaultValue($factory->datum_prvniho_pripojeni->format('Y-m-d'));
                $this['editorForm']['datum_uvedeni_do_provozu']->setDefaultValue($factory->datum_uvedeni_do_provozu->format('Y-m-d'));
                $this['editorForm']['stav']->setDefaultValue('podano');
            }
        }
    }

    /**
     * Změní stav výrobny na požadovaný
     * @param string $factoryID ID výrobny
     * @param string $stav stav
     */
    public function actionZmenStavVyrobny($factoryID, $stav)
    {
        $this->factoryManager->updateStavVyrobny($factoryID, $stav);
        $this->redirect('Factory:list');
    }

    
    /**
     * Vytváří a vrací formulář pro editaci výroben.
     * @return Form formulář pro editaci výroen
     */
    protected function createComponentEditorForm()
    {
        // Vytvoření formuláře a definice jeho polí.
        $form = new Form;
        $form->addHidden('stav', 'Stav')->setValue('podano');
        
        //kategorie SPOLEČNOST
        $form->addGroup('Společnost');
        $seznamDostupnychFirem = $this->factoryManager->getSeznamDostupnychFirem($this->user->getID());
        $form->addSelect('id_firmy', 'Výrobna patří ke společnosti:')->setItems($seznamDostupnychFirem)->setRequired();

        //kategorie DRUH PODPORY
        $form->addGroup('Data podpory');
        //minimálně schváleno/neschávleno

        //kategorie VÝROBNÍ ZDROJ
        $form->addGroup('Výrobní zdroj');
        $form->addText('kratky_nazev', 'Krátký název')->setRequired()->setHtmlAttribute('placeholder', 'Moje výrobna')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',64);;
        $form->addHidden('id');
        $form->addInteger('id_vyrobniho_zdroje', 'ID výrobního zdroje')->setHtmlAttribute('placeholder', '123456')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',11);;
        $form->addInteger('id_site', Html::el()->setHtml('ID sítě <span data-toggle="tooltip" data-placement="top" title="ID sítě nalzenete v dokumentech od Vašeho lokálního distributora elektřiny."><i class="fas fa-info-circle"></i></span>'))->setHtmlAttribute('placeholder', '123456')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',11);
        $druhy_vyroben = $this->factoryManager->get_enum_values('druh_vyrobny');
        $form->addSelect('druh_vyrobny', 'Druh zdroje')->setItems($druhy_vyroben);
        $form->addInteger('vyrobni_EAN', Html::el()->setHtml('Výrobní EAN <span data-toggle="tooltip" data-placement="top" title="Kód EAN (European Article Number) je mezinárodní číslo obchodní doložky. Najdete ho ve faktuře u adresy odběrného místa. Slouží k jednoznačné identifikaci odběrného místa, resp. místa spotřeby energie. Jde o 18místné číslo. "><i class="fas fa-info-circle"></i></span>'))
        ->setHtmlAttribute('placeholder', '123456')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',18);
        $form->addInteger('EAN_vyrobny', Html::el()->setHtml('EAN výrobny <span data-toggle="tooltip" data-placement="top" title="Kód EAN (European Article Number) je mezinárodní číslo obchodní doložky. Najdete ho ve faktuře u adresy odběrného místa. Slouží k jednoznačné identifikaci odběrného místa, resp. místa spotřeby energie. Jde o 18místné číslo. "><i class="fas fa-info-circle"></i></span>'))
        ->setHtmlAttribute('placeholder', '123456')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',18);
        $form->addInteger('vykon_zdroje', 'Výkon zdroje')->setHtmlAttribute('placeholder', '7820')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',11)->setOption('description', Html::el('span class="jednotky"')
		->setHtml('&nbsp;W'));
        $napetove_hladiny = [
            '0,4' => '0,4',
            '3' => '3',
            '6' => '6',
            '10' => '10',
            '22' => '22',
            '35' => '35',
            '110' => '110',
            '220' => '220',
            '400' => '400',
            'ostatní' => 'ostatní'
        ];
        $form->addSelect('napetova_hladina', 'Napěťová hladina')->setItems($napetove_hladiny)->setRequired()->setOption('description', Html::el('span class="jednotky"')
		->setHtml('&nbsp;kV'));
        $zpusoby_pripojeni = $this->factoryManager->get_enum_values('zpusob_pripojeni');
        $form->addSelect('zpusob_pripojeni', 'Způsob připojení')->setItems($zpusoby_pripojeni)->setRequired();
    
        $ano_ne = $this->factoryManager->get_enum_values('vykaz_za_opm');
        $form->addSelect('vykaz_za_opm', 'Výkaz za OPM')->setItems($ano_ne)->setRequired();

        //Kategorie ADRESA
        $form->addGroup('Adresa výrobny');
        $form->addText('ulice', 'Ulice')->setHtmlAttribute('placeholder', 'Ulicová')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',32);
        $form->addText('cislo_p', 'Číslo popisné')->setHtmlAttribute('placeholder', '123')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',8);
        $form->addText('cislo_o', 'Orientační číslo')->setHtmlAttribute('placeholder', 'BO-4a')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',8);
        $form->addText('kraj', 'Kraj')->setHtmlAttribute('placeholder', 'Olomoucký kraj')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',32);
        $form->addText('okres', 'Okres')->setHtmlAttribute('placeholder', 'Olomouc')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',32);
        $form->addText('obec', 'Obec')->setHtmlAttribute('placeholder', 'Obcov')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',32);
        $form->addInteger('psc', 'PSČ')->setHtmlAttribute('placeholder', '11100')->addRule($form::LENGTH, 'Délka %label je %d',5);
        $form->addText('parcela', 'Parcela')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',16);
        $form->addText('gps_n', 'GPS N')->addRule(Form::FLOAT, 'Zadejte číslo')->setNullable()->setHtmlAttribute('placeholder', '345,123')->addRule($form::RANGE, 'Délka %label je od %d do %d',[5,21]);
        $form->addText('gps_e', 'GPS E')->addRule(Form::FLOAT, 'Zadejte číslo')->setNullable()->setHtmlAttribute('placeholder', '123,123')->addRule($form::RANGE, 'Délka %label je od %d do %d',[5,21]);

        //kategorie DRUH PODPORY
        $form->addGroup('Druh podpory');
        $druhy_podpory = $this->factoryManager->get_enum_values('druh_podpory');
        $form->addSelect('druh_podpory', 'Druh podpory')->setItems($druhy_podpory)->setRequired();

        //kategorie TERMÍNY
        $dateTime = new DateTime;
        $date = $dateTime->format('Y-m-d');
        $form->addGroup('Termíny');
        $form->addText('datum_prvniho_pripojeni', 'Datum prvního připojení')->setHtmlType('date')->setDefaultValue($date)->setRequired('%label je nutné vyplnit');
        $form->addText('datum_uvedeni_do_provozu', 'Datum uvedení do provozu')->setHtmlType('date')->setDefaultValue($date)->setRequired('%label je nutné vyplnit');

        //$form->addSubmit('save', 'Registrovat výrobnu');
        $form->addSubmit('save', 'Registrovat výrobnu')->getControlPrototype()->setName('button')->setHtml('Registrovat výrobnu&nbsp;&nbsp;<i class="fa fa-save fa-lg"></i>')->setAttribute('class', 'button');

        // Funkce se vykonaná při úspěšném odeslání formuláře a zpracuje zadané hodnoty.
        $form->onSuccess[] = function (Form $form, ArrayHash $values) {
            try {
                $this->factoryManager->saveFactory($values);
                $this->flashMessage('Výrobna byla úspěšně uložena.');
                if(isset($values->id))
                {
                    $this->redirect('Factory:', $values->id);
                }else
                {
                    $this->redirect('Factory:list');
                }
            } catch (UniqueConstraintViolationException $e) {
                $this->flashMessage('Výrobna s tímto ID již existuje.');
            }
        };

        return $form;
    }
}