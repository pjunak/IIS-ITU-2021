<?php

declare(strict_types=1);

namespace App\CoreModule\Presenters;

use App\CoreModule\Model\UserManager;
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
class UserPresenter extends BasePresenter
{
    /** @var string URL výchozího článku. */
    private string $defaultUserId;

    /** @var UserManager Model pro správu s článků. */
    private UserManager $userManager;

    /** @var user Pro identifikaci uživatele */
    private $user;
    private $userFe;

    /**
     * Konstruktor s nastavením URL výchozího článku a injektovaným modelem pro správu článků.
     * @param string         $defaultUserId URL výchozího článku
     * @param UserManager $userManager    automaticky injektovaný model pro správu článků
     */
    public function __construct(string $defaultUserId, UserManager $userManager)
    {
        parent::__construct();
        $this->defaultUserId = $defaultUserId;
        $this->userManager = $userManager;
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
        if (!$id) $id = $this->defaultUserId; // Pokud není zadaná URL, vezme se URL výchozího článku.

        // Pokusí se načíst článek s danou URL a pokud nebude nalezen vyhodí chybu 404.
        if (!($userFe = $this->userManager->getUser($id)))
            $this->error(); // Vyhazuje výjimku BadRequestException.
        $this->userFe = $userFe;
        $this->template->userFe = $userFe; // Předá článek do šablony.
    }

    /** Načte a předá seznam článků do šablony. */
    public function renderList()
    {
        $this->template->users = $this->userManager->getUsers();
    }

    /**
     * Odstraní článek.
     * @param string|null $id URL článku
     * @throws AbortException
     */
    public function actionRemove(string $id = null)
    {
        $this->userManager->removeUser($id);
        $this->flashMessage('Uživatel byl úspěšně odstraněn.');
        $this->redirect('User:list');
    }

    /**
     * Vykresluje formulář pro editaci článku podle zadané URL.
     * Pokud URL není zadána, nebo článek s danou URL neexistuje, vytvoří se nový.
     * @param string|null $id URL adresa článku
     */
    public function actionEditor(string $id = null, $role = null)
    {
        if ($id) {
            if (!($user = $this->userManager->getUser($id)))
                $this->flashMessage('Uživatel nebyl nalezen.'); // Výpis chybové hlášky.
            else
            {
                $this['editorForm']->setDefaults($user); // Předání hodnot článku do editačního formuláře. 
                $this['editorFormUrednik']->setDefaults($user); // Předání hodnot článku do editačního formuláře.
            }
        }

        if (!$id) $id = $this->defaultUserId;
        if (!($userFe = $this->userManager->getUser($id)))
            $this->error();
        if ($role) {
            if($role == 'urednik')
            {
                $userFe->update(['typ_osoby' => 'urednik']);
            }else
            {
            $userFe->update(['typ_osoby' => 'disponent']);
            }
        }
        $this->userFe = $userFe;
        $this->template->userFe = $userFe;
    }

    /**
     * Vytváří a vrací formulář pro editaci článků.
     * @return Form formulář pro editaci článků
     */
    protected function createComponentEditorForm()
    {
        // Vytvoření formuláře a definice jeho polí.
        $form = new Form;
        $form->addGroup('Společnost');
        $seznamDostupnychFirem = $this->userManager->getSeznamFirem();
        $form->addSelect('id_firmy', 'Zákazník patří k firmě')->setItems($seznamDostupnychFirem)->setRequired();

        $form->addGroup('Údaje zákazníka');
        $form->addHidden('id');
        $form->addInteger('id_ucastnika', 'ID účastníka');
        $form->addHidden('typ_osoby', 'disponent');
        $form->addText('login', 'Login')->setRequired()->setHtmlAttribute('placeholder', 'Pepega')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',64);
        $form->addPassword('heslo', 'Heslo')->setRequired('%label je nutné vyplnit')
        ->addRule($form::MIN_LENGTH, 'Heslo musí mít alespoň %d znaků', 6)
        ->addRule($form::MAX_LENGTH, 'Heslo nemůže mít víc, než %d znaků', 255);
        // Pro zjednodušení kontroly odstraněno ->addRule($form::PATTERN, 'Musí obsahovat číslici', '.*[0-9].*');
        $form->addText('jmeno', 'Jméno')->setRequired()->setHtmlAttribute('placeholder', 'Jan')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',64);
        $form->addText('prijmeni', 'Příjmení')->setRequired()->setHtmlAttribute('placeholder', 'Novák')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',64);
        $form->addInteger('telefon', 'Telefonní číslo')->setHtmlAttribute('placeholder', '111222333')->addRule($form::LENGTH, 'Délka %label je %d',9);
        $form->addEmail('email', 'E-mail')->setHtmlAttribute('placeholder', 'muj.email@email.cz')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',32);
        $form->addSubmit('save', 'Uložit uživatele');



        // Funkce se vykonaná při úspěšném odeslání formuláře a zpracuje zadané hodnoty.
        $form->onSuccess[] = function (Form $form, ArrayHash $values) {
            try {
                $this->userManager->saveUser($values);
                $this->flashMessage('Uživatel byl úspěšně uložen.');
                if(isset($values->id))
                {
                    $this->redirect('User:', $values->id);
                }else
                {
                    $this->redirect('User:list');
                }
            } catch (UniqueConstraintViolationException $e) {
                $this->flashMessage('Uživatel s tímto Loginem již existuje.');
            }
        };

        return $form;
    }

     /**
     * Vytváří a vrací formulář pro editaci článků. Pokud je uživatel přihlášen jako ředitel, může také upravovat a přidávat tyto pole

     * @return Form formulář pro editaci článků
     */
    protected function createComponentEditorFormUrednik()
    {
        // Vytvoření formuláře a definice jeho polí.
        $form = new Form;
        $form->addGroup('Údaje osoby');
        $form->addHidden('id');
        $form->addInteger('id_ucastnika', 'ID účastníka');
        $form->addHidden('typ_osoby', 'urednik');
        $form->addText('login', 'Login')->setRequired()->setHtmlAttribute('placeholder', 'Pepega')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',64);
        $form->addPassword('heslo', 'Heslo')->setRequired('%label je nutné vyplnit')
        ->addRule($form::MIN_LENGTH, 'Heslo musí mít alespoň %d znaků', 6)
        ->addRule($form::MAX_LENGTH, 'Heslo nemůže mít víc, než %d znaků', 255);
        // Pro zjednodušení kontroly odstraněno ->addRule($form::PATTERN, 'Musí obsahovat číslici', '.*[0-9].*');
        $form->addText('jmeno', 'Jméno')->setRequired()->setHtmlAttribute('placeholder', 'Jan')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',64);
        $form->addText('prijmeni', 'Příjmení')->setRequired()->setHtmlAttribute('placeholder', 'Novák')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',64);
        $form->addInteger('telefon', 'Telefonní číslo')->setHtmlAttribute('placeholder', '111222333')->addRule($form::LENGTH, 'Délka %label je %d',9);
        $form->addEmail('email', 'E-mail')->setHtmlAttribute('placeholder', 'muj.email@email.cz')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',32);
      
        $form->addGroup('Firemní údaje');
        $form->addText('kancelar', 'Kancelář')->setHtmlAttribute('placeholder', '8B')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',32);
        $form->addText('pozice', 'Pozice')->setHtmlAttribute('placeholder', 'Technická podpora')->addRule($form::MAX_LENGTH, 'Maximální délka %label je %d',32);
        $form->addInteger('plat', 'Plat')->setHtmlAttribute('placeholder', '28000')->addRule($form::LENGTH, 'Délka %label je %d',11);
        
        $form->addSubmit('save', 'Uložit úředníka');

        // Funkce se vykonaná při úspěšném odeslání formuláře a zpracuje zadané hodnoty.
        $form->onSuccess[] = function (Form $form, ArrayHash $values) {
            try {
                $this->userManager->saveUser($values);
                $this->flashMessage('Úředník byl úspěšně uložen.');
                if(isset($values->id))
                {
                    $this->redirect('User:', $values->id);
                }else
                {
                    $this->redirect('User:list');
                }
            } catch (UniqueConstraintViolationException $e) {
                $this->flashMessage('Uživatel s tímto ID již existuje.');
            }
        };

        return $form;
    }
}