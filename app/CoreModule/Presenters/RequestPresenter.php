<?php

/*
//	Projekt do předmětu ITU - Zákaznický portál OTE, a.s.
//	Datum: 14.12.2021
//	Autor: Kristián Heřman, xherma33
*/

declare(strict_types=1);

namespace App\CoreModule\Presenters;

use App\CoreModule\Model\RequestManager;
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
 * Presenter pro vykreslování požadavků.
 * @package App\CoreModule\Presenters
 */
class RequestPresenter extends BasePresenter
{
    /** @var string URL výchozího požadavku. */
    private string $defaultRequestId;

    /** @var RequestManager Model pro správu s požadavků. */
    private RequestManager $requestManager;

    /** @var user Pro identifikaci uživatele */
    private $user;

    /**
     * Konstruktor s nastavením URL výchozího požadavku a injektovaným modelem pro správu požadavků.
     * @param string         $defaultRequestId URL výchozího požadavku
     * @param RequestManager $requestManager    automaticky injektovaný model pro správu požadavků
     */
    public function __construct(string $defaultRequestId, RequestManager $requestManager)
    {
        parent::__construct();
        $this->defaultRequestId = $defaultRequestId;
        $this->requestManager = $requestManager;
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
    
    public function getFilledForm(string $id = null, int $cnt = 0)
    {
        if ($id) {
            if (!($request = $this->requestManager->getRequest($id)))
                $this->flashMessage('Request nebyl nalezen.'); // Výpis chybové hlášky.
            else 
            {
                $this['editorForm']->setDefaults($request); // Předání hodnot požadavku do editačního formuláře.
                $this['editorForm']['datum_vytvoreni']->setDefaultValue($request->datum_vytvoreni->format('Y-m-d'));
                $this['editorForm']['datum_uzavreni']->setDefaultValue($request->datum_vytvoreni->format('Y-m-d'));
                $this['editorForm']['status']->setValue('vyrizen');
                $concat = 'form-no-' . $cnt;
                $this['editorForm']->setHtmlAttribute('id', $concat);
                $this['editorForm']['save']->setHtmlAttribute('formmethod', 'post');
            }
        }

        return $this['editorForm'];
    }

    /**
     * Načte a předá požadavek do šablony podle jeho URL.
     * @param string|null $id URL požadavku
     * @throws BadRequestException Jestliže požadavek s danou URL nebyl nalezen.
     */
    public function renderDefault(string $id = null)
    {
        if (!$id) $id = $this->defaultRequestId; // Pokud není zadaná URL, vezme se URL výchozího požadavku.

        // Pokusí se načíst požadavek s danou URL a pokud nebude nalezen vyhodí chybu 404.
        if (!($request = $this->requestManager->getRequest($id)))
            $this->error(); // Vyhazuje výjimku BadRequestException.

        $this->template->request = $request; // Předá požadavek do šablony.
    }

    /** Načte a předá seznam požadavků do šablony. */
    public function renderList()
    {
        if (!isset($this->template->currentIdClicked))
        {
            $this->template->currentIdClicked = null;
        }

        if($this->user->isInRole('disponent'))
        {
            $this->template->requests = $this->requestManager->getRequestsByUser($this->user->getID());
        }
        else
        {
            $this->template->requests = $this->requestManager->getRequests();

        /*
            $this->template->filledForms = array();
            $i = 0;
            foreach($this->template->requests as $requestBe)
            {
                if ($requestBe->status == "podan")
                {
                    array_push($this->template->filledForms, (string)$this->getFilledForm((string)$requestBe->id, $i));
                    $i++;
                }
            }
        */
        }
    }

    /**
     * Odstraní požadavek.
     * @param string|null $id URL požadavku
     * @throws AbortException
     */
    public function actionRemove(string $id = null)
    {
        $this->requestManager->removeRequest($id);
        $this->flashMessage('Request byl úspěšně odstraněn.');
        $this->redirect('Request:list');
    }

    /**
     * Vykresluje formulář pro editaci požadavku podle zadané URL.
     * Pokud URL není zadána, nebo požadavek s danou URL neexistuje, vytvoří se nový.
     * @param string|null $id URL adresa požadavku
     */
    public function actionEditor(string $id = null)
    {
        if ($id) {
            if (!($request = $this->requestManager->getRequest($id)))
                $this->flashMessage('Request nebyl nalezen.'); // Výpis chybové hlášky.
            else 
            {
                $this['editorForm']->setDefaults($request); // Předání hodnot požadavku do editačního formuláře.
                $this['editorForm']['datum_vytvoreni']->setDefaultValue($request->datum_vytvoreni->format('Y-m-d'));
                $this['editorForm']['datum_uzavreni']->setDefaultValue($request->datum_vytvoreni->format('Y-m-d'));
                $this['editorForm']['status']->setDefaultValue('podan');
            }
        }
    }

    /**
     * Vykresluje formulář pro odpověď na požadavek podle zadané URL.
     * @param string|null $id URL adresa požadavku
     */
    public function actionReply(string $id = null)
    {
        if ($id) {
            if (!($request = $this->requestManager->getRequest($id)))
                $this->flashMessage('Request nebyl nalezen.'); // Výpis chybové hlášky.
            else 
            {
                $this['editorForm']->setDefaults($request); // Předání hodnot požadavku do editačního formuláře.
                $this['editorForm']['datum_vytvoreni']->setDefaultValue($request->datum_vytvoreni->format('Y-m-d'));
                $this['editorForm']['datum_uzavreni']->setDefaultValue($request->datum_vytvoreni->format('Y-m-d'));
                $this['editorForm']['status']->setValue('vyrizen');
            }
        }
    }

    /**
     * Ošetří volání AJAXu - uloží odpověď na požadavek do DB přes datový model
     * a invaliduje (překreslí) tabulky s podanými/vyřízenými požadavky
     */
    public function handleAjax($values)
    {
        // nasledujici radek pracuje s datovym modelem (preda mu data z formulare pro ulozeni pozadavku)
        $this->requestManager->saveRequest($values);
        
        if ($this->isAjax())
        {
            $this->redrawControl('ajaxPoza'); // prekresli se tabulka s podanymi pozadavky
            $this->redrawControl('ajaxVyr'); // prekresli se tabulka s vyresenymi pozadavky
        }
        else
        {
            $this->redirect('Request:list');
        }
    }

    /**
     * Nastaví údaje do formuláře, který se přilepí za rozkliknutý požadavek.
     * Současně nastaví currentIdClicked pro šablonu.
     */
    public function handleView(string $id = null)
    {
        if ($id) {
            if (!($request = $this->requestManager->getRequest($id)))
                $this->flashMessage('Request nebyl nalezen.'); // Výpis chybové hlášky.
            else 
            {
                $this['editorForm']->setDefaults($request); // Předání hodnot požadavku do editačního formuláře.
                $this['editorForm']['datum_vytvoreni']->setDefaultValue($request->datum_vytvoreni->format('Y-m-d'));
                $this['editorForm']['datum_uzavreni']->setDefaultValue(date("Y-m-d"));
                $this['editorForm']['status']->setValue('vyrizen');
            }
        }

        $this->template->currentIdClicked = $id;
        $this->redrawControl('ajaxPoza'); // invalidace a překreslení výřezu s výkazy
    }

    /**
     * Vytváří a vrací formulář pro editaci požadavků.
     * @return Form formulář pro editaci požadavků
     */
    protected function createComponentEditorForm()
    {
        // Vytvoření formuláře a definice jeho polí.
        $form = new Form;
        $form->addHidden('id');
        $form->addHidden('id_osoby')->setDefaultValue($this->user->id);
        if($this->user->isInRole('disponent'))
        {
            $date = new DateTime;
            $form->addHidden('datum_vytvoreni')->setDefaultValue($date->format('Y-m-d'));
            $form->addText('predmet', Html::el()->setHtml('Předmět <span data-toggle="tooltip" data-placement="top" title="Krátký popis žádosti, nebo oznámení."><i class="fas fa-info-circle"></i></span>'))
            ->setRequired()->setHtmlAttribute('placeholder', 'Žádost o změnu údaje')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',128);
            $form->addHidden('status', 'Status')->setDefaultValue('podan');
            $form->addTextArea('obsah_pozadavku', 'Obsah požadavku')->setRequired();
            $form->addSubmit('save', 'Odeslat požadavek')->getControlPrototype()->setName('button')->setHtml('Odeslat požadavek&nbsp;&nbsp;<i class="fas fa-paper-plane fa-lg"></i>')->
            setAttribute('class', 'button');
        }
        else
        {
            $form->addText('predmet', 'Předmět')->setDisabled()->setHtmlAttribute('placeholder', 'Žádost o změnu údaje')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',128);
            $form->addText('datum_vytvoreni', 'Datum vytvoření')->setHtmlType('date')->setDisabled();
            $date = new DateTime;
            $form->addHidden('datum_uzavreni')->setDefaultValue($date->format('Y-m-d'));
            $form->addHidden('status', 'Status')->setDefaultValue('vyrizen');
            $form->addTextArea('obsah_pozadavku', 'Obsah požadavku')->setDisabled();
            $form->addTextArea('odpoved', 'Odpověď');
            $form->addSubmit('save', 'Odpovědět na požadavek')->getControlPrototype()->setName('button')->setHtml('Odpovědět na požadavek&nbsp;&nbsp;<i class="fas fa-share fa-lg"></i>')
            ->setAttribute('class', 'button ajax');
        }

        // Funkce se vykonaná při úspěšném odeslání formuláře a zpracuje zadané hodnoty.
        // Aktualne se tato cast nevyuziva, AJAX je odbaven ve funkci handleAjax
        $form->onSuccess[] = function (Form $form, ArrayHash $values) {
            try {
                $this->requestManager->saveRequest($values);
                
                if(!isset($values->id))
                {
                    $this->redirect('Request:list');
                }
                else
                {
                    $this->redrawControl('ajaxPoza'); // prekresli se tabulka s vykazy
                    $this->redrawControl('ajaxVyr'); // prekresli se tabulka s vykazy
                    // a zde se jiz nevola stary redirect
                }
            } catch (UniqueConstraintViolationException $e) {
                $this->flashMessage('Požadavek s tímto ID již existuje.');
            }
        };

        return $form;
    }
}