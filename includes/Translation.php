<?php
declare(strict_types = 1);

namespace Mireiawen\Translation;

/**
 * Set up the user language and load the correct gettext translation files
 * based on the given language
 *
 * @package Mireiawen\Translation
 */
class Translation
{
	/**
	 * The language to use
	 *
	 * @var string
	 */
	protected $language = '';
	
	/**
	 * The path to the translation files
	 *
	 * @var string
	 */
	protected $path = '';
	
	/**
	 * Set up the translation and try to detect the user settings
	 *
	 * @param string $path
	 *    The path to the translations
	 *
	 * @param string $default_language
	 *    The default language to use, if none can be determined
	 *
	 * @param string $domain
	 *    The text domain to bind to
	 *
	 * @param string $codeset
	 *    The character set to use with the translation file
	 *
	 * @throws \Exception
	 *    In case the extension is missing
	 *    In case the translation folder does not exist
	 *    In case the language cannot be determined
	 */
	public function __construct(string $path, string $default_language, string $domain, string $codeset = 'UTF-8')
	{
		// Check for intl extension
		if (!\extension_loaded('intl'))
		{
			throw new \Exception(\sprintf('Required extension "%s" is missing', 'intl'));
		}
		
		// Validate the translation path
		$translations = \realpath($path);
		if (!\is_dir($translations) || (!\is_readable($translations)))
		{
			throw new \Exception(\sprintf(\_('The translation path %s does not exist or is not readable'), $path));
		}
		$this->path = $translations;
		
		// Load from user input
		$language = $this->GetRequestLanguage();
		
		if (empty($language))
		{
			// Try the session
			$language = $this->GetSessionLanguage();
		}
		
		if (empty($language))
		{
			// Try the accept-language -header
			$language = $this->GetAcceptLanguage();
		}
		
		if (empty($language))
		{
			// Default language
			$language = $default_language;
		}
		
		// Set up the translation
		$this->TranslateTo($language, $domain, $codeset);
	}
	
	/**
	 * Get the user's currently selected language
	 *
	 * @return string
	 *    Current language code
	 */
	public function GetLanguage() : string
	{
		return $this->language;
	}
	
	/**
	 * Set up the environment to load the correct translation file for the
	 * chosen language
	 *
	 * @param string $language
	 *    The language to load
	 *
	 * @param string $domain
	 *    The text domain to bind to
	 *
	 * @param string $codeset
	 *    The character set to use with the translation file
	 *
	 * @throws \Exception
	 *    On invalid language settings
	 */
	public function TranslateTo(string $language, string $domain, string $codeset = 'UTF-8')
	{
		if (empty($language))
		{
			throw new \Exception(\sprintf(\_('Unable to set the language: %s'), \_('Empty language setting')));
		}
		
		// Do basic validation for the language
		$language = \Locale::canonicalize($language);
		
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
		{
			// Windows doesn't know of LC_MESSAGES, use LC_ALL on it
			setlocale(LC_ALL, $language);
		}
		else
		{
			// Just translate the messages
			setlocale(LC_MESSAGES, $language);
		}
		
		// Set the path for domain
		$this->path = bindtextdomain($domain, $this->path);
		
		// Set the default text domain
		textdomain($domain);
		
		// Set the character encoding for the messages
		bind_textdomain_codeset($domain, $codeset);
		
		// Set it up our variable
		$this->language = $language;
		
		// Set it up in session
		if (!empty(\session_id()))
		{
			$_SESSION['language'] = $language;
		}
	}
	
	/**
	 * Try to get the language from user request, such as $_GET or $_POST
	 *
	 * @return string
	 *    The language string retrieved
	 */
	protected function GetRequestLanguage() : string
	{
		if (isset($_REQUEST['language']))
		{
			return \trim
			(
				filter_var
				(
					$_REQUEST['language'],
					FILTER_SANITIZE_STRING,
					FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH
				)
			);
		}
		
		return '';
	}
	
	/**
	 * Try to get the language from the session
	 *
	 * @return string
	 *    The language string retrieved
	 */
	protected function GetSessionLanguage() : string
	{
		if (isset($_SESSION['language']))
		{
			return \trim($_SESSION['language']);
		}
		
		return '';
	}
	
	/**
	 * Try to get the language from the Accept-Language -header
	 *
	 * @return string
	 *    The language string retrieved
	 */
	protected function GetAcceptLanguage() : string
	{
		if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
		{
			return \trim(\Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']));
		}
		
		return '';
	}
}
