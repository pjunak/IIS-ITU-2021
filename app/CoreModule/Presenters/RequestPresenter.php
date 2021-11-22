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
        $this->template->requests = $this->requestManager->getRequests();
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
        $form->addText('datum_vytvoreni', 'Datum vytvoření')->setHtmlType('date')->setRequired();
        $form->addText('datum_uzavreni', 'Datum uzavření')->setHtmlType('date')->setRequired();
        $form->addText('predmet', 'Předmět')->setRequired();
        $stavy = [ //Zatím pro případné testování, TODO nepůjde nastavit zde.
            'podan' => 'Podán',
            'vyrizen' => 'Vyřízen',
            'uzavren' => 'Uzavřen',
        ];
        $form->addSelect('staus', ':', $stavy)->setDefaultValue('podan')->setRequired();

        $form->addTextArea('obsah_pozadavku', 'Obsah požadavku')->setRequired();
        $form->addTextArea('odpoved', 'Odpověď')->setRequired(); // Opět, nebude nastavována zde.
        $form->addSubmit('save', 'Uložit požadavek');

        // Funkce se vykonaná při úspěšném odeslání formuláře a zpracuje zadané hodnoty.
        $form->onSuccess[] = function (Form $form, ArrayHash $values) {
            try {
                $this->requestManager->saveRequest($values);
                $this->flashMessage('Request byl úspěšně uložen.');
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