<?php
/**
 * Test: IPub\MobileDetect\DeviceView
 * @testCase
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
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

use IPub\MobileDetect;
use IPub\MobileDetect\Helpers;

require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'bootstrap.php';

class DeviceViewTest extends Tester\TestCase
{
	public function testRequestHasSwitchParam() : void
	{
		$query = [
			'myparam'     => 'myvalue',
			'device_view' => Helpers\DeviceView::VIEW_MOBILE
		];

		// Get helper
		$deviceView = $this->getHelper($query);

		Assert::true($deviceView->hasSwitchParameter());
		Assert::equal(Helpers\DeviceView::VIEW_MOBILE, $deviceView->getSwitchParameterValue());
	}

	public function testDeviceIsMobile() : void
	{
		$query = [
			'myparam'     => 'myvalue',
			'device_view' => Helpers\DeviceView::VIEW_MOBILE
		];

		// Get helper
		$deviceView = $this->getHelper($query);

		Assert::equal(Helpers\DeviceView::VIEW_MOBILE, $deviceView->getViewType());
		Assert::true($deviceView->isMobileView());
		Assert::false($deviceView->isFullView());
	}

	public function testMobileViewType() : void
	{
		$query = [
			'myparam'     => 'myvalue',
			'device_view' => Helpers\DeviceView::VIEW_MOBILE
		];

		// Get helper
		$deviceView = $this->getHelper($query);

		Assert::equal(Helpers\DeviceView::VIEW_MOBILE, $deviceView->getViewType());
	}

	public function testSetMobileViewType() : void
	{
		$query = [
			'myparam'     => 'myvalue',
			'device_view' => Helpers\DeviceView::VIEW_MOBILE
		];

		// Get helper
		$deviceView = $this->getHelper($query);
		$deviceView->setTabletView();

		Assert::notEqual(Helpers\DeviceView::VIEW_MOBILE, $deviceView->getViewType());
	}

	/**
	 * Create DeviceView helper
	 *
	 * @param array $query
	 *
	 * @return Helpers\DeviceView
	 */
	private function getHelper($query = []) : Helpers\DeviceView
	{
		$url = new Nette\Http\Url('https://www.ipublikuj.eu');
		$url->setQuery($query);

		$urlScript = new Nette\Http\UrlScript($url);

		$httpRequest = new Nette\Http\Request($urlScript);
		$httpResponse = new Nette\Http\Response();

		$cookieSettings = new Helpers\CookieSettings('device_view', NULL, '+1 month', '/', false, true);

		// Get helper
		return new Helpers\DeviceView('device_view', $cookieSettings, $httpRequest, $httpResponse);
	}
}

\run(new DeviceViewTest());
