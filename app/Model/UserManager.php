<?php

/*
//	Projekt do předmětu ITU - Zákaznický portál OTE, a.s.
//	Datum: 5.12.2021
//	Autor: Kristián Heřman, xherma33
*/

declare(strict_types=1);

namespace App\Model;

use Nette;
use Nette\Security\Passwords;


/**
 * Users management.
 */
final class UserManager implements Nette\Security\Authenticator
{
	use Nette\SmartObject;
	
	private const
		TABLE_NAME = 'iis_osoba',
		ID = 'id',
		ID_UCASTNIKA = 'id_ucastnika',
		TYP_OSOBY = 'typ_osoby',
		JMENO = 'jmeno',
		PRIJMENI = 'prijmeni',
		TELEFON = 'telefon',
		EMAIL = 'email',
		LOGIN = 'login',
		HESLO = 'heslo',
		ULICE = 'ulice',
		CISLO_P = 'cislo_p',
		CISLO_O = 'cislo_o',
		OBEC = 'obec',
		PSC = 'psc',
		KANCELAR = 'kancelar',
		POZICE = 'pozice',
		PLAT = 'plat';


	private Nette\Database\Explorer $database;

	private Passwords $passwords;


	public function __construct(Nette\Database\Explorer $database, Passwords $passwords)
	{
		$this->database = $database;
		$this->passwords = $passwords;
	}


	/**
	 * Performs an authentication.
	 * @throws Nette\Security\AuthenticationException
	 */
	public function authenticate(string $username, string $password): Nette\Security\SimpleIdentity
	{
		$row = $this->database->table(self::TABLE_NAME)->where(self::LOGIN, $username)->fetch();

		if (!$row) {
			throw new Nette\Security\AuthenticationException('The username is incorrect.', self::IDENTITY_NOT_FOUND);

		} elseif (!$this->passwords->verify($password, $row[self::HESLO])) {
			/*
			echo '<br>';
			echo $password;
			echo '<br>';
			echo $row[self::HESLO];*/
			throw new Nette\Security\AuthenticationException('The password is incorrect.', self::INVALID_CREDENTIAL);

		} elseif ($this->passwords->needsRehash($row[self::HESLO])) {
			$row->update([
				self::HESLO => $this->passwords->hash($password),
			]);
		}

		$arr = $row->toArray();
		unset($arr[self::HESLO]);
		return new Nette\Security\SimpleIdentity($row[self::ID], $row[self::TYP_OSOBY], $arr);
	}


	/**
	 * Adds new user.
	 * @throws DuplicateNameException
	 */
	public function add(string $username, string $email, string $password): void
	{
		Nette\Utils\Validators::assert($email, 'email');
		try {
			$this->database->table(self::TABLE_NAME)->insert([
				self::COLUMN_NAME => $username,
				self::COLUMN_PASSWORD_HASH => $this->passwords->hash($password),
				self::COLUMN_EMAIL => $email,
			]);
		} catch (Nette\Database\UniqueConstraintViolationException $e) {
			throw new DuplicateNameException;
		}
	}
}



class DuplicateNameException extends \Exception
{
}
