# MobileDetect

Detect mobile devices, manage mobile view and redirect to the mobile and tablet version for [Nette Framework](http://nette.org/)

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
	images: IPub\MobileDetect\DI\MobileDetectExtension
```