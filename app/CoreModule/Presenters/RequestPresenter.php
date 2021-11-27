<?php

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

/**
 * Presenter pro vykreslování článků.
 * @package App\CoreModule\Presenters
 */
class RequestPresenter extends BasePresenter
{
    /** @var string URL výchozího článku. */
    private string $defaultRequestId;

    /** @var RequestManager Model pro správu s článků. */
    private RequestManager $requestManager;

    /** @var user Pro identifikaci uživatele */
    private $user;

    /**
     * Konstruktor s nastavením URL výchozího článku a injektovaným modelem pro správu článků.
     * @param string         $defaultRequestId URL výchozího článku
     * @param RequestManager $requestManager    automaticky injektovaný model pro správu článků
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
    
    /**
     * Načte a předá článek do šablony podle jeho URL.
     * @param string|null $id URL článku
     * @throws BadRequestException Jestliže článek s danou URL nebyl nalezen.
     */
    public function renderDefault(string $id = null)
    {
        if (!$id) $id = $this->defaultRequestId; // Pokud není zadaná URL, vezme se URL výchozího článku.

        // Pokusí se načíst článek s danou URL a pokud nebude nalezen vyhodí chybu 404.
        if (!($request = $this->requestManager->getRequest($id)))
            $this->error(); // Vyhazuje výjimku BadRequestException.

        $this->template->request = $request; // Předá článek do šablony.
    }

    /** Načte a předá seznam článků do šablony. */
    public function renderList()
    {
        if($this->user->isInRole('disponent'))
        {
            $this->template->requests = $this->requestManager->getRequestsByUser($this->user->getID());
        }
        else
        {
            $this->template->requests = $this->requestManager->getRequests();
        }
    }

    /**
     * Odstraní článek.
     * @param string|null $id URL článku
     * @throws AbortException
     */
    public function actionRemove(string $id = null)
    {
        $this->requestManager->removeRequest($id);
        $this->flashMessage('Request byl úspěšně odstraněn.');
        $this->redirect('Request:list');
    }

    /**
     * Vykresluje formulář pro editaci článku podle zadané URL.
     * Pokud URL není zadána, nebo článek s danou URL neexistuje, vytvoří se nový.
     * @param string|null $id URL adresa článku
     */
    public function actionEditor(string $id = null)
    {
        if ($id) {
            if (!($request = $this->requestManager->getRequest($id)))
                $this->flashMessage('Request nebyl nalezen.'); // Výpis chybové hlášky.
            else 
            {
                $this['editorForm']->setDefaults($request); // Předání hodnot článku do editačního formuláře.
                $this['editorForm']['datum_vytvoreni']->setDefaultValue($request->datum_vytvoreni->format('Y-m-d'));
                $this['editorForm']['datum_uzavreni']->setDefaultValue($request->datum_vytvoreni->format('Y-m-d'));
                $this['editorForm']['status']->setDefaultValue('podan');
            }
        }
    }

    public function actionReply(string $id = null)
    {
        if ($id) {
            if (!($request = $this->requestManager->getRequest($id)))
                $this->flashMessage('Request nebyl nalezen.'); // Výpis chybové hlášky.
            else 
            {
                $this['editorForm']->setDefaults($request); // Předání hodnot článku do editačního formuláře.
                $this['editorForm']['datum_vytvoreni']->setDefaultValue($request->datum_vytvoreni->format('Y-m-d'));
                $this['editorForm']['datum_uzavreni']->setDefaultValue($request->datum_vytvoreni->format('Y-m-d'));
                $this['editorForm']['status']->setValue('vyrizen');
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
        $form->addHidden('id_osoby')->setDefaultValue($this->user->id);
        if($this->user->getRoles()[0] == 'disponent')
        {
            $date = new DateTime;
            $form->addHidden('datum_vytvoreni')->setDefaultValue($date->format('Y-m-d'));
            $form->addText('predmet', 'Předmět')->setRequired()->setHtmlAttribute('placeholder', 'Žádost o změnu údaje')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',128);
            $form->addHidden('status', 'Status')->setValue('podan')->setDisabled();
            $form->addTextArea('obsah_pozadavku', 'Obsah požadavku')->setRequired();
            $form->addSubmit('save', 'Vložit požadavek');
        }
        else
        {
            $form->addText('predmet', 'Předmět')->setRequired()->setDisabled()->setHtmlAttribute('placeholder', 'Žádost o změnu údaje')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',128);
            $form->addText('datum_vytvoreni', 'Datum vytvoření')->setHtmlType('date')->setDisabled();
            $date = new DateTime;
            $form->addHidden('datum_uzavreni')->setDefaultValue($date->format('Y-m-d'));
            $form->addHidden('status', 'Status')->setDefaultValue('vyrizen');
            $form->addTextArea('obsah_pozadavku', 'Obsah požadavku')->setDisabled()->setRequired();
            $form->addTextArea('odpoved', 'Odpověď');
            $form->addSubmit('save', 'Odpovědět na požadavek');
        }
        

        // da se vyuzit pro odpoved
        //if (!$this->getAction() == "reply")
        
        
        
        

        // Funkce se vykonaná při úspěšném odeslání formuláře a zpracuje zadané hodnoty.
        $form->onSuccess[] = function (Form $form, ArrayHash $values) {
            try {
                $this->requestManager->saveRequest($values);
                $this->flashMessage('Požadavek byl úspěšně uložen.');
                if(isset($values->id))
                {
                    $this->redirect('Request:', $values->id);
                }else
                {
                    $this->redirect('Request:list');
                }
            } catch (UniqueConstraintViolationException $e) {
                $this->flashMessage('Požadavek s tímto ID již existuje.');
            }
        };

        return $form;
    }
}