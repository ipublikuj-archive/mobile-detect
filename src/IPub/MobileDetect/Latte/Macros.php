<?php
/**
 * Macros.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:MobileDetect!
 * @subpackage	Latte
 * @since		5.0
 *
 * @date		22.04.14
 */

namespace IPub\MobileDetect\Latte;

use Nette;

use Latte;
use Latte\Compiler;
use Latte\MacroNode;
use Latte\PhpWriter;
use Latte\Macros\MacroSet;

use IPub;

class Macros extends MacroSet
{
	/**
	 * Register latte macros
	 */
	public static function install(Compiler $compiler)
	{
		$me = new static($compiler);

		/**
		 * {isMobile /}, {isNotMobile /}
		 */
		$me->addMacro('isMobile', array($me, 'macroIsMobile'), '}');
		$me->addMacro('isNotMobile', array($me, 'macroIsNotMobile'), '}');

		/**
		 * {isPhone /}
		 */
		$me->addMacro('isPhone', array($me, 'macroIsPhone'), '}');

		/**
		 * {isTablet /}, {isNotTablet /}
		 */
		$me->addMacro('isTablet', array($me, 'macroIsTablet'), '}');
		$me->addMacro('isNotTablet', array($me, 'macroIsNotTablet'), '}');

		/**
		 * {isMobileDevice 'device_name'}
		 */
		$me->addMacro('isMobileDevice', array($me, 'macroIsDevice'), '}');

		/**
		 * {isMobileOs 'device_name'}
		 */
		$me->addMacro('isMobileOs', array($me, 'macroIsOS'), '}');

		/**
		 * {isFullView /}, {isMobileView /}, {isTabletView /}, {isNotMobileView /}
		 */
		$me->addMacro('isFullView', array($me, 'macroIsFullView'), '}');
		$me->addMacro('isMobileView', array($me, 'macroIsMobileView'), '}');
		$me->addMacro('isTabletView', array($me, 'macroIsTabletView'), '}');
		$me->addMacro('isNotMobileView', array($me, 'macroIsNotMobileView'), '}');

		return $me;
	}

	/**
	 * {isMobile /}
	 *
	 * @param MacroNode $node
	 * @param PhpWriter $writer
	 *
	 * @return string
	 */
	public function macroIsMobile(MacroNode $node, PhpWriter $writer)
	{
		return $writer->write('if ($template->getMobileDetectService()->isMobile()) {');
	}

	/**
	 * {isNotMobile /}
	 *
	 * @param MacroNode $node
	 * @param PhpWriter $writer
	 *
	 * @return string
	 */
	public function macroIsNotMobile(MacroNode $node, PhpWriter $writer)
	{
		return $writer->write('if (!$template->getMobileDetectService()->isMobile()) {');
	}

	/**
	 * {isPhone /}
	 *
	 * @param MacroNode $node
	 * @param PhpWriter $writer
	 *
	 * @return string
	 */
	public function macroIsPhone(MacroNode $node, PhpWriter $writer)
	{
		return $writer->write('if ($template->getMobileDetectService()->isMobile() && !$template->getMobileDetectService()->isTablet()) {');
	}

	/**
	 * {isTablet /}
	 *
	 * @param MacroNode $node
	 * @param PhpWriter $writer
	 *
	 * @return string
	 */
	public function macroIsTablet(MacroNode $node, PhpWriter $writer)
	{
		return $writer->write('if ($template->getMobileDetectService()->isTablet()) {');
	}

	/**
	 * {isNotTablet /}
	 *
	 * @param MacroNode $node
	 * @param PhpWriter $writer
	 *
	 * @return string
	 */
	public function macroIsNotTablet(MacroNode $node, PhpWriter $writer)
	{
		return $writer->write('if (!$template->getMobileDetectService()->isTablet()) {');
	}

	/**
	 * @param MacroNode $node
	 * @param PhpWriter $writer
	 *
	 * @return string
	 *
	 * @throws \Nette\Latte\CompileException
	 */
	public function macroIsDevice(MacroNode $node, PhpWriter $writer)
	{
		$arguments = self::prepareMacroArguments($node->args);

		if ($arguments["device"] === NULL) {
			throw new Nette\Latte\CompileException("Please provide device name.");
		}

		// Create magic method name
		$magicMethodName = 'is' . ucfirst(strtolower((string) $arguments["device"]));

		return $writer->write('if ($template->getMobileDetectService()->'. $magicMethodName.'()) {');
	}

	/**
	 * @param MacroNode $node
	 * @param PhpWriter $writer
	 *
	 * @return string
	 *
	 * @throws \Nette\Latte\CompileException
	 */
	public function macroIsOS(MacroNode $node, PhpWriter $writer)
	{
		$arguments = self::prepareMacroArguments($node->args);

		if ($arguments["os"] === NULL) {
			throw new Nette\Latte\CompileException("Please provide OS name.");
		}

		// Create magic method name
		$magicMethodName = 'is' . ucfirst(strtolower((string) $arguments["os"]));

		return $writer->write('if ($template->getMobileDetectService()->'. $magicMethodName.'()) {');
	}

	/**
	 * {isFullView /}
	 *
	 * @param MacroNode $node
	 * @param PhpWriter $writer
	 *
	 * @return string
	 */
	public function macroIsFullView(MacroNode $node, PhpWriter $writer)
	{
		return $writer->write('if ($template->getDeviceViewService()->isFullView()) {');
	}

	/**
	 * {isFullView /}
	 *
	 * @param MacroNode $node
	 * @param PhpWriter $writer
	 *
	 * @return string
	 */
	public function macroIsMobileView(MacroNode $node, PhpWriter $writer)
	{
		return $writer->write('if ($template->getDeviceViewService()->isMobileView()) {');
	}

	/**
	 * {isFullView /}
	 *
	 * @param MacroNode $node
	 * @param PhpWriter $writer
	 *
	 * @return string
	 */
	public function macroIsTabletView(MacroNode $node, PhpWriter $writer)
	{
		return $writer->write('if ($template->getDeviceViewService()->isTabletView()) {');
	}

	/**
	 * {isFullView /}
	 *
	 * @param MacroNode $node
	 * @param PhpWriter $writer
	 *
	 * @return string
	 */
	public function macroIsNotMobileView(MacroNode $node, PhpWriter $writer)
	{
		return $writer->write('if ($template->getDeviceViewService()->isNotMobileView()) {');
	}

	/**
	 * @param string $macro
	 *
	 * @return array
	 */
	public static function prepareMacroArguments($macro)
	{
		$arguments = array_map(function ($value) {
			return trim($value);
		}, explode(",", $macro));

		$device	= $os = $arguments[0];

		return array(
			"device"	=> $device,
			"os"		=> $os,
		);
	}
}