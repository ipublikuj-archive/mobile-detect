<?php
/**
 * DeviceView.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:MobileDetect!
 * @subpackage	Helpers
 * @since		5.0
 *
 * @date		23.04.14
 */

namespace IPub\MobileDetect\Helpers;

use Nette;
use Nette\Application;
use Nette\Http;

class DeviceView extends Nette\Object
{
	const SWITCH_PARAM		= 'device_view';

	const VIEW_MOBILE		= 'mobile';
	const VIEW_PHONE		= 'phone';
	const VIEW_TABLET		= 'tablet';
	const VIEW_FULL			= 'full';
	const VIEW_NOT_MOBILE	= 'not_mobile';

	/**
	 * @var Http\IRequest
	 */
	private $httpRequest;

	/**
	 * @var Http\IResponse
	 */
	private $httpResponse;

	/**
	 * @var string
	 */
	private $viewType;
	
	/**
	 * @var array
	 */
	private $cookieConfiguration;

	/**
	 * @param Http\IRequest $httpRequest
	 * @param Http\IResponse $httpResponse
	 */
	public function __construct(Http\IRequest $httpRequest, Http\IResponse $httpResponse)
	{
		$this->httpRequest	= $httpRequest;
		$this->httpResponse	= $httpResponse;
	}

	public function setCookieConfiguration(array $cookieConfiguration){
		$this->cookieConfiguration = $cookieConfiguration;
	}
	
	public function detectViewType(){
		if ($this->httpRequest->getQuery(self::SWITCH_PARAM)) {
			$this->viewType = $this->httpRequest->getQuery(self::SWITCH_PARAM);
		} else if ($this->httpRequest->getCookie($this->cookieConfiguration['name'])) {
			$this->viewType = $this->httpRequest->getCookie($this->cookieConfiguration['name']);
		}
	}
	
	/**
	 * Gets the view type for a device
	 *
	 * @return string
	 */
	public function getViewType()
	{
		return $this->viewType;
	}

	/**
	 * Is the device in full view
	 *
	 * @return boolean
	 */
	public function isFullView()
	{
		return $this->viewType === self::VIEW_FULL;
	}

	/**
	 * Is the device a tablet view type
	 *
	 * @return boolean
	 */
	public function isTabletView()
	{
		return $this->viewType === self::VIEW_TABLET;
	}

	/**
	 * Is the device a phone view type
	 *
	 * @return boolean
	 */
	public function isPhoneView()
	{
		return $this->viewType === self::VIEW_PHONE;
	}

	/**
	 * Is the device a mobile view type
	 *
	 * @return boolean
	 */
	public function isMobileView()
	{
		return $this->viewType === self::VIEW_MOBILE;
	}

	/**
	 * Is not the device a mobile view type (PC, Mac, etc.)
	 *
	 * @return boolean
	 */
	public function isNotMobileView()
	{
		return $this->viewType === self::VIEW_NOT_MOBILE;
	}

	/**
	 * Has the Request the switch param in the query string (GET header).
	 *
	 * @return boolean
	 */
	public function hasSwitchParam()
	{
		return $this->httpRequest->getQuery(self::SWITCH_PARAM);
	}

	/**
	 * Sets the tablet view type
	 *
	 * @return $this
	 */
	public function setTabletView()
	{
		$this->viewType = self::VIEW_TABLET;

		return $this;
	}

	/**
	 * Sets the phone view type
	 *
	 * @return $this
	 */
	public function setPhoneView()
	{
		$this->viewType = self::VIEW_PHONE;

		return $this;
	}

	/**
	 * Sets the mobile view type
	 *
	 * @return $this
	 */
	public function setMobileView()
	{
		$this->viewType = self::VIEW_MOBILE;

		return $this;
	}

	/**
	 * Sets the not mobile view type
	 *
	 * @return $this
	 */
	public function setNotMobileView()
	{
		$this->viewType = self::VIEW_NOT_MOBILE;

		return $this;
	}

	/**
	 * Gets the switch param value from the query string (GET header)
	 *
	 * @return string
	 */
	public function getSwitchParamValue()
	{
		return $this->httpRequest->getQuery(self::SWITCH_PARAM, self::VIEW_FULL);
	}

	/**
	 * Gets the RedirectResponse by switch param value.
	 *
	 * @param string $redirectUrl
	 *
	 * @return Application\Responses\RedirectResponse
	 */
	public function getRedirectResponseBySwitchParam($redirectUrl)
	{
		$statusCode = 302;

		switch ($this->getSwitchParamValue())
		{
			case self::VIEW_MOBILE:
				$this->createCookie(self::VIEW_MOBILE);
				break;

			case self::VIEW_PHONE:
				$this->createCookie(self::VIEW_PHONE);
				break;

			case self::VIEW_TABLET:
				$this->createCookie(self::VIEW_TABLET);
				break;

			default:
				$this->createCookie(self::VIEW_FULL);
				break;
		}

		return new Application\Responses\RedirectResponse($redirectUrl, $statusCode);
	}

	/**
	 * Modifies the Response for non-mobile devices
	 *
	 * @return Http\IResponse
	 */
	public function modifyNotMobileResponse()
	{
		// Create cookie
		$this->createCookie(self::VIEW_NOT_MOBILE);

		return $this->httpResponse;
	}

	/**
	 * Modifies the Response for tablet devices
	 *
	 * @return Http\IResponse
	 */
	public function modifyTabletResponse()
	{
		// Create cookie
		$this->createCookie(self::VIEW_TABLET);

		return $this->httpResponse;
	}

	/**
	 * Modifies the Response for phone devices
	 *
	 * @return Http\IResponse
	 */
	public function modifyPhoneResponse()
	{
		// Create cookie
		$this->createCookie(self::VIEW_PHONE);

		return $this->httpResponse;
	}

	/**
	 * Modifies the Response for mobile devices
	 *
	 * @return Http\IResponse
	 */
	public function modifyMobileResponse()
	{
		// Create cookie
		$this->createCookie(self::VIEW_MOBILE);

		return $this->httpResponse;
	}

	/**
	 * Gets the RedirectResponse for tablet devices.
	 *
	 * @param string	$host			Uri host
	 * @param int		$statusCode		Status code
	 *
	 * @return Application\Responses\RedirectResponse
	 */
	public function getTabletRedirectResponse($host, $statusCode)
	{
		// Create cookie
		$this->createCookie(self::VIEW_TABLET);

		return new Application\Responses\RedirectResponse($host, $statusCode);
	}

	/**
	 * Gets the RedirectResponse for mobile devices.
	 *
	 * @param string	$host			Uri host
	 * @param int		$statusCode		Status code
	 *
	 * @return Application\Responses\RedirectResponse
	 */
	public function getMobileRedirectResponse($host, $statusCode)
	{
		// Create cookie
		$this->createCookie(self::VIEW_MOBILE);

		return new Application\Responses\RedirectResponse($host, $statusCode);
	}

	/**
	 * Gets the cookie
	 *
	 * @param string $cookieValue
	 */
	protected function createCookie($cookieValue)
	{		
		// Store cookie in response
		$this->httpResponse->setCookie(
			$this->cookieConfiguration['name'],
			$cookieValue,
			\Nette\Utils\DateTime::from($this->cookieConfiguration['expirationAfter'])->format('U'),
			$this->cookieConfiguration['path'],
			$this->cookieConfiguration['domain'],
			$this->cookieConfiguration['secure'],
			$this->cookieConfiguration['httpOnly']
		);
	}
}
