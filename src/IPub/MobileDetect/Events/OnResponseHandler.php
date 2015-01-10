<?php
/**
 * OnResponseHandler.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:MobileDetect!
 * @subpackage	Events
 * @since		5.0
 *
 * @date		23.04.14
 */

namespace IPub\MobileDetect\Events;

use Nette\Application;
use Nette\Http;

use IPub\MobileDetect\Helpers\DeviceView;

class OnResponseHandler
{
	/**
	 * @var bool
	 */
	private $needModifyResponse = FALSE;

	/**
	 * @var DeviceView
	 */
	private $deviceView;

	/**
	 * @var \Closure
	 */
	public $modifyResponseClosure;

	/**
	 * @param DeviceView $deviceView
	 */
	public function __construct(DeviceView $deviceView)
	{
		$this->deviceView = $deviceView;
	}

	/**
	 * Stores information about modifying response
	 */
	public function needModifyResponse()
	{
		$this->needModifyResponse = TRUE;
	}

	/**
	 * @param Application\Application $application
	 */
	public function __invoke(Application\Application $application)
	{
		if ($this->needModifyResponse && $this->modifyResponseClosure instanceof \Closure) {
			$modifyClosure = $this->modifyResponseClosure;
			$modifyClosure($this->deviceView);

			return;
		}
	}
}