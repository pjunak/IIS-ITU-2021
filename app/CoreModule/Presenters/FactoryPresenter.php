<?php

declare(strict_types=1);

namespace App\CoreModule\Presenters;

use App\CoreModule\Model\FactoryManager;
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
class FactoryPresenter extends BasePresenter
{
    /** @var string URL výchozího článku. */
    private string $defaultFactoryId;

    /** @var FactoryManager Model pro správu s článků. */
    private FactoryManager $factoryManager;

    /** @var user Pro identifikaci uživatele */
    private $user;

    /**
     * Konstruktor s nastavením URL výchozího článku a injektovaným modelem pro správu článků.
     * @param string         $defaultFactoryId URL výchozího článku
     * @param FactoryManager $factoryManager    automaticky injektovaný model pro správu článků
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
     * Načte a předá článek do šablony podle jeho URL.
     * @param string|null $id URL článku
     * @throws BadReportException Jestliže článek s danou URL nebyl nalezen.
     */
    public function renderDefault(string $id = null)
    {
        if (!$id) $id = $this->defaultFactoryId; // Pokud není zadaná URL, vezme se URL výchozího článku.

        // Pokusí se načíst článek s danou URL a pokud nebude nalezen vyhodí chybu 404.
        if (!($factory = $this->factoryManager->getFactory($id)))
            $this->error(); // Vyhazuje výjimku BadReportException.

        $this->template->factory = $factory; // Předá článek do šablony.
    }

    /** Načte a předá seznam článků do šablony. */
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
     * Odstraní článek.
     * @param string|null $id URL článku
     * @throws AbortException
     */
    public function actionRemove(string $id = null)
    {
        $this->factoryManager->removeFactory($id);
        $this->flashMessage('Factory byl úspěšně odstraněn.');
        $this->redirect('Factory:list');
    }

    /**
     * Vykresluje formulář pro editaci článku podle zadané URL.
     * Pokud URL není zadána, nebo článek s danou URL neexistuje, vytvoří se nový.
     * @param string|null $id URL adresa článku
     */
    public function actionEditor(string $id = null)
    {
        if ($id) {
            if (!($factory = $this->factoryManager->getFactory($id)))
                $this->flashMessage('Výrobna nebyla nalezena.'); // Výpis chybové hlášky.
            else 
            {
                $this->template->factory = $factory;
                $this['editorForm']->setDefaults($factory); // Předání hodnot článku do editačního formuláře.
                $this['editorForm']['datum_prvniho_pripojeni']->setDefaultValue($factory->datum_prvniho_pripojeni->format('Y-m-d'));
                $this['editorForm']['datum_uvedeni_do_provozu']->setDefaultValue($factory->datum_uvedeni_do_provozu->format('Y-m-d'));
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
        $form->addInteger('id_site', 'ID sítě')->setHtmlAttribute('placeholder', '123456')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',11);
        $druhy_vyroben = $this->factoryManager->get_enum_values('druh_vyrobny');
        $form->addSelect('druh_vyrobny', 'Druh zdroje')->setItems($druhy_vyroben);
        $form->addInteger('vyrobni_EAN', 'Výrobní EAN')->setHtmlAttribute('placeholder', '123456')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',11);
        $form->addInteger('EAN_vyrobny', 'EAN výrobny')->setHtmlAttribute('placeholder', '123456')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',11);
        $form->addInteger('vykon_zdroje', 'Výkon zdroje')->setHtmlAttribute('placeholder', '7820')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',11);
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
        $form->addSelect('napetova_hladina', 'Napěťová hladina')->setItems($napetove_hladiny)->setRequired();
        $zpusoby_pripojeni = $this->factoryManager->get_enum_values('zpusob_pripojeni');
        $helper = $form->addSelect('zpusob_pripojeni', 'Způsob připojení')->setItems($zpusoby_pripojeni)->setRequired();
    
        $ano_ne = $this->factoryManager->get_enum_values('vykaz_za_opm');
        $form->addSelect('vykaz_za_opm', 'Výkaz za OPM')->setItems($ano_ne)->setRequired();

        //Kategorie ADRESA
        $form->addGroup('Adresa výrobny');
        $form->addText('ulice', 'Ulice')->setHtmlAttribute('placeholder', 'Ulicová')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',32);
        $form->addText('cislo_p', 'Číslo popisné')->setHtmlAttribute('placeholder', '123')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',8);
        $form->addText('cislo_o', 'Orientační číslo')->setHtmlAttribute('placeholder', 'BO-4a')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',8);
        $form->addInteger('kraj', 'Kraj')->setHtmlAttribute('placeholder', '1')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',11);
        $form->addInteger('okres', 'Okres')->setHtmlAttribute('placeholder', '1')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',11);
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
        $form->addGroup('Termíny');
        $form->addText('datum_prvniho_pripojeni', 'Datum prvního připojení')->setHtmlType('date');
        $form->addText('datum_uvedeni_do_provozu', 'Datum uvedení do provozu')->setHtmlType('date');

        $form->addSubmit('save', 'Registrovat výrobnu');

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