<?php
/**
 * Test: IPub\MobileDetect\Extension
 * @testCase
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:MobileDetect!
 * @subpackage     Tests
 * @since          1.0.0
 *
 * @date           10.01.15
 */

declare(strict_types = 1);

namespace IPubTests\MobileDetect;

use Nette;

use Tester;
use Tester\Assert;

use IPub;
use IPub\MobileDetect;

require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'bootstrap.php';

class ExtensionTest extends Tester\TestCase
{
	public function testFunctional()
	{
		$dic = $this->createContainer();

		Assert::true($dic->getService('mobileDetect.mobileDetect') instanceof IPub\MobileDetect\MobileDetect);
		Assert::true($dic->getService('mobileDetect.deviceView') instanceof IPub\MobileDetect\Helpers\DeviceView);
		Assert::true($dic->getService('mobileDetect.onRequestHandler') instanceof IPub\MobileDetect\Events\OnRequestHandler);
		Assert::true($dic->getService('mobileDetect.onResponseHandler') instanceof IPub\MobileDetect\Events\OnResponseHandler);
	}

	/**
	 * @return Nette\DI\Container
	 */
	protected function createContainer() : Nette\DI\Container
	{
		$config = new Nette\Configurator();
		$config->setTempDirectory(TEMP_DIR);

		MobileDetect\DI\MobileDetectExtension::register($config);

		$config->addConfig(__DIR__ . DS . 'files' . DS . 'config.neon');

		return $config->createContainer();
	}
}

\run(new ExtensionTest());
