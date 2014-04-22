<?php
/**
 * MobileDetectExtension.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:MobileDetect!
 * @subpackage	DI
 * @since		5.0
 *
 * @date		21.04.14
 */

namespace IPub\MobileDetect\DI;

use Nette;
use Nette\DI\Compiler;
use Nette\DI\Configurator;
use Nette\PhpGenerator as Code;

use IPub;

if (!class_exists('Nette\DI\CompilerExtension')) {
	class_alias('Nette\Config\CompilerExtension', 'Nette\DI\CompilerExtension');
	class_alias('Nette\Config\Compiler', 'Nette\DI\Compiler');
	class_alias('Nette\Config\Helpers', 'Nette\DI\Config\Helpers');
}

if (isset(Nette\Loaders\NetteLoader::getInstance()->renamed['Nette\Configurator']) || !class_exists('Nette\Configurator')) {
	unset(Nette\Loaders\NetteLoader::getInstance()->renamed['Nette\Configurator']); // fuck you
	class_alias('Nette\Config\Configurator', 'Nette\Configurator');
}

class MobileDetectExtension extends Nette\DI\CompilerExtension
{
	/**
	 * @var array
	 */
	protected $defaults = array(
		'redirect'	=> array(
			'mobile'	=> array(
				'isEnabled'		=> FALSE,
				'host'			=> NULL,
				'statusCode'	=> 301,
				'action'		=> 'redirect',	// redirect/noRedirect/redirectWithoutPath
			),
			'tablet'	=> array(
				'isEnabled'		=> FALSE,
				'host'			=> NULL,
				'statusCode'	=> 301,
				'action'		=> 'redirect',	// redirect/noRedirect/redirectWithoutPath
			),
			'detectTabletAsMobile'	=> FALSE,
		),
		'switchDeviceView'	=> array(
			'saveRefererPath'	=> TRUE
		)
	);


	public function loadConfiguration()
	{
		$config = $this->getConfig($this->defaults);
		$builder = $this->getContainerBuilder();

		// Install extension latte macros
		$install = 'IPub\MobileDetect\Latte\Macros::install';
		$builder->getDefinition('nette.latte')
			->addSetup($install . '(?->compiler)', array('@self'));

		$builder->addDefinition($this->prefix('deviceDetection'))
			->setClass('IPub\MobileDetect\MobileDetect');
	}

	/**
	 * @param \Nette\Configurator $config
	 * @param string $extensionName
	 */
	public static function register(Nette\Configurator $config, $extensionName = 'deviceDetectionExtension')
	{
		$config->onCompile[] = function (Configurator $config, Compiler $compiler) use ($extensionName) {
			$compiler->addExtension($extensionName, new MobileDetectExtension());
		};
	}
}