<?php
/**
 * TMobileDetect.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:MobileDetect!
 * @subpackage     common
 * @since          1.0.0
 *
 * @date           24.04.14
 */

declare(strict_types = 1);

namespace IPub\MobileDetect;

use IPub\MobileDetect\Helpers;

/**
 * Mobile detect trait for presenters & components
 *
 * @package        iPublikuj:MobileDetect!
 * @subpackage     common
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
trait TMobileDetect
{
	/**
	 * @var MobileDetect
	 */
	protected $mobileDetect;

	/**
	 * @var Helpers\DeviceView
	 */
	protected $deviceView;

	/**
	 * @param MobileDetect $mobileDetect
	 * @param Helpers\DeviceView $deviceView
	 *
	 * @return void
	 */
	public function injectMobileDetector(MobileDetect $mobileDetect, Helpers\DeviceView $deviceView) : void
	{
		$this->mobileDetect = $mobileDetect;
		$this->deviceView = $deviceView;
	}
}
