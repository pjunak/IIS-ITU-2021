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
        $form->addInteger('id_vyrobniho_zdroje', 'ID výrobního zdroje')->setRequired();
        $form->addInteger('id_site', 'ID sítě')->setRequired();
        $form->addText('kratky_nazev', 'Krátký název')->setRequired();
        $form->addText('ulice', 'Ulice')->setRequired();
        $form->addText('cislo_p', 'Číslo popisné')->setRequired();
        $form->addText('cislo_o', 'Orientační číslo')->setRequired();
        $form->addInteger('kraj', 'Kraj')->setRequired();
        $form->addInteger('okres', 'Okres')->setRequired();
        $form->addText('obec', 'Obec')->setRequired();
        $form->addInteger('psc', 'PSČ')->setRequired();
        $form->addText('parcela', 'Parcela')->setRequired();
        $form->addText('gps_n', 'GPS N')->addRule(Form::FLOAT, 'Zadejte číslo')->setNullable()->setRequired();
        $form->addText('gps_e', 'GPS E')->addRule(Form::FLOAT, 'Zadejte číslo')->setNullable()->setRequired();
        $druhy_vyroben = $this->factoryManager->get_types_of_factory('druh_vyrobny');
        $form->addSelect('druh_vyrobny', 'Druh výrobny')->setItems($druhy_vyroben)->setRequired();
        $form->addInteger('vyrobni_EAN', 'Výrobní EAN')->setRequired();
        $form->addInteger('EAN_vyrobny', 'EAN výrobny')->setRequired();
        $form->addInteger('vykon_zdroje', 'Výkon zdroje')->setRequired();
        $napetove_hladiny = $this->factoryManager->get_types_of_factory('napetova_hladina');
        $form->addSelect('napetova_hladina', 'Napěťová hladina')->setItems($napetove_hladiny)->setRequired();
        $zpusoby_pripojeni = $this->factoryManager->get_types_of_factory('zpusob_pripojeni');
        $form->addSelect('zpusob_pripojeni', 'Způsob připojení')->setItems($zpusoby_pripojeni)->setRequired();
        $ano_ne = $this->factoryManager->get_types_of_factory('vykaz_za_opm');
        $form->addSelect('vykazy_za_opm', 'Výkaz za OPM')->setItems($ano_ne)->setRequired();
        $druhy_podpory = $this->factoryManager->get_types_of_factory('druh_podpory');
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