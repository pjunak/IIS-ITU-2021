<?php

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
