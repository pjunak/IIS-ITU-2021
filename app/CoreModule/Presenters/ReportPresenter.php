<?php

/*
//	Projekt do předmětu ITU - Zákaznický portál OTE, a.s.
//	Datum: 5.12.2021
//	Autor: Petr Junák, xjunak01
*/

declare(strict_types=1);

namespace App\CoreModule\Presenters;

use App\CoreModule\Model\ReportManager;
use App\Presenters\BasePresenter;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Database\UniqueConstraintViolationException;
use Nette\Utils\ArrayHash;
use Nette\Utils\DateTime;
use Nette\Utils\Html;
use Nette;

/**
 * Presenter pro vykreslování výkazů.
 * @package App\CoreModule\Presenters
 */
class ReportPresenter extends BasePresenter
{
    /** @var string URL výchozího výkazu. */
    private string $defaultReportId;

    /** @var ReportManager Model pro správu s výkazů. */
    private ReportManager $reportManager;

    /** @var user Pro identifikaci uživatele */
    private $user;

    /** @var vybrana_vyrobna Pro identifikaci uživatele */
    private $vybrana_vyrobna = 0;

    private $anyVariable;

    /**
     * Konstruktor s nastavením URL výchozího výkazu a injektovaným modelem pro správu výkazů.
     * @param string         $defaultReportId URL výchozího výkazu
     * @param ReportManager $reportManager    automaticky injektovaný model pro správu výkazů
     */
    public function __construct(string $defaultReportId, ReportManager $reportManager)
    {
        parent::__construct();
        $this->defaultReportId = $defaultReportId;
        $this->reportManager = $reportManager;
    }

    public function startup()
    {
        parent::startup();
        $this->user = $this->getUser();
        if (!$this->user->isLoggedIn())
        {
            $this->redirect(':Sign:in');
        }
        // Pro nastavení vchozí zobrazené výrobny
        $seznam_vyroven = $this->reportManager->get_factories($this->user);
        if ($seznam_vyroven != NULL)
        {
            $this->vybrana_vyrobna = array_key_first($seznam_vyroven);
        }
    }
    
    
    /**
     * Načte a předá výkaz do šablony podle jeho URL.
     * @param string|null $id URL výkazu
     * @throws BadReportException Jestliže výkaz s danou URL nebyl nalezen.
     */
    public function renderDefault(string $id = null)
    {
        if (!$id) $id = $this->defaultReportId; // Pokud není zadaná URL, vezme se URL výchozího výkazu.

        // Pokusí se načíst výkaz s danou URL a pokud nebude nalezen vyhodí chybu 404.
        if (!($report = $this->reportManager->getReport($id)))
            $this->error(); // Vyhazuje výjimku BadReportException.

        $this->template->report = $report; // Předá výkaz do šablony.
    }

    /** Načte a předá seznam výkazů do šablony. */
    public function renderList()
    {
        $this->template->reports = $this->reportManager->getReportsWhereFactory($this->vybrana_vyrobna);
        $seznam_vyroven = $this->reportManager->get_factories($this->user);
        if ($seznam_vyroven == NULL)
        {
            $this->template->vyrobna_exists = false;
        } else
        {
            $this->template->vyrobna_exists = true;     
        }

        if ($this->anyVariable === NULL) {
            $this->anyVariable = 'default value';
        }
        $this->template->anyVariable = $this->anyVariable;
    }

    /**
     * Odstraní výkaz.
     * @param string|null $id URL výkazu
     * @throws AbortException
     */
    public function actionRemove(string $id = null)
    {
        $this->reportManager->removeReport($id);
        $this->flashMessage('Report byl úspěšně odstraněn.');
        $this->redirect('Report:list');
    }

    /**
     * Vykresluje formulář pro editaci výkazu podle zadané URL.
     * Pokud URL není zadána, nebo výkaz s danou URL neexistuje, vytvoří se nový.
     * @param string|null $id URL adresa výkazu
     */
    public function actionEditor(string $id = null)
    {
        if ($id) {
            if (!($report = $this->reportManager->getReport($id)))
                $this->flashMessage('Výkaz nebyl nalezen.'); // Výpis chybové hlášky.
            else 
            {
                $this['editorForm']->setDefaults($report); // Předání hodnot výkazu do editačního formuláře.
                $this['editorForm']['od']->setDefaultValue($report->od->format('Y-m-d'));
                $this['editorForm']['do']->setDefaultValue($report->do->format('Y-m-d'));
                $date = new DateTime;
                $this['editorForm']['datum_cas_zadani_vykazu']->setDefaultValue($date);
            }
        }
    }

    /**
     * Po zavolání vloží do proměnné $this->vybrana_vyrobna údaj z dropdown menu
     * @param value hodnota z dropdown menu poslaná AJAX voláním
     */
    public function handleVykazy($value)
    {
        $this->user = $this->getUser();
        $this->vybrana_vyrobna = $value; // uložení hodnoty do vybrane_vyrobny
        $this->redrawControl('ajaxRedraw'); // invalidace a překreslení výřezu s výkazy
    }

    /**
     * Vytváří dropdown menu se všemi výrobnami daného uživatele.
     * @return Form formulář pro editaci výkazů
     */
    protected function createComponentDropdownVyrobny()
    {
        // Vytvoření formuláře a definice jeho polí.
        $form = new Form;

        $seznam_vyroven = $this->reportManager->get_factories($this->user);
        if ($seznam_vyroven == NULL)
        {
            echo 'Nemáte žádnou výrobnu a tedy ani žádné výkazy.';
        }
        else
        {
            $form->addSelect('vyrobna', 'Název výrobny')->setItems($seznam_vyroven)->setRequired()
            ->setAttribute('class', 'ajax')->setAttribute('id', 'testAja');            
        }
        return $form;
    }

    /**
     * Vytváří a vrací formulář pro editaci výkazů.
     * @return Form formulář pro editaci výkazů
     */
    protected function createComponentEditorForm()
    {
        // Vytvoření formuláře a definice jeho polí.
        $form = new Form;
        $form->addHidden('id');
        $form->addHidden('id_osoby')->setRequired()->setDefaultValue($this->user->id);
        $form->addHidden('id_vyrobny')->setRequired()->setDefaultValue($this->vybrana_vyrobna);
        $dateTime = new DateTime;
        $date = $dateTime->format('Y-m-d');
        $form->addText('od', Html::el()->setHtml('Od <span data-toggle="tooltip" data-placement="top" title="První den měsíce, ve kterém byla vrobna v provozu"><i class="fas fa-info-circle"></i></span>'))
        ->setHtmlType('date')->setDefaultValue($date)->setRequired('%label je nutné vyplnit');
        $form->addText('do', Html::el()->setHtml('Do <span data-toggle="tooltip" data-placement="top" title="Poslední den měsíce, ve kterém byla výrobna v provozu"><i class="fas fa-info-circle"></i></span>'))
        ->setHtmlType('date')->setDefaultValue($date)->setRequired('%label je nutné vyplnit');
        $form->addHidden('datum_cas_zadani_vykazu')->setDefaultValue($dateTime)->setRequired(); // Je nastaveno automaticky podle aktuálního data a času
        $form->addInteger('svorkova_vyroba_elektriny', Html::el()->setHtml('Svorková výroba elektřiny <span data-toggle="tooltip" data-placement="top" title="Všechny údaje lze zjistit přímo z měřiče umístěného na výrobně."><i class="fas fa-info-circle"></i></span>'))
        ->setRequired()->setHtmlAttribute('placeholder', '12500')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',11)->setOption('description', Html::el('span class="jednotky"')
		->setHtml('&nbsp;kWh'));
        $form->addInteger('vlastni_spotreba_elektriny', 'Vlastní spotřeba elektřiny')->setHtmlAttribute('placeholder', '7000')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',11)->setOption('description', Html::el('span class="jednotky"')
		->setHtml('&nbsp;kWh'));
        $form->addInteger('celkova_konecna_spotreba', 'Celková spotřeba elektřiny')->setHtmlAttribute('placeholder', '9500')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',11)->setOption('description', Html::el('span class="jednotky"')
		->setHtml('&nbsp;kWh'));
        $form->addInteger('spotreba_z_toho_lokalni', 'Spotřeba z toho lokální')->setHtmlAttribute('placeholder', '2500')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',11)->setOption('description', Html::el('span class="jednotky"')
		->setHtml('&nbsp;kWh'));
        $form->addInteger('spotreba_z_toho_odber', 'Spotřeba z toho odběr')->setHtmlAttribute('placeholder', '4500')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',11)->setOption('description', Html::el('span class="jednotky"')
		->setHtml('&nbsp;kWh'));
        //$form->addSubmit('save', 'Uložit výkaz');
        $form->addSubmit('save', 'Uložit výkaz')->getControlPrototype()->setName('button')->setHtml('Uložit výkaz&nbsp;&nbsp;<i class="fa fa-save fa-lg"></i>')->setAttribute('class', 'button');

        // Funkce se vykonaná při úspěšném odeslání formuláře a zpracuje zadané hodnoty.
        $form->onSuccess[] = function (Form $form, ArrayHash $values) {
            try {
                $this->reportManager->saveReport($values);
                $this->flashMessage('Výkaz byl úspěšně uložen.');
                if(isset($values->id))
                {
                    $this->redirect('Report:', $values->id);
                }else
                {
                    $this->redirect('Report:list');
                }
            } catch (UniqueConstraintViolationException $e) {
                $this->flashMessage('Výkaz s tímto ID již existuje.');
            }
        };

        return $form;
    }
}