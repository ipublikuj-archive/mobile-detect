<?php
/**
 * OnRequestHandler.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:History!
 * @subpackage	common
 * @since		5.0
 *
 * @date		31.01.14
 */

namespace IPub\History;

use Nette\Http;

class OnRequestHandler
{
	/**
	 * @var Http\IRequest
	 */
	private $httpRequest;

	/**
	 * @var OnResponseHandler
	 */
	private $onResponseHandler;

	/**
	 * @param Http\IRequest $httpRequest
	 * @param OnResponseHandler $onResponseHandler
	 */
	public function __construct(Http\IRequest $httpRequest, OnResponseHandler $onResponseHandler)
	{
		$this->httpRequest = $httpRequest;
		$this->onResponseHandler = $onResponseHandler;
	}

	/**
	 * @param $application
	 */
	public function __invoke($application)
	{
		if ($this->httpRequest->isAjax() && count($application->getRequests()) > 1) {
			$this->onResponseHandler->markForward();
		}
	}
}