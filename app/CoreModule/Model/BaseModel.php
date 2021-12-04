<?php

/*
//	Projekt do předmětu ITU - Zákaznický portál OTE, a.s.
//	Datum: 5.12.2021
//	Autor: Kristián Heřman, xherma33
*/

namespace Models\Admin;

use Nette;


abstract class Base extends Nette\Object
{
	protected $database;

	public function __construct(Nette\Database\Context $db)
	{
		$this->database = $db;
	}
}
