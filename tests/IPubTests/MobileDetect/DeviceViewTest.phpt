<?php
/**
 * Test: IPub\MobileDetect\DeviceView
 * @testCase
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:MobileDetect!
 * @subpackage	Tests
 * @since		5.0
 *
 * @date		10.01.15
 */

namespace IPub\Forms\Slug;

use Nette;

use Tester;
use Tester\Assert;

use IPub;
use IPub\MobileDetect;

require __DIR__ . '/../bootstrap.php';

class DeviceViewTest extends Tester\TestCase
{


	public function testRequestHasSwitchParam()
	{
		$query = [
			'myparam'		=> 'myvalue',
			'device_view'	=> MobileDetect\Helpers\DeviceView::VIEW_MOBILE
		];

		// Get helper
		$deviceView = $this->getHelper($query);

		Assert::equal(MobileDetect\Helpers\DeviceView::VIEW_MOBILE, $deviceView->hasSwitchParam());
		Assert::equal(MobileDetect\Helpers\DeviceView::VIEW_MOBILE, $deviceView->getSwitchParamValue());
	}

	public function testDeviceIsMobile()
	{
		$query = [
			'myparam'		=> 'myvalue',
			'device_view'	=> MobileDetect\Helpers\DeviceView::VIEW_MOBILE
		];

		// Get helper
		$deviceView = $this->getHelper($query);

		Assert::equal(MobileDetect\Helpers\DeviceView::VIEW_MOBILE, $deviceView->getViewType());
		Assert::true($deviceView->isMobileView());
		Assert::false($deviceView->isFullView());
	}

	public function testMobileViewType()
	{
		$query = [
			'myparam'		=> 'myvalue',
			'device_view'	=> MobileDetect\Helpers\DeviceView::VIEW_MOBILE
		];

		// Get helper
		$deviceView = $this->getHelper($query);

		Assert::equal(MobileDetect\Helpers\DeviceView::VIEW_MOBILE, $deviceView->getViewType());
	}

	public function testSetMobileViewType()
	{
		$query = [
			'myparam'		=> 'myvalue',
			'device_view'	=> MobileDetect\Helpers\DeviceView::VIEW_MOBILE
		];

		// Get helper
		$deviceView = $this->getHelper($query);

		Assert::true($deviceView->setTabletView() instanceof MobileDetect\Helpers\DeviceView);
		Assert::notEqual(MobileDetect\Helpers\DeviceView::VIEW_MOBILE, $deviceView->getViewType());
	}

	/**
	 * Create DeviceView helper
	 *
	 * @param array $query
	 *
	 * @return MobileDetect\Helpers\DeviceView
	 */
	private function getHelper($query = [])
	{
		$httpRequest	= new Nette\Http\Request(new Nette\Http\UrlScript('http://www.ipublikuj.eu' . ($query ? '?' . http_build_query($query) : '')), $query, []);
		$httpResponse	= new Nette\Http\Response();

		// Get helper
		return new MobileDetect\Helpers\DeviceView($httpRequest, $httpResponse);
	}
}

\run(new DeviceViewTest());