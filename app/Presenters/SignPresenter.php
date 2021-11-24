<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Forms;
use Nette\Application\UI\Form;
use Nette\Security\User;


final class SignPresenter extends BasePresenter
{
	/** @persistent */
	public $backlink = '';

	private Forms\SignInFormFactory $signInFactory;

	private $user;

	public function __construct(Forms\SignInFormFactory $signInFactory)
	{
		$this->signInFactory = $signInFactory;
	}

	public function startUp()
	{
		parent::startUp();

		$this->user = $this->getUser();

		// Pokud uzivatel klikne na prihlaseni a jiz je prihlasen
		if ($this->isLinkCurrent('Sign:in') && $this->user->isLoggedIn())
		{
			$this->flashMessage('Již jste přihlášen!', "danger");
			$this->forward('Administration:');
		}
	}

	/**
	 * Sign-in form factory.
	 */
	protected function createComponentSignInForm(): Form
	{
		return $this->signInFactory->create(function (): void {
			$this->restoreRequest($this->backlink);
			$this->forward('Administration:');
		});
	}

	public function actionOut(): void
	{
		$this->getUser()->logout();
		$this->session->destroy();
	}
}
