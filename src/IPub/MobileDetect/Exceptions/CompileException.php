<?php
/**
 * CompileException.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:MobileDetect!
 * @subpackage     Exceptions
 * @since          2.0.0
 *
 * @date           13.01.17
 */

declare(strict_types = 1);

namespace IPub\MobileDetect\Exceptions;

use Latte;

class CompileException extends Latte\CompileException implements IException
{
}
