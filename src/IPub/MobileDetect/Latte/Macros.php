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
use Nette\Latte\Compiler,
	Nette\Latte\MacroNode,
	Nette\Latte\PhpWriter;

use IPub\MobileDetect\MobileDetect;

class Macros extends Nette\Latte\Macros\MacroSet
{
	/**
	 * @var bool
	 */
	private $isUsed = FALSE;

	/**
	 * @param Compiler $compiler
	 *
	 * @return \Nette\Latte\Macros\MacroSet
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
		 * {isTablet}, {isDevice 'device_name'}
		 */
		$me->addMacro('isTablet', array($me, 'macroIsTablet'), '}');
		$me->addMacro('isNotTablet', array($me, 'macroIsNotTablet'), '}');

		/**
		 * {isDevice 'device_name'}, {isNotDevice 'device_name'}
		 */
		$me->addMacro('isMobileDevice', array($me, 'macroIsDevice'), '}');

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
		$this->isUsed = TRUE;

		return $writer->write('if ($_mobileDetect->isMobile()) {');
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
		$this->isUsed = TRUE;

		return $writer->write('if (!$_mobileDetect->isMobile()) {');
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
		$this->isUsed = TRUE;

		return $writer->write('if ($_mobileDetect->isTablet()) {');
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
		$this->isUsed = TRUE;

		return $writer->write('if (!$_mobileDetect->isTablet()) {');
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
		$this->isUsed = TRUE;

		$arguments = self::prepareMacroArguments($node->args);

		if ($arguments["device"] === NULL) {
			throw new Nette\Latte\CompileException("Please provide device name.");
		}

		// Create magic method name
		$magicMethodName = 'is' . ucfirst(strtolower((string) $arguments["device"]));

		return $writer->write('if ($_mobileDetect->'. $magicMethodName.'()) {');
	}

	/**
	 *
	 */
	public function initialize()
	{
		$this->isUsed = FALSE;
	}

	/**
	 * Finishes template parsing.
	 *
	 * @return array(prolog, epilog)
	 */
	public function finalize()
	{
		if (!$this->isUsed) {
			return array();
		}

		return array(
			get_called_class() . '::validateTemplateParams($template);',
			NULL
		);
	}

	/**
	 * @param \Nette\Templating\Template $template
	 *
	 * @throws \Nette\InvalidStateException
	 */
	public static function validateTemplateParams(Nette\Templating\Template $template)
	{
		$params = $template->getParameters();

		if (!isset($params['_mobileDetect']) || !$params['_mobileDetect'] instanceof MobileDetect) {
			$where = isset($params['control']) ?
				" of component " . get_class($params['control']) . '(' . $params['control']->getName() . ')'
				: NULL;

			throw new Nette\InvalidStateException(
				'Please provide an instanceof IPub\\MobileDetect\\MobileDetect ' .
				'as a parameter $_mobileDetect to template' . $where
			);
		}
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

		$device	= $arguments[0];

		return array(
			"device"	=> $device,
		);
	}
}