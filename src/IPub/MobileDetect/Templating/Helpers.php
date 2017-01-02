<?php
/**
 * Helpers.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:MobileDetect!
 * @subpackage	Templating
 * @since		5.0
 *
 * @date		16.06.14
 */

namespace IPub\MobileDetect\Templating;

use Nette;

use IPub;
use IPub\MobileDetect;

class Helpers extends Nette\Object
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
		$this->mobileDetect	= $mobileDetect;
		$this->deviceView	= $deviceView;
	}

	public function loader($method)
	{
		if ( method_exists($this, $method) ) {
			return callback($this, $method);
		}
	}

	/**
	 * @return MobileDetect\MobileDetect
	 */
	public function getMobileDetectService()
	{
		return $this->mobileDetect;
	}

	/**
	 * @return MobileDetect\Helpers\DeviceView
	 */
	public function getDeviceViewService()
	{
		return $this->deviceView;
	}

	public function isMobile()
	{
		return $this->mobileDetect->isMobile();
	}

	public function isTablet()
	{
		return $this->mobileDetect->isTablet();
	}
}
