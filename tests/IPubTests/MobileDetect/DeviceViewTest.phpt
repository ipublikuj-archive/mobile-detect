<?php
/**
 * Test: IPub\MobileDetect\DeviceView
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
use IPub\MobileDetect\Helpers;

require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'bootstrap.php';

class DeviceViewTest extends Tester\TestCase
{
	public function testRequestHasSwitchParam()
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

	public function testDeviceIsMobile()
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

	public function testMobileViewType()
	{
		$query = [
			'myparam'     => 'myvalue',
			'device_view' => Helpers\DeviceView::VIEW_MOBILE
		];

		// Get helper
		$deviceView = $this->getHelper($query);

		Assert::equal(Helpers\DeviceView::VIEW_MOBILE, $deviceView->getViewType());
	}

	public function testSetMobileViewType()
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
		$url = new Nette\Http\UrlScript('http://www.ipublikuj.eu');
		$url->setQuery($query);

		$httpRequest = new Nette\Http\Request($url);
		$httpResponse = new Nette\Http\Response();

		$cookieSettings = new Helpers\CookieSettings('device_view', NULL, '+1 month', '/', FALSE, TRUE);

		// Get helper
		return new Helpers\DeviceView('device_view', $cookieSettings, $httpRequest, $httpResponse);
	}
}

\run(new DeviceViewTest());
