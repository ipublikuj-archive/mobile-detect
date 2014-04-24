<?php
/**
 * TMobileDetect.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:MobileDetect!
 * @subpackage	common
 * @since		5.0
 *
 * @date		24.04.14
 */

namespace IPub\MobileDetect;

use Nette;

use IPub\MobileDetect\Helpers

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
	 */
	public function injectMobileDetector(MobileDetect $mobileDetect, Helpers\DeviceView $deviceView)
	{
		$this->mobileDetect	= $mobileDetect;
		$this->deviceView	= $deviceView;
	}
}