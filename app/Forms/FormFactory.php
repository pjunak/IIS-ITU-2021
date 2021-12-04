<?php

/*
//	Projekt do předmětu ITU - Zákaznický portál OTE, a.s.
//	Datum: 5.12.2021
//	Autor: Kristián Heřman, xherma33
//	Autor: Dalibor Čásek, xcasek01
//	Autor: Petr Junák, xjunak01
*/

declare(strict_types=1);

namespace App\Forms;

use Nette;
use Nette\Application\UI\Form;


final class FormFactory
{
	use Nette\SmartObject;

	public function create(): Form
	{
		return new Form;
	}
}
