# MobileDetect

Detect mobile devices, manage mobile view and redirect to the mobile and tablet version for [Nette Framework](http://nette.org/)

## Introduction

This Bundle use [Mobile_Detect](https://github.com/serbanghita/Mobile-Detect) class and provides the following features:

* Detect the various mobile devices by name, OS, browser User-Agent
* Manages site views for the variuos mobile devices (`mobile`, `tablet`, `full`)
* Redirects to mobile and tablet sites

## Instalation

The best way to install ipub/mobile-detect is using  [Composer](http://getcomposer.org/):


```json
{
	"require": {
		"ipub/mobile-detect": "dev-master"
	}
}
```

After that you have to register extension in config.neon.

```neon
extensions:
	mobileDetect: IPub\MobileDetect\DI\MobileDetectExtension
```

Package contains trait, which you will have to use in class, where you want to use mobile detector. This works only for PHP 5.3+, for older version you can simply copy trait content and paste it into class where you want to use it.

```php
<?php

class BasePresenter extends Nette\Application\UI\Presenter
{

	use IPub\MobileDetect\TMobileDetect;

}
```

You have to add few lines in base presenter or base control in section createTemplate

```php
<?php

class BasePresenter extends Nette\Application\UI\Presenter
{
	protected function createTemplate($class = NULL)
	{
		// Init template
		$template = parent::createTemplate($class);

		// Add mobile detect and its helper to template
		$template->_mobileDetect	= $this->mobileDetect;
		$template->_deviceView		= $this->deviceView;

		return $template;
	}
}
```

## Usage

### PHP examles

#### Switch device view

For switch device view, use `device_view` GET parameter:

````
http://site.com?device_view={full/mobile/tablet}
````

#### How to use in presenter etc.

In presenters or other services where you import mobile detector you could create calls like this

```php
class SomePresenter extends Nette\Application\UI\Presenter
{
	/**
	 * @var \IPub\MobileDetect\MobileDetect
	 */
	protected $mobileDetector;

	/**
	 * Some action with mobile detection
	 */
	public function someAction()
	{
		if ($this->mobileDetector->isMobile()) {
			//...do whatever
		}
	}
}
```

#### Check type device

```php
$mobileDetector->isMobile();
$mobileDetector->isTablet();
```

#### Check phone

is[iPhone|HTC|Nexus|Dell|Motorola|Samsung|Sony|Asus|Palm|Vertu|GenericPhone]

```php
$mobileDetector->isIphone();
$mobileDetector->isHTC();
// etc.
```

#### Check tablet

is[BlackBerryTablet|iPad|Kindle|SamsungTablet|HTCtablet|MotorolaTablet|AsusTablet|NookTablet|AcerTablet| YarvikTablet|GenericTablet]

```php
$mobileDetector->isIpad();
$mobileDetector->isMotorolaTablet();
// etc.
```

#### Check mobile OS

is[AndroidOS|BlackBerryOS|PalmOS|SymbianOS|WindowsMobileOS|iOS|badaOS]

```php
$mobileDetector->isAndroidOS();
$mobileDetector->isIOS();
// etc.
```

#### Check mobile browser User-Agent

is[Chrome|Dolfin|Opera|Skyfire|IE|Firefox|Bolt|TeaShark|Blazer|Safari|Midori|GenericBrowser]

```php
$mobileDetector->isChrome();
$mobileDetector->isSafari();
// etc.
```

### Using in Latte

#### Check device type

```html
{isMobile}
	<span>This content will be only on mobile devices....</span>
{/isMobile}

{isTablet}
	<span>This content will be only on tablet devices....</span>
{/isTablet}
```

Available Latte macros:

```html
{isMobile}....{/isMobile}
{isNotMobile}....{/isNotMobile}

{isTablet}....{/isTablet}
{isNotTablet}....{/isNotTablet}
```

#### Check device type by provided name

```html
{isMobileDevice 'iPhone'}
	<span>This content will be only on Apple iPhone devices....</span>
{/isMobileDevice}

<div n:isMobileDevice="iPhone">
	<span>This content will be only on Apple iPhone devices....</span>
</div>
```

#### Check device OS by provided name

```html
{isMobileOs 'iOS'}
	<span>This content will be only on mobile devices with iOS operating system....</span>
{/isMobileOs}

<div n:isMobileOs="iOS">
	<span>This content will be only on mobile devices with iOS operating system....</span>
</div>
```

#### Check view type set by helper

With view type detector you could change your default layout in templates.

```html
{isMobileView}
	{layout '../Path/To/Your/Mobile/Device/@layout.latte'}
{/isMobileView}

{isTabletView}
	{layout '../Path/To/Your/Tablet/Device/@layout.latte'}
{/isTabletView}

{isFullView}
	{layout '../Path/To/Your/Full/View/@layout.latte'}
{/isFullView}

{isNotMobileView}
	{layout '../Path/To/Your/Not/Mobile/Device/@layout.latte'}
{/isNotMobileView}
```

