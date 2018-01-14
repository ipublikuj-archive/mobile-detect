<?php
/**
 * CookieSettings.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:MobileDetect!
 * @subpackage     Helpers
 * @since          1.0.0
 *
 * @date           23.04.14
 */

declare(strict_types = 1);

namespace IPub\MobileDetect\Helpers;

use Nette;

use IPub;
use IPub\MobileDetect\Exceptions;

/**
 * Cookies creator helper
 *
 * @package        iPublikuj:MobileDetect!
 * @subpackage     Helpers
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
final class CookieSettings
{
	/**
	 * Implement nette smart magic
	 */
	use Nette\SmartObject;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string|NULL
	 */
	private $domain;

	/**
	 * @var string
	 */
	private $expire;

	/**
	 * @var string
	 */
	private $path;

	/**
	 * @var bool
	 */
	private $secure;

	/**
	 * @var bool
	 */
	private $httpOnly;

	/**
	 * @param string $name          The name of the cookie
	 * @param string $expireAfter   The time the cookie expires
	 * @param string $path          The path on the server in which the cookie will be available on
	 * @param string $domain        The domain that the cookie is available to
	 * @param bool $secure          Whether the cookie should only be transmitted over a secure HTTPS connection from the client
	 * @param bool $httpOnly        Whether the cookie will be made accessible only through the HTTP protocol
	 *
	 * @throws Exceptions\InvalidArgumentException
	 */
	public function __construct(string $name, string $domain = NULL, string $expireAfter = NULL, string $path = '/',  bool $secure = FALSE, bool $httpOnly = TRUE)
	{
		// from PHP source code
		if (preg_match("/[=,; \t\r\n\013\014]/", $name)) {
			throw new Exceptions\InvalidArgumentException(sprintf('The cookie name "%s" contains invalid characters.', $name));
		}

		if (empty($name)) {
			throw new Exceptions\InvalidArgumentException('The cookie name cannot be empty.');
		}

		$expire = new \DateTime;

		if ($expireAfter !== NULL) {
			$expire->modify($expireAfter);
		}

		$this->name = $name;
		$this->domain = $domain;
		$this->expire = (int) $expire->format('U');
		$this->path = empty($path) ? '/' : $path;
		$this->secure = $secure;
		$this->httpOnly = $httpOnly;
	}

	/**
	 * Gets the name of the cookie
	 *
	 * @return string
	 */
	public function getName() : string
	{
		return $this->name;
	}

	/**
	 * Gets the domain that the cookie is available to
	 *
	 * @return string|NULL
	 */
	public function getDomain()
	{
		return $this->domain;
	}

	/**
	 * Gets the time the cookie expires
	 *
	 * @return int
	 */
	public function getExpiresTime() : int
	{
		return $this->expire;
	}

	/**
	 * Gets the path on the server in which the cookie will be available on
	 *
	 * @return string
	 */
	public function getPath() : string
	{
		return $this->path;
	}

	/**
	 * Checks whether the cookie should only be transmitted over a secure HTTPS connection from the client
	 *
	 * @return bool
	 */
	public function isSecure() : bool
	{
		return $this->secure;
	}

	/**
	 * Checks whether the cookie will be made accessible only through the HTTP protocol
	 *
	 * @return bool
	 */
	public function isHttpOnly() : bool
	{
		return $this->httpOnly;
	}
}
