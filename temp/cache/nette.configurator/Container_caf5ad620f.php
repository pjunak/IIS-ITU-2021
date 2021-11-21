<?php
// source: /opt/lampp/htdocs/sandbox/config/common.neon
// source: /opt/lampp/htdocs/sandbox/config/local.neon
// source: array

/** @noinspection PhpParamsInspection,PhpMethodMayBeStaticInspection */

declare(strict_types=1);

class Container_caf5ad620f extends Nette\DI\Container
{
	protected $tags = [
		'nette.inject' => [
			'02' => true,
			'03' => true,
			'application.1' => true,
			'application.2' => true,
			'application.3' => true,
			'application.4' => true,
			'application.5' => true,
			'application.6' => true,
		],
	];

	protected $types = ['container' => 'Nette\DI\Container'];

	protected $aliases = [
		'application' => 'application.application',
		'cacheStorage' => 'cache.storage',
		'database.default' => 'database.default.connection',
		'httpRequest' => 'http.request',
		'httpResponse' => 'http.response',
		'nette.cacheJournal' => 'cache.journal',
		'nette.database.default' => 'database.default',
		'nette.database.default.context' => 'database.default.context',
		'nette.httpRequestFactory' => 'http.requestFactory',
		'nette.latteFactory' => 'latte.latteFactory',
		'nette.mailer' => 'mail.mailer',
		'nette.presenterFactory' => 'application.presenterFactory',
		'nette.templateFactory' => 'latte.templateFactory',
		'nette.userStorage' => 'security.userStorage',
		'session' => 'session.session',
		'user' => 'security.user',
	];

	protected $wiring = [
		'Nette\DI\Container' => [['container']],
		'Nette\Application\Application' => [['application.application']],
		'Nette\Application\IPresenterFactory' => [['application.presenterFactory']],
		'Nette\Application\LinkGenerator' => [['application.linkGenerator']],
		'Nette\Caching\Storages\Journal' => [['cache.journal']],
		'Nette\Caching\Storage' => [['cache.storage']],
		'Nette\Database\Connection' => [['database.default.connection']],
		'Nette\Database\IStructure' => [['database.default.structure']],
		'Nette\Database\Structure' => [['database.default.structure']],
		'Nette\Database\Conventions' => [['database.default.conventions']],
		'Nette\Database\Conventions\DiscoveredConventions' => [['database.default.conventions']],
		'Nette\Database\Explorer' => [['database.default.context']],
		'Nette\Http\RequestFactory' => [['http.requestFactory']],
		'Nette\Http\IRequest' => [['http.request']],
		'Nette\Http\Request' => [['http.request']],
		'Nette\Http\IResponse' => [['http.response']],
		'Nette\Http\Response' => [['http.response']],
		'Nette\Bridges\ApplicationLatte\LatteFactory' => [['latte.latteFactory']],
		'Nette\Application\UI\TemplateFactory' => [['latte.templateFactory']],
		'Nette\Mail\Mailer' => [['mail.mailer']],
		'Nette\Security\Passwords' => [['security.passwords']],
		'Nette\Security\UserStorage' => [['security.userStorage']],
		'Nette\Security\IUserStorage' => [['security.legacyUserStorage']],
		'Nette\Security\User' => [['security.user']],
		'Nette\Http\Session' => [['session.session']],
		'Tracy\ILogger' => [['tracy.logger']],
		'Tracy\BlueScreen' => [['tracy.blueScreen']],
		'Tracy\Bar' => [['tracy.bar']],
		'App\Model\DatabaseManager' => [['01']],
		'App\CoreModule\Model\ArticleManager' => [['01']],
		'App\Presenters\BasePresenter' => [2 => ['02', '03', 'application.2', 'application.3', 'application.4']],
		'Nette\Application\UI\Presenter' => [2 => ['02', '03', 'application.2', 'application.3', 'application.4']],
		'Nette\Application\UI\Control' => [2 => ['02', '03', 'application.2', 'application.3', 'application.4']],
		'Nette\Application\UI\Component' => [2 => ['02', '03', 'application.2', 'application.3', 'application.4']],
		'Nette\ComponentModel\Container' => [2 => ['02', '03', 'application.2', 'application.3', 'application.4']],
		'Nette\ComponentModel\Component' => [2 => ['02', '03', 'application.2', 'application.3', 'application.4']],
		'Nette\ComponentModel\IComponent' => [2 => ['02', '03', 'application.2', 'application.3', 'application.4']],
		'Nette\ComponentModel\IContainer' => [2 => ['02', '03', 'application.2', 'application.3', 'application.4']],
		'Nette\Application\UI\SignalReceiver' => [2 => ['02', '03', 'application.2', 'application.3', 'application.4']],
		'Nette\Application\UI\StatePersistent' => [2 => ['02', '03', 'application.2', 'application.3', 'application.4']],
		'ArrayAccess' => [2 => ['02', '03', '08', 'application.2', 'application.3', 'application.4']],
		'Nette\Application\UI\Renderable' => [2 => ['02', '03', 'application.2', 'application.3', 'application.4']],
		'Nette\Application\IPresenter' => [
			2 => [
				'02',
				'03',
				'application.1',
				'application.2',
				'application.3',
				'application.4',
				'application.5',
				'application.6',
			],
		],
		'App\CoreModule\Presenters\ArticlePresenter' => [2 => ['02']],
		'App\CoreModule\Presenters\ContactPresenter' => [2 => ['03']],
		'Nette\Security\Authenticator' => [['04']],
		'Nette\Security\IAuthenticator' => [['04']],
		'App\Model\UserManager' => [['04']],
		'App\Forms\FormFactory' => [['05']],
		'App\Forms\SignInFormFactory' => [['06']],
		'App\Forms\SignUpFormFactory' => [['07']],
		'Nette\Routing\RouteList' => [['08']],
		'Nette\Routing\Router' => [['08']],
		'Countable' => [2 => ['08']],
		'IteratorAggregate' => [2 => ['08']],
		'Traversable' => [2 => ['08']],
		'Nette\Application\Routers\RouteList' => [['08']],
		'App\Presenters\ErrorPresenter' => [2 => ['application.1']],
		'App\Presenters\SignPresenter' => [2 => ['application.2']],
		'App\Presenters\Error4xxPresenter' => [2 => ['application.3']],
		'App\CoreModule\Presenters\AdministrationPresenter' => [2 => ['application.4']],
		'NetteModule\ErrorPresenter' => [2 => ['application.5']],
		'NetteModule\MicroPresenter' => [2 => ['application.6']],
		'Nette\Forms\FormFactory' => [['forms.factory']],
	];


	public function __construct(array $params = [])
	{
		parent::__construct($params);
		$this->parameters += [
			'defaultArticleUrl' => 'uvod',
			'contactEmail' => 'admin@localhost.cz',
			'appDir' => '/opt/lampp/htdocs/sandbox/app',
			'wwwDir' => '/opt/lampp/htdocs/sandbox/www',
			'vendorDir' => '/opt/lampp/htdocs/sandbox/vendor',
			'debugMode' => true,
			'productionMode' => false,
			'consoleMode' => false,
			'tempDir' => '/opt/lampp/htdocs/sandbox/temp',
		];
	}


	public function createService01(): App\CoreModule\Model\ArticleManager
	{
		return new App\CoreModule\Model\ArticleManager($this->getService('database.default.context'));
	}


	public function createService02(): App\CoreModule\Presenters\ArticlePresenter
	{
		$service = new App\CoreModule\Presenters\ArticlePresenter('uvod', $this->getService('01'));
		$service->injectPrimary(
			$this,
			$this->getService('application.presenterFactory'),
			$this->getService('08'),
			$this->getService('http.request'),
			$this->getService('http.response'),
			$this->getService('session.session'),
			$this->getService('security.user'),
			$this->getService('latte.templateFactory')
		);
		$service->invalidLinkMode = 5;
		return $service;
	}


	public function createService03(): App\CoreModule\Presenters\ContactPresenter
	{
		$service = new App\CoreModule\Presenters\ContactPresenter('admin@localhost.cz', $this->getService('mail.mailer'));
		$service->injectPrimary(
			$this,
			$this->getService('application.presenterFactory'),
			$this->getService('08'),
			$this->getService('http.request'),
			$this->getService('http.response'),
			$this->getService('session.session'),
			$this->getService('security.user'),
			$this->getService('latte.templateFactory')
		);
		$service->invalidLinkMode = 5;
		return $service;
	}


	public function createService04(): App\Model\UserManager
	{
		return new App\Model\UserManager($this->getService('database.default.context'), $this->getService('security.passwords'));
	}


	public function createService05(): App\Forms\FormFactory
	{
		return new App\Forms\FormFactory;
	}


	public function createService06(): App\Forms\SignInFormFactory
	{
		return new App\Forms\SignInFormFactory($this->getService('05'), $this->getService('security.user'));
	}


	public function createService07(): App\Forms\SignUpFormFactory
	{
		return new App\Forms\SignUpFormFactory($this->getService('05'), $this->getService('04'));
	}


	public function createService08(): Nette\Application\Routers\RouteList
	{
		return App\Router\RouterFactory::createRouter();
	}


	public function createServiceApplication__1(): App\Presenters\ErrorPresenter
	{
		return new App\Presenters\ErrorPresenter($this->getService('tracy.logger'));
	}


	public function createServiceApplication__2(): App\Presenters\SignPresenter
	{
		$service = new App\Presenters\SignPresenter($this->getService('06'), $this->getService('07'));
		$service->injectPrimary(
			$this,
			$this->getService('application.presenterFactory'),
			$this->getService('08'),
			$this->getService('http.request'),
			$this->getService('http.response'),
			$this->getService('session.session'),
			$this->getService('security.user'),
			$this->getService('latte.templateFactory')
		);
		$service->invalidLinkMode = 5;
		return $service;
	}


	public function createServiceApplication__3(): App\Presenters\Error4xxPresenter
	{
		$service = new App\Presenters\Error4xxPresenter;
		$service->injectPrimary(
			$this,
			$this->getService('application.presenterFactory'),
			$this->getService('08'),
			$this->getService('http.request'),
			$this->getService('http.response'),
			$this->getService('session.session'),
			$this->getService('security.user'),
			$this->getService('latte.templateFactory')
		);
		$service->invalidLinkMode = 5;
		return $service;
	}


	public function createServiceApplication__4(): App\CoreModule\Presenters\AdministrationPresenter
	{
		$service = new App\CoreModule\Presenters\AdministrationPresenter;
		$service->injectPrimary(
			$this,
			$this->getService('application.presenterFactory'),
			$this->getService('08'),
			$this->getService('http.request'),
			$this->getService('http.response'),
			$this->getService('session.session'),
			$this->getService('security.user'),
			$this->getService('latte.templateFactory')
		);
		$service->invalidLinkMode = 5;
		return $service;
	}


	public function createServiceApplication__5(): NetteModule\ErrorPresenter
	{
		return new NetteModule\ErrorPresenter($this->getService('tracy.logger'));
	}


	public function createServiceApplication__6(): NetteModule\MicroPresenter
	{
		return new NetteModule\MicroPresenter($this, $this->getService('http.request'), $this->getService('08'));
	}


	public function createServiceApplication__application(): Nette\Application\Application
	{
		$service = new Nette\Application\Application(
			$this->getService('application.presenterFactory'),
			$this->getService('08'),
			$this->getService('http.request'),
			$this->getService('http.response')
		);
		$service->catchExceptions = null;
		$service->errorPresenter = 'Error';
		Nette\Bridges\ApplicationDI\ApplicationExtension::initializeBlueScreenPanel(
			$this->getService('tracy.blueScreen'),
			$service
		);
		$this->getService('tracy.bar')->addPanel(new Nette\Bridges\ApplicationTracy\RoutingPanel(
			$this->getService('08'),
			$this->getService('http.request'),
			$this->getService('application.presenterFactory')
		));
		return $service;
	}


	public function createServiceApplication__linkGenerator(): Nette\Application\LinkGenerator
	{
		return new Nette\Application\LinkGenerator(
			$this->getService('08'),
			$this->getService('http.request')->getUrl()->withoutUserInfo(),
			$this->getService('application.presenterFactory')
		);
	}


	public function createServiceApplication__presenterFactory(): Nette\Application\IPresenterFactory
	{
		$service = new Nette\Application\PresenterFactory(new Nette\Bridges\ApplicationDI\PresenterFactoryCallback(
			$this,
			5,
			'/opt/lampp/htdocs/sandbox/temp/cache/nette.application/touch'
		));
		$service->setMapping(['*' => 'App\*Module\Presenters\*Presenter']);
		return $service;
	}


	public function createServiceCache__journal(): Nette\Caching\Storages\Journal
	{
		return new Nette\Caching\Storages\SQLiteJournal('/opt/lampp/htdocs/sandbox/temp/cache/journal.s3db');
	}


	public function createServiceCache__storage(): Nette\Caching\Storage
	{
		return new Nette\Caching\Storages\FileStorage('/opt/lampp/htdocs/sandbox/temp/cache', $this->getService('cache.journal'));
	}


	public function createServiceContainer(): Container_caf5ad620f
	{
		return $this;
	}


	public function createServiceDatabase__default__connection(): Nette\Database\Connection
	{
		$service = new Nette\Database\Connection('mysql:host=127.0.0.1;dbname=nette-rs', 'root', null, ['lazy' => true]);
		Nette\Database\Helpers::initializeTracy(
			$service,
			true,
			'default',
			true,
			$this->getService('tracy.bar'),
			$this->getService('tracy.blueScreen')
		);
		return $service;
	}


	public function createServiceDatabase__default__context(): Nette\Database\Explorer
	{
		return new Nette\Database\Explorer(
			$this->getService('database.default.connection'),
			$this->getService('database.default.structure'),
			$this->getService('database.default.conventions'),
			$this->getService('cache.storage')
		);
	}


	public function createServiceDatabase__default__conventions(): Nette\Database\Conventions\DiscoveredConventions
	{
		return new Nette\Database\Conventions\DiscoveredConventions($this->getService('database.default.structure'));
	}


	public function createServiceDatabase__default__structure(): Nette\Database\Structure
	{
		return new Nette\Database\Structure($this->getService('database.default.connection'), $this->getService('cache.storage'));
	}


	public function createServiceForms__factory(): Nette\Forms\FormFactory
	{
		return new Nette\Forms\FormFactory($this->getService('http.request'));
	}


	public function createServiceHttp__request(): Nette\Http\Request
	{
		return $this->getService('http.requestFactory')->fromGlobals();
	}


	public function createServiceHttp__requestFactory(): Nette\Http\RequestFactory
	{
		$service = new Nette\Http\RequestFactory;
		$service->setProxy([]);
		return $service;
	}


	public function createServiceHttp__response(): Nette\Http\Response
	{
		$service = new Nette\Http\Response;
		$service->cookieSecure = $this->getService('http.request')->isSecured();
		return $service;
	}


	public function createServiceLatte__latteFactory(): Nette\Bridges\ApplicationLatte\LatteFactory
	{
		return new class ($this) implements Nette\Bridges\ApplicationLatte\LatteFactory {
			private $container;


			public function __construct(Container_caf5ad620f $container)
			{
				$this->container = $container;
			}


			public function create(): Latte\Engine
			{
				$service = new Latte\Engine;
				$service->setTempDirectory('/opt/lampp/htdocs/sandbox/temp/cache/latte');
				$service->setAutoRefresh();
				$service->setContentType('html');
				Nette\Utils\Html::$xhtml = false;
				return $service;
			}
		};
	}


	public function createServiceLatte__templateFactory(): Nette\Application\UI\TemplateFactory
	{
		$service = new Nette\Bridges\ApplicationLatte\TemplateFactory(
			$this->getService('latte.latteFactory'),
			$this->getService('http.request'),
			$this->getService('security.user'),
			$this->getService('cache.storage')
		);
		Nette\Bridges\ApplicationDI\LatteExtension::initLattePanel($service, $this->getService('tracy.bar'));
		return $service;
	}


	public function createServiceMail__mailer(): Nette\Mail\Mailer
	{
		return new Nette\Mail\SendmailMailer;
	}


	public function createServiceSecurity__legacyUserStorage(): Nette\Security\IUserStorage
	{
		return new Nette\Http\UserStorage($this->getService('session.session'));
	}


	public function createServiceSecurity__passwords(): Nette\Security\Passwords
	{
		return new Nette\Security\Passwords;
	}


	public function createServiceSecurity__user(): Nette\Security\User
	{
		$service = new Nette\Security\User(
			$this->getService('security.legacyUserStorage'),
			$this->getService('04'),
			null,
			$this->getService('security.userStorage')
		);
		$this->getService('tracy.bar')->addPanel(new Nette\Bridges\SecurityTracy\UserPanel($service));
		return $service;
	}


	public function createServiceSecurity__userStorage(): Nette\Security\UserStorage
	{
		return new Nette\Bridges\SecurityHttp\SessionStorage($this->getService('session.session'));
	}


	public function createServiceSession__session(): Nette\Http\Session
	{
		$service = new Nette\Http\Session($this->getService('http.request'), $this->getService('http.response'));
		$service->setExpiration('14 days');
		$service->setOptions(['cookieSamesite' => 'Lax']);
		return $service;
	}


	public function createServiceTracy__bar(): Tracy\Bar
	{
		return Tracy\Debugger::getBar();
	}


	public function createServiceTracy__blueScreen(): Tracy\BlueScreen
	{
		return Tracy\Debugger::getBlueScreen();
	}


	public function createServiceTracy__logger(): Tracy\ILogger
	{
		return Tracy\Debugger::getLogger();
	}


	public function initialize()
	{
		// di.
		(function () {
			$this->getService('tracy.bar')->addPanel(new Nette\Bridges\DITracy\ContainerPanel($this));
		})();
		// forms.
		(function () {
			Nette\Forms\Validator::$messages[Nette\Forms\Form::REQUIRED] = 'Povinné pole.';
			Nette\Forms\Validator::$messages[Nette\Forms\Form::EMAIL] = 'Neplatná emailová adresa.';
		})();
		// http.
		(function () {
			$response = $this->getService('http.response');
			$response->setHeader('X-Powered-By', 'Nette Framework 3');
			$response->setHeader('Content-Type', 'text/html; charset=utf-8');
			$response->setHeader('X-Frame-Options', 'SAMEORIGIN');
			Nette\Http\Helpers::initCookie($this->getService('http.request'), $response);
		})();
		// session.
		(function () {
			$this->getService('session.session')->exists() && $this->getService('session.session')->start();
		})();
		// tracy.
		(function () {
			if (!Tracy\Debugger::isEnabled()) { return; }
			Tracy\Debugger::getLogger()->mailer = [new Tracy\Bridges\Nette\MailSender($this->getService('mail.mailer')), 'send'];
			$this->getService('session.session')->start();
			Tracy\Debugger::dispatch();
		})();
	}
}
