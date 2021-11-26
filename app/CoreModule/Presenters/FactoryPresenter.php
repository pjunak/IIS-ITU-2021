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
        $this->template->factories = $this->factoryManager->getFactories();
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
        $form->addHidden('id');
        $form->addInteger('id_vyrobniho_zdroje', 'ID výrobního zdroje')->setRequired()->setHtmlAttribute('placeholder', '123456')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',11);;
        $form->addInteger('id_site', 'ID sítě')->setRequired()->setHtmlAttribute('placeholder', '123456')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',11);
        $form->addText('kratky_nazev', 'Krátký název')->setRequired()->setHtmlAttribute('placeholder', 'Moje výrobna')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',64);;
        $form->addText('ulice', 'Ulice')->setRequired()->setHtmlAttribute('placeholder', 'Ulicová')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',32);
        $form->addText('cislo_p', 'Číslo popisné')->setHtmlAttribute('placeholder', '123')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',8);
        $form->addText('cislo_o', 'Orientační číslo')->setHtmlAttribute('placeholder', 'BO-4a')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',8);
        $form->addInteger('kraj', 'Kraj')->setRequired()->setHtmlAttribute('placeholder', '1')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',11);
        $form->addInteger('okres', 'Okres')->setRequired()->setHtmlAttribute('placeholder', '1')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',11);
        $form->addText('obec', 'Obec')->setRequired()->setHtmlAttribute('placeholder', 'Obcov')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',32);
        $form->addInteger('psc', 'PSČ')->setRequired()->setHtmlAttribute('placeholder', '11100')->addRule($form::LENGTH, 'Délka %label je %d',6);
        $form->addText('parcela', 'Parcela')->setRequired()->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',16);
        $form->addText('gps_n', 'GPS N')->addRule(Form::FLOAT, 'Zadejte číslo')->setNullable()->setRequired()->setHtmlAttribute('placeholder', '345,123')->addRule($form::RANGE, 'Délka %label je od %d do %d',[5,21]);
        $form->addText('gps_e', 'GPS E')->addRule(Form::FLOAT, 'Zadejte číslo')->setNullable()->setRequired()->setHtmlAttribute('placeholder', '123,123')->addRule($form::RANGE, 'Délka %label je od %d do %d',[5,21]);
        $druhy_vyroben = $this->factoryManager->get_enum_values('druh_vyrobny');
        $form->addSelect('druh_vyrobny', 'Druh výrobny')->setItems($druhy_vyroben)->setRequired();
        $form->addInteger('vyrobni_EAN', 'Výrobní EAN')->setRequired()->setHtmlAttribute('placeholder', '123456')->addRule($form::RANGE, 'Délka %label je %d',[3,11]);
        $form->addInteger('EAN_vyrobny', 'EAN výrobny')->setRequired()->setHtmlAttribute('placeholder', '123456')->addRule($form::RANGE, 'Délka %label je mezi %d a %d',[3,11]);
        $form->addInteger('vykon_zdroje', 'Výkon zdroje')->setRequired()->setHtmlAttribute('placeholder', '7820')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',11);
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
        print_r($form['zpusob_pripojeni'].getValue());
        
        $ano_ne = $this->factoryManager->get_enum_values('vykaz_za_opm');
        $form->addSelect('vykaz_za_opm', 'Výkaz za OPM')->setItems($ano_ne)->setRequired();
        $druhy_podpory = $this->factoryManager->get_enum_values('druh_podpory');
        $form->addSelect('druh_podpory', 'Druh podpory')->setItems($druhy_podpory)->setRequired();
        $form->addText('datum_prvniho_pripojeni', 'Datum prvního připojení')->setHtmlType('date')->setRequired();
        $form->addText('datum_uvedeni_do_provozu', 'Datum uvedení do provozu')->setHtmlType('date')->setRequired();
        $form->addSubmit('save', 'Uložit firmu');

        // Funkce se vykonaná při úspěšném odeslání formuláře a zpracuje zadané hodnoty.
        $form->onSuccess[] = function (Form $form, ArrayHash $values) {
            try {
                $this->factoryManager->saveFactory($values);
                $this->flashMessage('Firma byla úspěšně uložena.');
                if(isset($values->id))
                {
                    $this->redirect('Factory:', $values->id);
                }else
                {
                    $this->redirect('Factory:list');
                }
            } catch (UniqueConstraintViolationException $e) {
                $this->flashMessage('Firma s tímto ID již existuje.');
            }
        };

        return $form;
    }
}