<?php

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

/**
 * Presenter pro vykreslování článků.
 * @package App\CoreModule\Presenters
 */
class ReportPresenter extends BasePresenter
{
    /** @var string URL výchozího článku. */
    private string $defaultReportId;

    /** @var ReportManager Model pro správu s článků. */
    private ReportManager $reportManager;

    /** @var user Pro identifikaci uživatele */
    private $user;

    /** @var vybrana_vyrobna Pro identifikaci uživatele */
    private $vybrana_vyrobna = 0;


    /**
     * Konstruktor s nastavením URL výchozího článku a injektovaným modelem pro správu článků.
     * @param string         $defaultReportId URL výchozího článku
     * @param ReportManager $reportManager    automaticky injektovaný model pro správu článků
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
     * Načte a předá článek do šablony podle jeho URL.
     * @param string|null $id URL článku
     * @throws BadReportException Jestliže článek s danou URL nebyl nalezen.
     */
    public function renderDefault(string $id = null)
    {
        if (!$id) $id = $this->defaultReportId; // Pokud není zadaná URL, vezme se URL výchozího článku.

        // Pokusí se načíst článek s danou URL a pokud nebude nalezen vyhodí chybu 404.
        if (!($report = $this->reportManager->getReport($id)))
            $this->error(); // Vyhazuje výjimku BadReportException.

        $this->template->report = $report; // Předá článek do šablony.
    }

    /** Načte a předá seznam článků do šablony. */
    public function renderList()
    {
        $this->template->reports = $this->reportManager->getReportsWhereFactory($this->vybrana_vyrobna);
    }

    /**
     * Odstraní článek.
     * @param string|null $id URL článku
     * @throws AbortException
     */
    public function actionRemove(string $id = null)
    {
        $this->reportManager->removeReport($id);
        $this->flashMessage('Report byl úspěšně odstraněn.');
        $this->redirect('Report:list');
    }

    /**
     * Vykresluje formulář pro editaci článku podle zadané URL.
     * Pokud URL není zadána, nebo článek s danou URL neexistuje, vytvoří se nový.
     * @param string|null $id URL adresa článku
     */
    public function actionEditor(string $id = null)
    {
        if ($id) {
            if (!($report = $this->reportManager->getReport($id)))
                $this->flashMessage('Výkaz nebyl nalezen.'); // Výpis chybové hlášky.
            else 
            {
                $this['editorForm']->setDefaults($report); // Předání hodnot článku do editačního formuláře.
                $this['editorForm']['od']->setDefaultValue($report->od->format('Y-m-d'));
                $this['editorForm']['do']->setDefaultValue($report->do->format('Y-m-d'));
                $date = new DateTime;
                $this['editorForm']['datum_cas_zadani_vykazu']->setDefaultValue($date);
            }
        }
    }
    public function vykazy(Form $form) {
        $this->user = $this->getUser();
        $vyrobna = $form->getValues();
        $this->vybrana_vyrobna = $vyrobna['vyrobna'];
    }

    /**
     * Vytváří dropdown menu se všemi výrobnami daného uživatele.
     * @return Form formulář pro editaci článků
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
            $form->addSelect('vyrobna', 'Jméno výrobny')->setItems($seznam_vyroven)->setRequired()->setAttribute('onChange', 'submit()');
            $form->onSuccess[] = [$this,'vykazy'];            
        }
        return $form;
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
        $form->addHidden('id_osoby')->setRequired()->setDefaultValue($this->user->id);
        $form->addHidden('id_vyrobny')->setRequired()->setDefaultValue($this->vybrana_vyrobna);
        $dateTime = new DateTime;
        $date = $dateTime->format('Y-m-d');
        $form->addText('od', 'Od')->setHtmlType('date')->setDefaultValue($date)->setRequired('%label je nutné vyplnit');;
        $form->addText('do', 'Do')->setHtmlType('date')->setDefaultValue($date)->setRequired('%label je nutné vyplnit');;
        $form->addHidden('datum_cas_zadani_vykazu')->setDefaultValue($dateTime)->setRequired(); // Je nastaveno automaticky podle aktuálního data a času
        $form->addInteger('svorkova_vyroba_elektriny', 'Svorková výroba elektřiny')->setRequired()->setHtmlAttribute('placeholder', '12500')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',11);
        $form->addInteger('vlastni_spotreba_elektriny', 'Vlastní spotřeba elektřiny')->setHtmlAttribute('placeholder', '7000')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',11);;
        $form->addInteger('celkova_konecna_spotreba', 'Celková spotřeba elektřiny')->setHtmlAttribute('placeholder', '9500')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',11);;
        $form->addInteger('spotreba_z_toho_lokalni', 'Spotřeba z toho lokální')->setHtmlAttribute('placeholder', '2500')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',11);;
        $form->addInteger('spotreba_z_toho_odber', 'Spotřeba z toho odběr')->setHtmlAttribute('placeholder', '4500')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',11);;
        $form->addSubmit('save', 'Uložit výkaz');

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