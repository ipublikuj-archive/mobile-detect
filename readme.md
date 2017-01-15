# MobileDetect

[![Build Status](https://img.shields.io/travis/iPublikuj/mobile-detect.svg?style=flat-square)](https://travis-ci.org/iPublikuj/mobile-detect)
[![Scrutinizer Code Coverage](https://img.shields.io/scrutinizer/coverage/g/iPublikuj/mobile-detect.svg?style=flat-square)](https://scrutinizer-ci.com/g/iPublikuj/mobile-detect/?branch=master)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/iPublikuj/mobile-detect.svg?style=flat-square)](https://scrutinizer-ci.com/g/iPublikuj/mobile-detect/?branch=master)
[![Latest Stable Version](https://img.shields.io/packagist/v/ipub/mobile-detect.svg?style=flat-square)](https://packagist.org/packages/ipub/mobile-detect)
[![Composer Downloads](https://img.shields.io/packagist/dt/ipub/mobile-detect.svg?style=flat-square)](https://packagist.org/packages/ipub/mobile-detect)
[![License](https://img.shields.io/packagist/l/ipub/mobile-detect.svg?style=flat-square)](https://packagist.org/packages/ipub/mobile-detect)

Extension for detecting mobile devices, managing mobile view types, redirect to mobile version for [Nette Framework](http://nette.org/)

## Introduction

This extension use [Mobile_Detect](https://github.com/serbanghita/Mobile-Detect) class and provides the following features:

* Detect the various mobile devices by name, OS, browser User-Agent
* Manages site views for the variuos mobile devices (`mobile`, `tablet`, `full`)
* Redirects to mobile and tablet sites

## Installation

The best way to install ipub/mobile-detect is using  [Composer](http://getcomposer.org/):

```sh
$ composer require ipub/mobile-detect
```

After that you have to register extension in config.neon.

```neon
extensions:
	mobileDetect: IPub\MobileDetect\DI\MobileDetectExtension
```

Package contains trait, which you will have to use in class, where you want to use mobile detector.

```php
<?php

class BasePresenter extends Nette\Application\UI\Presenter
{
    use IPub\MobileDetect\TMobileDetect;
    
    // Rest of code...
}
```


## Documentation

Learn how to get info about visitor device in [documentation](https://github.com/iPublikuj/mobile-detect/blob/master/docs/en/index.md).

***
Homepage [http://www.ipublikuj.eu](http://www.ipublikuj.eu) and repository [http://github.com/iPublikuj/mobile-detect](http://github.com/iPublikuj/mobile-detect).
