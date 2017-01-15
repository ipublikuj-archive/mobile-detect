<?php
/**
 * Macros.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:MobileDetect!
 * @subpackage     Latte
 * @since          1.0.0
 *
 * @date           22.04.14
 */

declare(strict_types = 1);

namespace IPub\MobileDetect\Latte;

use Nette;

use Latte;
use Latte\Compiler;
use Latte\MacroNode;
use Latte\PhpWriter;
use Latte\Macros\MacroSet;

use IPub;
use IPub\MobileDetect\Exceptions;

/**
 * Mobile detect latte macros definition
 *
 * @package        iPublikuj:MobileDetect!
 * @subpackage     Latte
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
final class Macros extends MacroSet
{
	/**
	 * Register latte macros
	 *
	 * @param Compiler $compiler
	 *
	 * @return static
	 */
	public static function install(Compiler $compiler)
	{
		$me = new static($compiler);

		/**
		 * {isMobile /}, {isNotMobile /}
		 */
		$me->addMacro('isMobile', [$me, 'macroIsMobile'], '}');
		$me->addMacro('isNotMobile', [$me, 'macroIsNotMobile'], '}');

		/**
		 * {isPhone /}, {isNotPhone /}
		 */
		$me->addMacro('isPhone', [$me, 'macroIsPhone'], '}');
		$me->addMacro('isNotPhone', [$me, 'macroIsNotPhone'], '}');

		/**
		 * {isTablet /}, {isNotTablet /}
		 */
		$me->addMacro('isTablet', [$me, 'macroIsTablet'], '}');
		$me->addMacro('isNotTablet', [$me, 'macroIsNotTablet'], '}');

		/**
		 * {isMobileDevice 'device_name'}
		 */
		$me->addMacro('isMobileDevice', [$me, 'macroIsDevice'], '}');

		/**
		 * {isMobileOs 'device_name'}
		 */
		$me->addMacro('isMobileOs', [$me, 'macroIsOS'], '}');

		/**
		 * {isFullView /}, {isMobileView /}, {isTabletView /}, {isNotMobileView /}
		 */
		$me->addMacro('isFullView', [$me, 'macroIsFullView'], '}');
		$me->addMacro('isMobileView', [$me, 'macroIsMobileView'], '}');
		$me->addMacro('isPhoneView', [$me, 'macroIsPhoneView'], '}');
		$me->addMacro('isTabletView', [$me, 'macroIsTabletView'], '}');
		$me->addMacro('isNotMobileView', [$me, 'macroIsNotMobileView'], '}');

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
	public function macroIsMobile(MacroNode $node, PhpWriter $writer) : string
	{
		return $writer->write('
			$_resultMD = %escape(property_exists($this, "filters")) ? %escape(call_user_func($this->filters->isMobile)) : $template->getMobileDetectService()->isMobile() && !$template->getMobileDetectService()->isTablet();
			if ($_resultMD) {
			');
	}

	/**
	 * {isNotMobile /}
	 *
	 * @param MacroNode $node
	 * @param PhpWriter $writer
	 *
	 * @return string
	 */
	public function macroIsNotMobile(MacroNode $node, PhpWriter $writer) : string
	{
		return $writer->write('
			$_resultMD = %escape(property_exists($this, "filters")) ? %escape(!call_user_func($this->filters->isMobile)) : ;
			if ($_resultMD) {
			');
	}

	/**
	 * {isPhone /}
	 *
	 * @param MacroNode $node
	 * @param PhpWriter $writer
	 *
	 * @return string
	 */
	public function macroIsPhone(MacroNode $node, PhpWriter $writer) : string
	{
		return $writer->write('if (%escape(call_user_func($this->filters->isMobile) && !call_user_func($this->filters->isTablet))) {');
	}

	/**
	 * {isNotPhone /}
	 *
	 * @param MacroNode $node
	 * @param PhpWriter $writer
	 *
	 * @return string
	 */
	public function macroIsNotPhone(MacroNode $node, PhpWriter $writer) : string
	{
		return $writer->write('if (%escape((call_user_func($this->filters->isMobile) && call_user_func($this->filters->isTablet)) || !call_user_func($this->filters->isMobile))) {');
	}

	/**
	 * {isTablet /}
	 *
	 * @param MacroNode $node
	 * @param PhpWriter $writer
	 *
	 * @return string
	 */
	public function macroIsTablet(MacroNode $node, PhpWriter $writer) : string
	{
		return $writer->write('if (%escape(call_user_func($this->filters->isTablet))) {');
	}

	/**
	 * {isNotTablet /}
	 *
	 * @param MacroNode $node
	 * @param PhpWriter $writer
	 *
	 * @return string
	 */
	public function macroIsNotTablet(MacroNode $node, PhpWriter $writer) : string
	{
		return $writer->write('if (%escape(!call_user_func($this->filters->isTablet))) {');
	}

	/**
	 * @param MacroNode $node
	 * @param PhpWriter $writer
	 *
	 * @return string
	 *
	 * @throws Exceptions\CompileException
	 */
	public function macroIsDevice(MacroNode $node, PhpWriter $writer) : string
	{
		$arguments = self::prepareMacroArguments($node->args);

		if ($arguments['device'] === NULL) {
			throw new Exceptions\CompileException('Please provide device name.');
		}

		return $writer->write('if (%escape(call_user_func($this->filters->isDevice, "' . $arguments['device'] . '"))) {');
	}

	/**
	 * @param MacroNode $node
	 * @param PhpWriter $writer
	 *
	 * @return string
	 *
	 * @throws Exceptions\CompileException
	 */
	public function macroIsOS(MacroNode $node, PhpWriter $writer) : string
	{
		$arguments = self::prepareMacroArguments($node->args);

		if ($arguments['os'] === NULL) {
			throw new Exceptions\CompileException('Please provide OS name.');
		}

		return $writer->write('if (%escape(call_user_func($this->filters->isOs, "' . $arguments['os'] . '"))) {');
	}

	/**
	 * {isFullView /}
	 *
	 * @param MacroNode $node
	 * @param PhpWriter $writer
	 *
	 * @return string
	 */
	public function macroIsFullView(MacroNode $node, PhpWriter $writer) : string
	{
		return $writer->write('if (%escape(!call_user_func($this->filters->isFullView))) {');
	}

	/**
	 * {isMobileView /}
	 *
	 * @param MacroNode $node
	 * @param PhpWriter $writer
	 *
	 * @return string
	 */
	public function macroIsMobileView(MacroNode $node, PhpWriter $writer) : string
	{
		return $writer->write('if (%escape(!call_user_func($this->filters->isMobileView))) {');
	}

	/**
	 * {isPhoneView /}
	 *
	 * @param MacroNode $node
	 * @param PhpWriter $writer
	 *
	 * @return string
	 */
	public function macroIsPhoneView(MacroNode $node, PhpWriter $writer) : string
	{
		return $writer->write('if (%escape(!call_user_func($this->filters->isPhoneView))) {');
	}

	/**
	 * {isTabletView /}
	 *
	 * @param MacroNode $node
	 * @param PhpWriter $writer
	 *
	 * @return string
	 */
	public function macroIsTabletView(MacroNode $node, PhpWriter $writer) : string
	{
		return $writer->write('if (%escape(!call_user_func($this->filters->isTabletView))) {');
	}

	/**
	 * {isNotMobileView /}
	 *
	 * @param MacroNode $node
	 * @param PhpWriter $writer
	 *
	 * @return string
	 */
	public function macroIsNotMobileView(MacroNode $node, PhpWriter $writer) : string
	{
		return $writer->write('if (%escape(!call_user_func($this->filters->isNotMobileView))) {');
	}

	/**
	 * @param string $macro
	 *
	 * @return array
	 */
	public static function prepareMacroArguments($macro) : array
	{
		$arguments = array_map(function ($value) {
			return trim($value);
		}, explode(',', $macro));

		$device = $os = $arguments[0];

		return [
			'device' => $device,
			'os'     => $os,
		];
	}
}
