<?php
/**
 * OnRequestHandler.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:MobileDetect!
 * @subpackage     Events
 * @since          1.0.0
 *
 * @date           22.04.14
 */

declare(strict_types = 1);

namespace IPub\MobileDetect\Events;

use Nette\Application;
use Nette\Application\Responses;
use Nette\Http;

use IPub\MobileDetect;
use IPub\MobileDetect\Helpers;

/**
 * On request event handler
 *
 * @package        iPublikuj:MobileDetect!
 * @subpackage     Events
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
final class OnRequestHandler
{
	const REDIRECT = 'redirect';
	const NO_REDIRECT = 'noRedirect';
	const REDIRECT_WITHOUT_PATH = 'redirectWithoutPath';

	const MOBILE = 'mobile';
	const TABLET = 'tablet';
	const PHONE = 'phone';

	/**
	 * @var array
	 */
	public $redirectConf = [];

	/**
	 * @var bool
	 */
	public $isFullPath = TRUE;

	/**
	 * @var Http\IRequest
	 */
	private $httpRequest;

	/**
	 * @var Http\IResponse
	 */
	private $httpResponse;

	/**
	 * @var Application\IRouter
	 */
	private $router;

	/**
	 * @var MobileDetect\MobileDetect
	 */
	private $mobileDetect;

	/**
	 * @var Helpers\DeviceView
	 */
	private $deviceView;

	/**
	 * @var OnResponseHandler
	 */
	private $onResponseHandler;

	/**
	 * @param Http\IRequest $httpRequest
	 * @param Http\IResponse $httpResponse
	 * @param Application\IRouter $router
	 * @param OnResponseHandler $onResponseHandler
	 * @param MobileDetect\MobileDetect $mobileDetect
	 * @param Helpers\DeviceView $deviceView
	 */
	public function __construct(
		Http\IRequest $httpRequest,
		Http\IResponse $httpResponse,
		Application\IRouter $router,
		OnResponseHandler $onResponseHandler,
		MobileDetect\MobileDetect $mobileDetect,
		Helpers\DeviceView $deviceView
	) {
		$this->httpRequest = $httpRequest;
		$this->httpResponse = $httpResponse;

		$this->router = $router;

		$this->onResponseHandler = $onResponseHandler;

		$this->mobileDetect = $mobileDetect;
		$this->deviceView = $deviceView;
	}

	/**
	 * @param Application\Application $application
	 *
	 * @return void
	 */
	public function __invoke(Application\Application $application)
	{
		// Redirect only normal request
		if ($this->httpRequest->isAjax()) {
			return;
		}

		// Sets the flag for the response handled by the GET switch param and the type of the view.
		if ($this->deviceView->hasSwitchParameter()) {
			if ($response = $this->getRedirectResponseBySwitchParam()) {
				$response->send($this->httpRequest, $this->httpResponse);
				exit();
			}

			return;
		}

		// If the device view is either the full view or not the mobile view
		if ($this->deviceView->isFullView() || $this->deviceView->isNotMobileView()) {
			return;
		}

		// Redirects to the phone version and set the 'phone' device view in a cookie.
		if ($this->hasPhoneRedirect()) {
			if ($response = $this->getDeviceRedirectResponse(self::PHONE)) {
				$response->send($this->httpRequest, $this->httpResponse);
				exit();
			}

			return;
		}

		// Redirects to the tablet version and set the 'tablet' device view in a cookie.
		if ($this->hasTabletRedirect()) {
			if ($response = $this->getDeviceRedirectResponse(self::TABLET)) {
				$response->send($this->httpRequest, $this->httpResponse);
				exit();
			}

			return;
		}

		// Redirects to the mobile version and set the 'mobile' device view in a cookie.
		if ($this->hasMobileRedirect()) {
			if ($response = $this->getDeviceRedirectResponse(self::MOBILE)) {
				$response->send($this->httpRequest, $this->httpResponse);
				exit();
			}

			return;
		}

		// No need to redirect

		// Sets the flag for the response handler
		$this->onResponseHandler->needModifyResponse();

		// Checking the need to modify the Response and set closure
		if ($this->needPhoneResponseModify()) {
			$this->deviceView->setPhoneView();

			return;
		}

		// Checking the need to modify the Response and set closure
		if ($this->needTabletResponseModify()) {
			$this->deviceView->setTabletView();

			return;
		}

		// Sets the closure modifier mobile Response
		if ($this->needMobileResponseModify()) {
			$this->deviceView->setMobileView();

			return;
		}

		// Sets the closure modifier not_mobile Response
		if ($this->needNotMobileResponseModify()) {
			$this->deviceView->setNotMobileView();

			return;
		}
	}

	/**
	 * Detects phone redirections
	 *
	 * @return bool
	 */
	private function hasPhoneRedirect() : bool
	{
		if (!$this->redirectConf['phone']['isEnabled']) {
			return FALSE;
		}

		$isPhone = $this->mobileDetect->isPhone();

		if ($this->redirectConf['detectPhoneAsMobile'] === FALSE) {
			$isPhoneHost = ($this->getCurrentHost() === $this->redirectConf['phone']['host']);

			if ($isPhone && !$isPhoneHost && ($this->getRoutingOption(self::PHONE) != self::NO_REDIRECT)) {
				return TRUE;
			}

		} else {
			$isMobileHost = ($this->getCurrentHost() === $this->redirectConf['mobile']['host']);

			if ($isPhone && !$isMobileHost && ($this->getRoutingOption(self::PHONE) != self::NO_REDIRECT)) {
				return TRUE;
			}
		}

		return FALSE;
	}

	/**
	 * Detects tablet redirections
	 *
	 * @return bool
	 */
	private function hasTabletRedirect() : bool
	{
		if (!$this->redirectConf['tablet']['isEnabled']) {
			return FALSE;
		}

		$isTablet = $this->mobileDetect->isTablet();

		if ($this->redirectConf['detectTabletAsMobile'] === FALSE) {
			$isTabletHost = ($this->getCurrentHost() === $this->redirectConf['tablet']['host']);

			if ($isTablet && !$isTabletHost && ($this->getRoutingOption(self::TABLET) != self::NO_REDIRECT)) {
				return TRUE;
			}

		} else {
			$isMobileHost = ($this->getCurrentHost() === $this->redirectConf['mobile']['host']);

			if ($isTablet && !$isMobileHost && ($this->getRoutingOption(self::TABLET) != self::NO_REDIRECT)) {
				return TRUE;
			}
		}

		return FALSE;
	}

	/**
	 * Detects mobile redirections
	 *
	 * @return bool
	 */
	private function hasMobileRedirect() : bool
	{
		if (!$this->redirectConf['mobile']['isEnabled']) {
			return FALSE;
		}

		if ($this->redirectConf['detectPhoneAsMobile'] === FALSE) {
			$isMobile = ($this->mobileDetect->isTablet() || ($this->mobileDetect->isMobile()) && !$this->mobileDetect->isPhone());

		} elseif ($this->redirectConf['detectTabletAsMobile'] === FALSE) {
			$isMobile = ($this->mobileDetect->isPhone() || ($this->mobileDetect->isMobile()) && !$this->mobileDetect->isTablet());

		} else {
			$isMobile = $this->mobileDetect->isMobile();
		}

		$isMobileHost = ($this->getCurrentHost() === $this->redirectConf['mobile']['host']);

		if ($isMobile && !$isMobileHost && ($this->getRoutingOption(self::MOBILE) != self::NO_REDIRECT)) {
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * If a modified Response for phone devices is needed
	 *
	 * @return bool
	 */
	private function needPhoneResponseModify() : bool
	{
		if (($this->deviceView->getViewType() === NULL || $this->deviceView->isPhoneView()) && $this->mobileDetect->isMobile() && !$this->mobileDetect->isTablet()) {
			$this->onResponseHandler->modifyResponseClosure = function ($deviceView) {
				return $deviceView->modifyPhoneResponse();
			};

			return TRUE;
		}

		return FALSE;
	}

	/**
	 * If a modified Response for tablet devices is needed
	 *
	 * @return bool
	 */
	private function needTabletResponseModify() : bool
	{
		if (($this->deviceView->getViewType() === NULL || $this->deviceView->isTabletView()) && $this->mobileDetect->isTablet()) {
			$this->onResponseHandler->modifyResponseClosure = function (Helpers\DeviceView $deviceView) {
				return $deviceView->modifyTabletResponse();
			};

			return TRUE;
		}

		return FALSE;
	}

	/**
	 * If a modified Response for mobile devices is needed
	 *
	 * @return bool
	 */
	private function needMobileResponseModify() : bool
	{
		if (($this->deviceView->getViewType() === NULL || $this->deviceView->isMobileView()) && $this->mobileDetect->isMobile()) {
			$this->onResponseHandler->modifyResponseClosure = function (Helpers\DeviceView $deviceView) {
				return $deviceView->modifyMobileResponse();
			};

			return TRUE;
		}

		return FALSE;
	}

	/**
	 * If a modified Response for non-mobile devices is needed
	 *
	 * @return bool
	 */
	private function needNotMobileResponseModify() : bool
	{
		if ($this->deviceView->getViewType() === NULL || $this->deviceView->isNotMobileView()) {
			$this->onResponseHandler->modifyResponseClosure = function (Helpers\DeviceView $deviceView) {
				return $deviceView->modifyNotMobileResponse();
			};

			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Gets the RedirectResponse by switch param
	 *
	 * @return Responses\RedirectResponse
	 */
	private function getRedirectResponseBySwitchParam() : Responses\RedirectResponse
	{
		// Generate full url path
		if ($this->isFullPath === TRUE) {
			// Get actual url
			$url = $this->httpRequest->getUrl();

			// Remove switch param
			$url->setQueryParameter($this->deviceView->getSwitchParameterName(), NULL);

			// Create full path url
			$redirectUrl = $this->getCurrentHost() . $url->getRelativeUrl();

			// Generate only domain path
		} else {
			$redirectUrl = $this->getCurrentHost();
		}

		return $this->deviceView->getRedirectResponseBySwitchParam($redirectUrl);
	}

	/**
	 * Gets the device RedirectResponse
	 * 
	 * @param string $device
	 *
	 * @return Responses\RedirectResponse
	 */
	private function getDeviceRedirectResponse(string $device) : Responses\RedirectResponse
	{
		if ($host = $this->getRedirectUrl($device)) {
			return $this->deviceView->getMobileRedirectResponse(
				$host,
				$this->redirectConf[$device]['statusCode']
			);
		}
	}

	/**
	 * Gets the redirect url
	 *
	 * @param string $platform
	 *
	 * @return string
	 */
	private function getRedirectUrl(string $platform) : string
	{
		if ($routingOption = $this->getRoutingOption($platform)) {
			switch ($routingOption) {
				case self::REDIRECT:
					return rtrim($this->redirectConf[$platform]['host'], '/') . '/' . ltrim($this->httpRequest->getUrl()->getRelativeUrl(), '/');

				case self::REDIRECT_WITHOUT_PATH:
					return $this->redirectConf[$platform]['host'];
			}
		}
	}

	/**
	 * Gets named option from current route
	 *
	 * @param string $name
	 *
	 * @return string|NULL
	 */
	private function getRoutingOption(string $name)
	{
		$option = NULL;

		// Get actual route
		$request = $this->router->match($this->httpRequest);

		if ($request instanceof Application\Request) {
			$params = $request->getParameters();
			$option = isset($params[$name]) ? $params[$name] : NULL;
		}

		if (!$option) {
			$option = $this->redirectConf[$name]['action'];
		}

		if (in_array($option, [self::REDIRECT, self::REDIRECT_WITHOUT_PATH, self::NO_REDIRECT])) {
			return $option;
		}

		return NULL;
	}

	/**
	 * Gets the current host
	 *
	 * @return string
	 */
	private function getCurrentHost() : string
	{
		return $this->httpRequest->getUrl()->getHostUrl() . $this->httpRequest->getUrl()->getScriptPath();
	}
}
