<?php
/**
 * DeviceView.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:MobileDetect!
 * @subpackage     Helpers
 * @since          1.0.0
 *
 * @date           23.04.14
 */

declare(strict_types = 1);

namespace IPub\MobileDetect\Helpers;

use Nette;
use Nette\Application;
use Nette\Http;
use Nette\Utils;

final class DeviceView extends Nette\Object
{
	const VIEW_MOBILE = 'mobile';
	const VIEW_PHONE = 'phone';
	const VIEW_TABLET = 'tablet';
	const VIEW_FULL = 'full';
	const VIEW_NOT_MOBILE = 'not_mobile';

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
	private $viewType = self::VIEW_FULL;

	/**
	 * @var CookieSettings
	 */
	private $cookieSettings;

	/**
	 * @var string
	 */
	private $switchParameterName = 'device_view';

	/**
	 * @param string $setSwitchParameterName
	 * @param CookieSettings $cookieSettings
	 * @param Http\IRequest $httpRequest
	 * @param Http\IResponse $httpResponse
	 */
	public function __construct(string $setSwitchParameterName, CookieSettings $cookieSettings, Http\IRequest $httpRequest, Http\IResponse $httpResponse)
	{
		$this->cookieSettings = $cookieSettings;
		$this->httpRequest = $httpRequest;
		$this->httpResponse = $httpResponse;

		$this->switchParameterName = $setSwitchParameterName;

		if ($this->httpRequest->getQuery($this->switchParameterName, FALSE)) {
			$this->viewType = $this->httpRequest->getQuery($this->switchParameterName);

		} elseif ($this->httpRequest->getCookie($this->cookieSettings->getName())) {
			$this->viewType = $this->httpRequest->getCookie($this->cookieSettings->getName());
		}
	}

	/**
	 * Gets the view type for a device
	 *
	 * @return string
	 */
	public function getViewType() : string
	{
		return $this->viewType;
	}

	/**
	 * Is the device in full view
	 *
	 * @return bool
	 */
	public function isFullView() : bool
	{
		return $this->viewType === self::VIEW_FULL;
	}

	/**
	 * Is the device a tablet view type
	 *
	 * @return bool
	 */
	public function isTabletView() : bool
	{
		return $this->viewType === self::VIEW_TABLET;
	}

	/**
	 * Is the device a phone view type
	 *
	 * @return bool
	 */
	public function isPhoneView() : bool
	{
		return $this->viewType === self::VIEW_PHONE;
	}

	/**
	 * Is the device a mobile view type
	 *
	 * @return bool
	 */
	public function isMobileView() : bool
	{
		return $this->viewType === self::VIEW_MOBILE || $this->isPhoneView() || $this->isTabletView();
	}

	/**
	 * Is not the device a mobile view type (PC, Mac, etc.)
	 *
	 * @return bool
	 */
	public function isNotMobileView() : bool
	{
		return $this->viewType === self::VIEW_NOT_MOBILE;
	}

	/**
	 * Sets the tablet view type
	 *
	 * @return void
	 */
	public function setTabletView()
	{
		$this->viewType = self::VIEW_TABLET;
	}

	/**
	 * Sets the phone view type
	 *
	 * @return void
	 */
	public function setPhoneView()
	{
		$this->viewType = self::VIEW_PHONE;
	}

	/**
	 * Sets the mobile view type
	 *
	 * @return void
	 */
	public function setMobileView()
	{
		$this->viewType = self::VIEW_MOBILE;
	}

	/**
	 * Sets the not mobile view type
	 *
	 * @return void
	 */
	public function setNotMobileView()
	{
		$this->viewType = self::VIEW_NOT_MOBILE;
	}

	/**
	 * @return string
	 */
	public function getSwitchParameterName() : string
	{
		$this->switchParameterName;
	}

	/**
	 * Gets the switch param value from the query string (GET header)
	 *
	 * @return string
	 */
	public function getSwitchParameterValue() : string
	{
		return $this->httpRequest->getQuery($this->switchParameterName, self::VIEW_FULL);
	}

	/**
	 * Has the Request the switch param in the query string (GET header)
	 *
	 * @return bool
	 */
	public function hasSwitchParameter() : bool
	{
		return $this->httpRequest->getQuery($this->switchParameterName, FALSE) ? TRUE : FALSE;
	}

	/**
	 * Gets the RedirectResponse by switch param value
	 *
	 * @param string $redirectUrl
	 *
	 * @return Application\Responses\RedirectResponse
	 */
	public function getRedirectResponseBySwitchParam(string $redirectUrl) : Application\Responses\RedirectResponse
	{
		$statusCode = 302;

		switch ($this->getSwitchParameterValue()) {
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
	public function modifyNotMobileResponse() : Http\IResponse
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
	public function modifyTabletResponse() : Http\IResponse
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
	public function modifyPhoneResponse() : Http\IResponse
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
	public function modifyMobileResponse() : Http\IResponse
	{
		// Create cookie
		$this->createCookie(self::VIEW_MOBILE);

		return $this->httpResponse;
	}

	/**
	 * Gets the RedirectResponse for phone devices
	 *
	 * @param string $host    Uri host
	 * @param int $statusCode Status code
	 *
	 * @return Application\Responses\RedirectResponse
	 */
	public function getPhoneRedirectResponse(string $host, int $statusCode) : Application\Responses\RedirectResponse
	{
		// Create cookie
		$this->createCookie(self::VIEW_PHONE);

		return new Application\Responses\RedirectResponse($host, $statusCode);
	}

	/**
	 * Gets the RedirectResponse for tablet devices
	 *
	 * @param string $host    Uri host
	 * @param int $statusCode Status code
	 *
	 * @return Application\Responses\RedirectResponse
	 */
	public function getTabletRedirectResponse(string $host, int $statusCode) : Application\Responses\RedirectResponse
	{
		// Create cookie
		$this->createCookie(self::VIEW_TABLET);

		return new Application\Responses\RedirectResponse($host, $statusCode);
	}

	/**
	 * Gets the RedirectResponse for mobile devices
	 *
	 * @param string $host    Uri host
	 * @param int $statusCode Status code
	 *
	 * @return Application\Responses\RedirectResponse
	 */
	public function getMobileRedirectResponse(string $host, int $statusCode) : Application\Responses\RedirectResponse
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
	private function createCookie(string $cookieValue)
	{
		// Store cookie in response
		$this->httpResponse->setCookie(
			$this->cookieSettings->getName(),
			$cookieValue,
			$this->cookieSettings->getExpiresTime(),
			$this->cookieSettings->getPath(),
			$this->cookieSettings->getDomain(),
			$this->cookieSettings->isSecure(),
			$this->cookieSettings->isHttpOnly()
		);
	}
}
