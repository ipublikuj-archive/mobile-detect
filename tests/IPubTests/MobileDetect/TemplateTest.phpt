<?php
/**
 * Test: IPub\MobileDetect\MobileDetect
 * @testCase
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:MobileDetect!
 * @subpackage     Tests
 * @since          1.0.0
 *
 * @date           15.01.17
 */

declare(strict_types = 1);

namespace IPubTests\MobileDetect;

use Nette;
use Nette\Application;
use Nette\Application\Routers;
use Nette\Application\UI;
use Nette\Utils;

use Tester;
use Tester\Assert;

use IPub;
use IPub\MobileDetect;
use IPub\MobileDetect\Helpers;

require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'bootstrap.php';
require __DIR__ . DS . 'libs' . DS . 'RouterFactory.php';

class TemplateTest extends Tester\TestCase
{
	/**
	 * @var Application\IPresenterFactory
	 */
	private $presenterFactory;

	/**
	 * @var Nette\DI\Container
	 */
	private $container;

	/**
	 * @var MobileDetect\MobileDetect
	 */
	private $mobileDetector;

	/**
	 * {@inheritdoc}
	 */
	public function setUp()
	{
		parent::setUp();

		$this->container = $this->createContainer();

		// Get presenter factory from container
		$this->presenterFactory = $this->container->getByType(Application\IPresenterFactory::class);

		// Get device view service
		$this->mobileDetector = $this->container->getByType(MobileDetect\MobileDetect::class);
	}

	public function testMobileVersion()
	{
		$this->mobileDetector->setHttpHeaders([
			'SERVER_SOFTWARE'       => 'Apache/2.2.15 (Linux) Whatever/4.0 PHP/5.2.13',
			'REQUEST_METHOD'        => 'POST',
			'HTTP_HOST'             => 'home.ghita.org',
			'HTTP_X_REAL_IP'        => '1.2.3.4',
			'HTTP_X_FORWARDED_FOR'  => '1.2.3.5',
			'HTTP_CONNECTION'       => 'close',
			'HTTP_USER_AGENT'       => 'Mozilla/5.0 (iPhone; CPU iPhone OS 6_0_1 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A523 Safari/8536.25',
			'HTTP_ACCEPT'           => 'text/vnd.wap.wml, application/json, text/javascript, */*; q=0.01',
			'HTTP_ACCEPT_LANGUAGE'  => 'en-us,en;q=0.5',
			'HTTP_ACCEPT_ENCODING'  => 'gzip, deflate',
			'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest',
			'HTTP_REFERER'          => 'http://ipublikuj.eu',
			'HTTP_PRAGMA'           => 'no-cache',
			'HTTP_CACHE_CONTROL'    => 'no-cache',
			'REMOTE_ADDR'           => '11.22.33.44',
			'REQUEST_TIME'          => '01-10-2012 07:57'
		]);

		// Create test presenter
		$presenter = $this->createPresenter();

		// Create GET request
		$request = new Application\Request('Test', 'GET', ['action' => 'default']);
		// & fire presenter & catch response
		$response = $presenter->run($request);

		$dq = Tester\DomQuery::fromHtml((string) $response->getSource());

		Assert::true($dq->has('div[id*="mobileDevice"]'));
	}

	/**
	 * @return Application\IPresenter
	 */
	private function createPresenter() : Application\IPresenter
	{
		// Create test presenter
		$presenter = $this->presenterFactory->createPresenter('Test');
		// Disable auto canonicalize to prevent redirection
		$presenter->autoCanonicalize = FALSE;

		return $presenter;
	}

	/**
	 * @return Nette\DI\Container
	 */
	private function createContainer() : Nette\DI\Container
	{
		$config = new Nette\Configurator();
		$config->setTempDirectory(TEMP_DIR);

		MobileDetect\DI\MobileDetectExtension::register($config);

		$config->addConfig(__DIR__ . DS . 'files' . DS . 'config.neon');

		$version = getenv('NETTE');

		if (!$version || $version == 'default') {
			$config->addConfig(__DIR__ . DS . 'files' . DS . 'presenters.neon');

		} else {
			$config->addConfig(__DIR__ . DS . 'files' . DS . 'presenters_2.3.neon');
		}

		return $config->createContainer();
	}
}

class TestPresenter extends UI\Presenter
{
	use MobileDetect\TMobileDetect;

	public function renderDefault()
	{
		// Set template for component testing
		$this->template->setFile(__DIR__ . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'default.latte');
	}
}

\run(new TemplateTest());
