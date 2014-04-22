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

class MobileDetect extends \Detection\MobileDetect
{
	/**
	 * @param Nette\Http\Request $httpRequest
	 */
	public function __construct(Nette\Http\Request $httpRequest)
	{
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
}