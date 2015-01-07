<?php
/**
 * OnRequestHandler.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:MobileDetect!
 * @subpackage	Events
 * @since		5.0
 *
 * @date		22.04.14
 */

namespace IPub\MobileDetect\Events;

use Nette\Application;
use Nette\Application\Responses;
use Nette\Http;

use IPub\MobileDetect\MobileDetect;
use IPub\MobileDetect\Helpers\DeviceView;

class OnRequestHandler
{
	const REDIRECT				= 'redirect';
	const NO_REDIRECT			= 'noRedirect';
	const REDIRECT_WITHOUT_PATH	= 'redirectWithoutPath';

	const MOBILE	= 'mobile';
	const TABLET	= 'tablet';

	/**
	 * @var array()
	 */
	public $redirectConf = array();

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
	 * @var MobileDetect
	 */
	private $mobileDetect;

	/**
	 * @var DeviceView
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
	 * @param MobileDetect $mobileDetect
	 * @param DeviceView $deviceView
	 */
	public function __construct(
		Http\IRequest $httpRequest,
		Http\IResponse $httpResponse,
		Application\IRouter $router,
		OnResponseHandler $onResponseHandler,
		MobileDetect $mobileDetect,
		DeviceView $deviceView
	) {
		$this->httpRequest	= $httpRequest;
		$this->httpResponse	= $httpResponse;

		$this->router = $router;

		$this->onResponseHandler = $onResponseHandler;

		$this->mobileDetect	= $mobileDetect;
		$this->deviceView	= $deviceView;
	}

	/**
	 * @param Application\Application $application
	 */
	public function __invoke(Application\Application $application)
	{
		// Redirect only normal request
		if ($this->httpRequest->isAjax()) {
			return;
		}

		// Sets the flag for the response handled by the GET switch param and the type of the view.
		if ($this->deviceView->hasSwitchParam()) {
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

		// Redirects to the tablet version and set the 'tablet' device view in a cookie.
		if ($this->hasTabletRedirect()) {
			if ($response = $this->getTabletRedirectResponse()) {
				$response->send($this->httpRequest, $this->httpResponse);
				exit();
			}

			return;
		}

		// Redirects to the mobile version and set the 'mobile' device view in a cookie.
		if ($this->hasMobileRedirect()) {
			if ($response = $this->getMobileRedirectResponse()) {
				$response->send($this->httpRequest, $this->httpResponse);
				exit();
			}

			return;
		}

		// No need to redirect

		// Sets the flag for the response handler
		$this->onResponseHandler->needModifyResponse();

		// Checking the need to modify the Response and set closure
		if ($this->needTabletResponseModify()) {
			$this->deviceView->setTabletView();

			return;
		}

		// Checking the need to modify the Response and set closure
		if ($this->needPhoneResponseModify()) {
			$this->deviceView->setPhoneView();

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
	 * Detects tablet redirections
	 *
	 * @return boolean
	 */
	protected function hasTabletRedirect()
	{
		if (!$this->redirectConf['tablet']['isEnabled']) {
			return FALSE;
		}

		$isTablet = $this->mobileDetect->isTablet();
		$isTabletHost = ($this->getCurrentHost() === $this->redirectConf['tablet']['host']);

		if ($isTablet && !$isTabletHost && ($this->getRoutingOption(self::TABLET) != self::NO_REDIRECT)) {
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Detects mobile redirections
	 *
	 * @return boolean
	 */
	protected function hasMobileRedirect()
	{

		if (!$this->redirectConf['mobile']['isEnabled']) {
			return false;
		}

		if ($this->redirectConf['detectTabletAsMobile'] === FALSE) {
			$isMobile = $this->mobileDetect->isMobile() && !$this->mobileDetect->isTablet();

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
	 * If a modified Response for tablet devices is needed
	 *
	 * @return boolean
	 */
	protected function needTabletResponseModify()
	{
		if (($this->deviceView->getViewType() === NULL || $this->deviceView->isTabletView()) && $this->mobileDetect->isTablet()) {
			$this->onResponseHandler->modifyResponseClosure = function($deviceView) {
				return $deviceView->modifyTabletResponse();
			};

			return TRUE;
		}

		return FALSE;
	}

	/**
	 * If a modified Response for phone devices is needed
	 *
	 * @return boolean
	 */
	protected function needPhoneResponseModify()
	{
		if (($this->deviceView->getViewType() === NULL || $this->deviceView->isPhoneView()) && $this->mobileDetect->isMobile() && !$this->mobileDetect->isTablet()) {
			$this->onResponseHandler->modifyResponseClosure = function($deviceView) {
				return $deviceView->modifyPhoneResponse();
			};

			return TRUE;
		}

		return FALSE;
	}

	/**
	 * If a modified Response for mobile devices is needed
	 *
	 * @return boolean
	 */
	protected function needMobileResponseModify()
	{
		if (($this->deviceView->getViewType() === NULL || $this->deviceView->isMobileView()) && $this->mobileDetect->isMobile()) {
			$this->onResponseHandler->modifyResponseClosure = function($deviceView) {
				return $deviceView->modifyMobileResponse();
			};

			return TRUE;
		}

		return FALSE;
	}

	/**
	 * If a modified Response for non-mobile devices is needed
	 *
	 * @return boolean
	 */
	protected function needNotMobileResponseModify()
	{
		if ($this->deviceView->getViewType() === NULL || $this->deviceView->isNotMobileView()) {
			$this->onResponseHandler->modifyResponseClosure = function($deviceView) {
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
	protected function getRedirectResponseBySwitchParam()
	{
		// Generate full url path
		if ($this->isFullPath === TRUE) {
			// Get actual url
			$url = $this->httpRequest->getUrl();

			// Create full path url
			$redirectUrl = $this->getCurrentHost() . $url->getPathInfo();

		// Generate only domain path
		} else {
			$redirectUrl = $this->getCurrentHost();
		}

		return $this->deviceView->getRedirectResponseBySwitchParam($redirectUrl);
	}

	/**
	 * Gets the mobile RedirectResponse
	 *
	 * @return Responses\RedirectResponse
	 */
	protected function getMobileRedirectResponse()
	{
		if ($host = $this->getRedirectUrl(self::MOBILE)) {
			return $this->deviceView->getMobileRedirectResponse(
				$host,
				$this->redirectConf[self::MOBILE]['statusCode']
			);
		}
	}

	/**
	 * Gets the tablet RedirectResponse
	 *
	 * @return Responses\RedirectResponse
	 */
	protected function getTabletRedirectResponse()
	{
		if ($host = $this->getRedirectUrl(self::TABLET)) {
			return $this->deviceView->getTabletRedirectResponse(
				$host,
				$this->redirectConf[self::TABLET]['statusCode']
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
	protected function getRedirectUrl($platform)
	{
		if ($routingOption = $this->getRoutingOption($platform)) {
			switch ($routingOption)
			{
				case self::REDIRECT:
					return rtrim($this->redirectConf[$platform]['host'], '/') .'/'. ltrim($this->httpRequest->getUrl()->getRelativeUrl(), '/');
					break;

				case self::REDIRECT_WITHOUT_PATH:
					return  $this->redirectConf[$platform]['host'];
					break;
			}
		}
	}

	/**
	 * Gets named option from current route
	 *
	 * @param string $name
	 *
	 * @return string|null
	 */
	protected function getRoutingOption($name)
	{
		$option = NULL;

		// Get actual route
		$route = $this->router->match($this->httpRequest);

		if ($route instanceof Route) {
			$option = $route->getOption($name);
		}

		if (!$option) {
			$option = $this->redirectConf[$name]['action'];
		}

		if (in_array($option, array(self::REDIRECT, self::REDIRECT_WITHOUT_PATH, self::NO_REDIRECT))) {
			return $option;
		}

		return null;
	}

	/**
	 * Gets the current host
	 *
	 * @return string
	 */
	protected function getCurrentHost()
	{
		return $this->httpRequest->getUrl()->getHostUrl() . $this->httpRequest->getUrl()->getScriptPath();
	}
}