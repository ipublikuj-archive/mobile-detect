<?php
/**
 * Helpers.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:MobileDetect!
 * @subpackage     Templating
 * @since          1.0.0
 *
 * @date           16.06.14
 */

declare(strict_types = 1);

namespace IPub\MobileDetect\Templating;

use Nette;

use IPub\MobileDetect;

/**
 * Mobile detect template helpers
 *
 * @package        iPublikuj:MobileDetect!
 * @subpackage     Templating
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
final class Helpers
{
	/**
	 * Implement nette smart magic
	 */
	use Nette\SmartObject;

	/**
	 * @var MobileDetect\MobileDetect
	 */
	private $mobileDetect;

	/**
	 * @var MobileDetect\Helpers\DeviceView
	 */
	private $deviceView;

	/**
	 * @param MobileDetect\MobileDetect $mobileDetect
	 * @param MobileDetect\Helpers\DeviceView $deviceView
	 */
	public function __construct(
		MobileDetect\MobileDetect $mobileDetect,
		MobileDetect\Helpers\DeviceView $deviceView
	) {
		$this->mobileDetect = $mobileDetect;
		$this->deviceView = $deviceView;
	}

	/**
	 * @return MobileDetect\MobileDetect
	 */
	public function getMobileDetectService() : MobileDetect\MobileDetect
	{
		return $this->mobileDetect;
	}

	/**
	 * @return MobileDetect\Helpers\DeviceView
	 */
	public function getDeviceViewService() : MobileDetect\Helpers\DeviceView
	{
		return $this->deviceView;
	}

	/**
	 * @return bool
	 */
	public function isMobile() : bool
	{
		return $this->mobileDetect->isMobile();
	}

	/**
	 * @return bool
	 */
	public function isPhone() : bool
	{
		return $this->mobileDetect->isPhone();
	}

	/**
	 * @return bool
	 */
	public function isTablet() : bool
	{
		return $this->mobileDetect->isTablet();
	}

	/**
	 * @param string $device
	 *
	 * @return bool
	 */
	public function isDevice(string $device) : bool
	{
		return $this->mobileDetect->is($device);
	}

	/**
	 * @param string $os
	 *
	 * @return bool
	 */
	public function isOs(string $os) : bool
	{
		return $this->mobileDetect->is($os);
	}

	/**
	 * @return bool
	 */
	public function isFullView() : bool
	{
		return $this->deviceView->isFullView();
	}

	/**
	 * @return bool
	 */
	public function isMobileView() : bool
	{
		return $this->deviceView->isMobileView();
	}

	/**
	 * @return bool
	 */
	public function isPhoneView() : bool
	{
		return $this->deviceView->isPhoneView();
	}

	/**
	 * @return bool
	 */
	public function isTabletView() : bool
	{
		return $this->deviceView->isTabletView();
	}

	/**
	 * @return bool
	 */
	public function isNotMobileView() : bool
	{
		return $this->deviceView->isNotMobileView();
	}
}
