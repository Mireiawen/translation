# Translation
Helper class to set up the text translation domain and other settings for gettext translations

* Class name: `Translation`
* Namespace: `Mireiawen\Translation`

## Requirements
* Intl extension
* PHP 7

## Installation
You can clone or download the code from the [GitHub repository](https://github.com/Mireiawen/translation) or you can use composer: `composer require mireiawen/translation`

## Methods

### __construct
    Translation::__construct(string $path, string $default_language, string $domain, string $codeset = 'UTF-8')
    
Set up the translation and try to detect the user settings

#### Arguments
* **string** `$path` - The filesystem path to the translations
* **string** `$default_language` - The default language to use, if none can be determined
* **string** `$domain` - The text domain to bind to
* **string** `$codeset` - The character set to use with the translation file

#### Exceptions thrown
##### \Exception
* In case the extension is missing
* In case the translation folder does not exist
* In case the language cannot be determined

### GetLanguage
    Translation::GetLanguage()

Get the user's currently selected language

#### Return value
* **string** - The current language code

### TranslateTo
    Translation::TranslateTo(string $language, string $domain, string $codeset = 'UTF-8')
    
Set up the environment to load the correct translation file for the chosen language

#### Arguments
* **string** `$language` - The language to load
* **string** `$domain` - The text domain to bind to
* **string** `$codeset` - The character set to use with the translation file
 
#### Exceptions thrown
##### \Exception
* In case the language is empty
