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
	 * @var Helpers\DeviceView
	 */
	private $deviceView;

	/**
	 * @var string
	 */
	private $doVar = '_do';

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
		$this->deviceView = $this->container->getByType(Helpers\DeviceView::class);

		$version = getenv('NETTE');

		if ($version !== 'default') {
			$this->doVar = 'do';
		}
	}

	public function testMobileVersion()
	{
		// Create test presenter
		$presenter = $this->createPresenter();

		// Switch view
		$this->deviceView->setMobileView();

		// Create GET request
		$request = new Application\Request('Test', 'GET', ['action' => 'default']);
		// & fire presenter & catch response
		$response = $presenter->run($request);

		$dq = Tester\DomQuery::fromHtml((string) $response->getSource());

		Assert::true($dq->has('div[id="mobileDevice"]'));
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
