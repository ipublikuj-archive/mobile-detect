<?php
/**
 * MobileDetect.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:MobileDetect!
 * @subpackage	common
 * @since		5.0
 *
 * @date		21.04.14
 */

namespace IPub\MobileDetect;

use Nette;
use Nette\Http;

use IPub;
use IPub\MobileDetect\Helpers;
use IPub\MobileDetect\Templating;

class MobileDetect extends \Detection\MobileDetect
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
		$this->deviceView = $deviceView;

		// Get http headers
		$httpHeaders = $httpRequest->getHeaders();

		// Set http headers
		$this->setHttpHeaders($httpHeaders);

		// If user agent info is set in headers...
		if (isset($httpHeaders['user-agent'])) {
			// ...set user agent details
			$this->setUserAgent($httpHeaders['user-agent']);
		}
	}

	/**
	 * @return Templating\Helpers
	 */
	public function createTemplateHelpers()
	{
		return new Templating\Helpers($this, $this->deviceView);
	}
}