<?php

declare(strict_types=1);

namespace App\Forms;

use Nette;
use Nette\Application\UI\Form;
use Nette\Security\User;


final class SignInFormFactory
{
	use Nette\SmartObject;

	private FormFactory $factory;

	private User $user;


	public function __construct(FormFactory $factory, User $user)
	{
		$this->factory = $factory;
		$this->user = $user;
	}


	public function create(callable $onSuccess): Form
	{
		$form = $this->factory->create();

		$form->addText('username', 'Login:')
			->setRequired('Prosím zadejte vaše uživatelské jméno.');

		$form->addPassword('password', 'Certifikát:')
			->setRequired('Prosím zadejte vaše heslo.');

		$form->addCheckbox('remember', 'Zapamatovat si přihlášení.');

		$form->addSubmit('send', 'Přihlásit se');

		$form->onSuccess[] = function (Form $form, \stdClass $values) use ($onSuccess): void {
			try {
				$this->user->setExpiration($values->remember ? '14 days' : '20 minutes');
				$this->user->login($values->username, $values->password);
			} catch (Nette\Security\AuthenticationException $e) {
				$form->addError('Přihlašovací údaje jsou neplatné, zkuste to prosím znovu.');
				return;
			}
			$onSuccess();
		};

		return $form;
	}
}
