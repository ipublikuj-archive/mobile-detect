<?php
/**
 * Helpers.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:MobileDetect!
 * @subpackage     Templating
 * @since          1.0.0
 *
 * @date           16.06.14
 */

declare(strict_types = 1);

namespace IPub\MobileDetect\Templating;

use Nette;

use IPub;
use IPub\MobileDetect;

/**
 * Mobile detect template helpers
 *
 * @package        iPublikuj:MobileDetect!
 * @subpackage     Templating
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
final class Helpers extends Nette\Object
{
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
	public function isTablet() : bool
	{
		return $this->mobileDetect->isTablet();
	}
}
