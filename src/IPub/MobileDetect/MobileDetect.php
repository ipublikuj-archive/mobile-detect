<?php
/**
 * MobileDetect.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:MobileDetect!
 * @subpackage     common
 * @since          1.0.0
 *
 * @date           21.04.14
 */

declare(strict_types = 1);

namespace IPub\MobileDetect;

use Nette;
use Nette\Http;

use IPub;
use IPub\MobileDetect\Helpers;
use IPub\MobileDetect\Templating;

/**
 * Mobile detect detector service
 *
 * @package        iPublikuj:MobileDetect!
 * @subpackage     common
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
final class MobileDetect extends \Detection\MobileDetect
{
	/**
	 * @var Helpers\DeviceView
	 */
	private $deviceView;

	/**
	 * @param Helpers\DeviceView $deviceView
	 * @param Http\Request $httpRequest
	 */
	public function __construct(
		Helpers\DeviceView $deviceView,
		Http\Request $httpRequest
	) {
		// Get http headers
		$httpHeaders = $httpRequest->getHeaders();

		$userAgent = NULL;

		// If user agent info is set in headers...
		if (isset($httpHeaders['user-agent'])) {
			// ...set user agent details
			$userAgent = $httpHeaders['user-agent'];
		}

		parent::__construct($httpHeaders, $userAgent);

		$this->deviceView = $deviceView;
	}

	/**
	 * Check if the device is mobile phone
	 *
	 * @return bool
	 */
	public function isPhone() : bool
	{
		return $this->isMobile() && !$this->isTablet();
	}

	/**
	 * Check if the device is not mobile phone
	 *
	 * @return bool
	 */
	public function isNotPhone() : bool
	{
		return (($this->isMobile() && $this->isTablet()) || !$this->isMobile());
	}

	/**
	 * @return Templating\Helpers
	 */
	public function createTemplateHelpers() : Templating\Helpers
	{
		return new Templating\Helpers($this, $this->deviceView);
	}
}
