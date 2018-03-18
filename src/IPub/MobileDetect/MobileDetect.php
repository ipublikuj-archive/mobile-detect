<?php
/**
 * MobileDetect.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:MobileDetect!
 * @subpackage     common
 * @since          1.0.0
 *
 * @date           21.04.14
 */

declare(strict_types = 1);

namespace IPub\MobileDetect;

use Nette\Http;

use IPub\MobileDetect\Helpers;

use Jenssegers\Agent;

/**
 * Mobile detect detector service
 *
 * @package        iPublikuj:MobileDetect!
 * @subpackage     common
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
final class MobileDetect extends Agent\Agent
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
	 * Check if the device is not mobile phone
	 *
	 * @return bool
	 */
	public function isNotPhone() : bool
	{
		return (($this->isMobile() && $this->isTablet()) || !$this->isMobile());
	}

	/**
	 * @return string
	 */
	public function view() : string
	{
		return $this->deviceView->getViewType();
	}

	/**
	 * @return string
	 */
	public function browserVersion() : string
	{
		return (string) $this->version($this->browser());
	}

	/**
	 * @return string
	 */
	public function platformVersion() : string
	{
		return (string) $this->version($this->platform());
	}
}
