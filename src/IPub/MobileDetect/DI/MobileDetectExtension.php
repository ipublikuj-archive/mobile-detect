<?php
/**
 * MobileDetectExtension.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:MobileDetect!
 * @subpackage     DI
 * @since          1.0.0
 *
 * @date           21.04.14
 */

declare(strict_types = 1);

namespace IPub\MobileDetect\DI;

use Nette;
use Nette\Bridges;
use Nette\DI;
use Nette\PhpGenerator as Code;

use IPub;
use IPub\MobileDetect;
use IPub\MobileDetect\Events;
use IPub\MobileDetect\Helpers;
use IPub\MobileDetect\Templating;

/**
 * Mobile device detect extension container
 *
 * @package        iPublikuj:MobileDetect!
 * @subpackage     DI
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
final class MobileDetectExtension extends DI\CompilerExtension
{
	/**
	 * @var array
	 */
	private $defaults = [
		'redirect'         => [
			'mobile'               => [
				'isEnabled'  => FALSE,
				'host'       => NULL,
				'statusCode' => 301,
				'action'     => 'noRedirect',    // redirect/noRedirect/redirectWithoutPath
			],
			'phone'                => [
				'isEnabled'  => FALSE,
				'host'       => NULL,
				'statusCode' => 301,
				'action'     => 'noRedirect',    // redirect/noRedirect/redirectWithoutPath
			],
			'tablet'               => [
				'isEnabled'  => FALSE,
				'host'       => NULL,
				'statusCode' => 301,
				'action'     => 'noRedirect',    // redirect/noRedirect/redirectWithoutPath
			],
			'detectPhoneAsMobile'  => FALSE,
			'detectTabletAsMobile' => FALSE,
		],
		'switchDeviceView' => [
			'saveRefererPath' => TRUE
		],
		'switchParameterName' => 'device_view',
		'deviceViewCookie' => [
			'name'        => 'device_view',
			'domain'      => NULL,
			'expireAfter' => '+1 month',
			'path'        => '/',
			'secure'      => FALSE,
			'httpOnly'    => TRUE,
		],
		'debugger' => '%debugMode%'
	];

	/**
	 * @return void
	 */
	public function loadConfiguration() : void
	{
		// Get container builder
		$builder = $this->getContainerBuilder();
		// Get extension configuration
		$configuration = $this->getConfig($this->defaults);

		// Install mobile detect service
		$mobileDetect = $builder->addDefinition($this->prefix('mobileDetect'))
			->setClass(MobileDetect\MobileDetect::class);

		$builder->addDefinition($this->prefix('deviceView'))
			->setClass(Helpers\DeviceView::class)
			->setArguments(['setSwitchParameterName' => $configuration['switchParameterName']]);

		$builder->addDefinition($this->prefix('cookieSettings'))
			->setClass(Helpers\CookieSettings::class)
			->setArguments([
				'name'        => $configuration['deviceViewCookie']['name'],
				'domain'      => $configuration['deviceViewCookie']['domain'],
				'expireAfter' => $configuration['deviceViewCookie']['expireAfter'],
				'path'        => $configuration['deviceViewCookie']['path'],
				'secure'      => $configuration['deviceViewCookie']['secure'],
				'httpOnly'    => $configuration['deviceViewCookie']['httpOnly'],
			]);

		if ($configuration['debugger'] && interface_exists('Tracy\IBarPanel')) {
			$builder->addDefinition($this->prefix('panel'))
				->setClass('IPub\MobileDetect\Diagnostics\Panel');

			$mobileDetect->addSetup('?->register(?)', [$this->prefix('@panel'), '@self']);
		}

		$builder->addDefinition($this->prefix('onRequestHandler'))
			->setClass(Events\OnRequestHandler::class)
			->addSetup('$redirectConf', [$configuration['redirect']])
			->addSetup('$isFullPath', [$configuration['switchDeviceView']['saveRefererPath']]);

		$builder->addDefinition($this->prefix('onResponseHandler'))
			->setClass(Events\OnResponseHandler::class);

		// Register template helpers
		$builder->addDefinition($this->prefix('helpers'))
			->setClass(Templating\Helpers::class)
			->setAutowired(FALSE);

		$application = $builder->getDefinition('application');
		$application->addSetup('$service->onRequest[] = ?', ['@' . $this->prefix('onRequestHandler')]);
		$application->addSetup('$service->onResponse[] = ?', ['@' . $this->prefix('onResponseHandler')]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function beforeCompile() : void
	{
		parent::beforeCompile();

		$builder = $this->getContainerBuilder();

		// Install extension latte macros
		$latteFactory = $builder->getDefinition($builder->getByType(Bridges\ApplicationLatte\ILatteFactory::class) ?: 'nette.latteFactory');

		$latteFactory
			->addSetup('IPub\MobileDetect\Latte\Macros::install(?->getCompiler())', ['@self'])
			->addSetup('addFilter', ['isMobile', [$this->prefix('@helpers'), 'isMobile']])
			->addSetup('addFilter', ['isPhone', [$this->prefix('@helpers'), 'isPhone']])
			->addSetup('addFilter', ['isTablet', [$this->prefix('@helpers'), 'isTablet']])
			->addSetup('addFilter', ['isDevice', [$this->prefix('@helpers'), 'isDevice']])
			->addSetup('addFilter', ['isOs', [$this->prefix('@helpers'), 'isOs']])
			->addSetup('addFilter', ['isFullView', [$this->prefix('@helpers'), 'isFullView']])
			->addSetup('addFilter', ['isMobileView', [$this->prefix('@helpers'), 'isMobileView']])
			->addSetup('addFilter', ['isPhoneView', [$this->prefix('@helpers'), 'isPhoneView']])
			->addSetup('addFilter', ['isTabletView', [$this->prefix('@helpers'), 'isTabletView']])
			->addSetup('addFilter', ['isNotMobileView', [$this->prefix('@helpers'), 'isNotMobileView']])
			->addSetup('addFilter', ['getMobileDetectService', [$this->prefix('@helpers'), 'getMobileDetectService']])
			->addSetup('addFilter', ['getDeviceViewService', [$this->prefix('@helpers'), 'getDeviceViewService']]);
	}

	/**
	 * @param Nette\Configurator $config
	 * @param string $extensionName
	 *
	 * @return void
	 */
	public static function register(Nette\Configurator $config, string $extensionName = 'mobileDetect')
	{
		$config->onCompile[] = function (Nette\Configurator $config, Nette\DI\Compiler $compiler) use ($extensionName) : void {
			$compiler->addExtension($extensionName, new MobileDetectExtension());
		};
	}
}
