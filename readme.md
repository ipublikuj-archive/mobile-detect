# MobileDetect

[![Build Status](https://img.shields.io/travis/iPublikuj/mobile-detect.svg?style=flat-square)](https://travis-ci.org/iPublikuj/mobile-detect)
[![Latest Stable Version](https://img.shields.io/packagist/v/ipub/mobile-detect.svg?style=flat-square)](https://packagist.org/packages/ipub/mobile-detect)
[![Composer Downloads](https://img.shields.io/packagist/dt/ipub/mobile-detect.svg?style=flat-square)](https://packagist.org/packages/ipub/mobile-detect)

Extension for detecting mobile devices, managing mobile view types, redirect to mobile version for [Nette Framework](http://nette.org/)

## Introduction

This extension use [Mobile_Detect](https://github.com/serbanghita/Mobile-Detect) class and provides the following features:

* Detect the various mobile devices by name, OS, browser User-Agent
* Manages site views for the variuos mobile devices (`mobile`, `tablet`, `full`)
* Redirects to mobile and tablet sites

## Installation

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

Package contains trait, which you will have to use in class, where you want to use mobile detector. This works only for PHP 5.4+, for older version you can simply copy trait content and paste it into class where you want to use it.

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

## Configuration

You can change default behaviour of your redirects with action parameter:

- `redirect`: redirects to appropriate host with your current path
- `noRedirect`: no redirection (default behaviour)
- `redirectWithoutPath`: redirects to appropriate host index page

```php
	# Mobile detector
	mobileDetect:
		redirect:
			mobile:
				isEnabled: true				# default false
				host: http://m.site.com		# with scheme (http|https), default null, url validate
				statusCode: 301				# default 302
				action: redirect			# redirect, noRedirect, redirectWithoutPath
			tablet:
				isEnabled: false			# default false
				host: http://t.site.com		# with scheme (http|https), default null, url validate
				statusCode: 301				# default 302
				action: redirect			# redirect, noRedirect, redirectWithoutPath
			detectTabletAsMobile: true		# default false
		switchDeviceView:
			saveRefererPath: false			# default true
											# true	=> redirectUrl = http://site.com/current/path
											# false	=> redirectUrl = http://site.com
```

## Usage in PHP files

### Switch device view

For switch device view, use `device_view` GET parameter:

````
http://site.com?device_view={full/mobile/tablet}
````

### How to use in presenter etc.

In presenters or other services where you import mobile detector you could create calls like this

```php
class SomePresenter extends Nette\Application\UI\Presenter
{
	/**
	 * @var \IPub\MobileDetect\MobileDetect
	 */
	protected $mobileDetect

	/**
	 * Some action with mobile detection
	 */
	public function someAction()
	{
		if ($this->mobileDetect->isMobile()) {
			//...do whatever
		}
	}
}
```

### Check type device

```php
$mobileDetect->isMobile();
$mobileDetect->isTablet();
$mobileDetect->isPhone();
```

### Check phone

is[iPhone|HTC|Nexus|Dell|Motorola|Samsung|Sony|Asus|Palm|Vertu|GenericPhone]

```php
$mobileDetect->isIphone();
$mobileDetect->isHTC();
// etc.
```

### Check tablet

is[BlackBerryTablet|iPad|Kindle|SamsungTablet|HTCtablet|MotorolaTablet|AsusTablet|NookTablet|AcerTablet| YarvikTablet|GenericTablet]

```php
$mobileDetect->isIpad();
$mobileDetect->isMotorolaTablet();
// etc.
```

### Check mobile OS

is[AndroidOS|BlackBerryOS|PalmOS|SymbianOS|WindowsMobileOS|iOS|badaOS]

```php
$mobileDetect->isAndroidOS();
$mobileDetect->isIOS();
// etc.
```

### Check mobile browser User-Agent

is[Chrome|Dolfin|Opera|Skyfire|IE|Firefox|Bolt|TeaShark|Blazer|Safari|Midori|GenericBrowser]

```php
$mobileDetect->isChrome();
$mobileDetect->isSafari();
// etc.
```

## Using in Latte

### Check device type

```html
{isMobile}
	<span>This content will be only on mobile devices....</span>
{/isMobile}

{isTablet}
	<span>This content will be only on tablet devices....</span>
{/isTablet}

{isPhone}
	<span>This content will be only on phone devices....</span>
{/isPhone}
```

Available Latte macros:

```html
{isMobile}....{/isMobile}
{isNotMobile}....{/isNotMobile}

{isTablet}....{/isTablet}
{isNotTablet}....{/isNotTablet}

{isPhone}....{/isPhone}
{isNotPhone}....{/isNotPhone}
```

### Check device type by provided name

```html
{isMobileDevice 'iPhone'}
	<span>This content will be only on Apple iPhone devices....</span>
{/isMobileDevice}

<div n:isMobileDevice="iPhone">
	<span>This content will be only on Apple iPhone devices....</span>
</div>
```

### Check device OS by provided name

```html
{isMobileOs 'iOS'}
	<span>This content will be only on mobile devices with iOS operating system....</span>
{/isMobileOs}

<div n:isMobileOs="iOS">
	<span>This content will be only on mobile devices with iOS operating system....</span>
</div>
```

### Check view type set by helper

With view type detector you could change your default layout in templates.

```html
{isMobileView}
	{layout '../Path/To/Your/Mobile/Device/@layout.latte'}
{/isMobileView}

{isTabletView}
	{layout '../Path/To/Your/Tablet/Device/@layout.latte'}
{/isTabletView}

{isPhoneView}
	{layout '../Path/To/Your/Phone/Device/@layout.latte'}
{/isPhoneView}

{isFullView}
	{layout '../Path/To/Your/Full/View/@layout.latte'}
{/isFullView}

{isNotMobileView}
	{layout '../Path/To/Your/Not/Mobile/Device/@layout.latte'}
{/isNotMobileView}
```
