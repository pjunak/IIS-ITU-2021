<?php

/*
//	Projekt do předmětu ITU - Zákaznický portál OTE, a.s.
//	Datum: 5.12.2021
//	Autor: Kristián Heřman, xherma33
//	Autor: Dalibor Čásek, xcasek01
//	Autor: Petr Junák, xjunak01
*/

declare(strict_types=1);

namespace App\Presenters;

use Nette;


final class Error4xxPresenter extends BasePresenter
{
	public function startup(): void
	{
		parent::startup();
		if (!$this->getRequest()->isMethod(Nette\Application\Request::FORWARD)) {
			$this->error();
		}
	}


	public function renderDefault(Nette\Application\BadRequestException $exception): void
	{
		// load template 403.latte or 404.latte or ... 4xx.latte
		$file = dirname(__DIR__, 1) . "/templates/Error/{$exception->getCode()}.latte";
		$file = is_file($file) ? $file : dirname(__DIR__, 1) . '/templates/Error/4xx.latte';
		$this->template->setFile($file);
	}
}
