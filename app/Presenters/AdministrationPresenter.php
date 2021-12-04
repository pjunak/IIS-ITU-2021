<?php

/*
//	Projekt do předmětu ITU - Zákaznický portál OTE, a.s.
//	Datum: 5.12.2021
//	Autor: Dalibor Čásek, xcasek01
*/

declare(strict_types=1);

namespace App\Presenters;

use App\Presenters\BasePresenter;

/**
 * Presenter pro vykreslování administrační sekce.
 * @package App\CoreModule\Presenters
 */
class AdministrationPresenter extends BasePresenter
{
    public function startup(): void
    {
        parent::startup();

        if (!$this->getUser()->isLoggedIn()) {
            $this->redirect('Sign:in');
        }
    }
}