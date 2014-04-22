# Device detection

Simple image storage for [Nette Framework](http://nette.org/)

## Instalation

The best way to install ipub/device-detection is using  [Composer](http://getcomposer.org/):


```json
{
	"require": {
		"ipub/device-detection": "dev-master"
	}
}
```

After that you have to register extension in config.neon.

```neon
extensions:
	images: IPub\DeviceDetection\DI\DeviceDetectionExtension
```

Package contains trait, which you will have to use in class, where you want to use image storage. This works only for PHP 5.4+, for older version you can simply copy trait content and paste it into class where you want to use it.

```php
<?php

class BasePresenter extends Nette\Application\UI\Presenter
{

	use IPub\DeviceDetection\TImagePipe;

}
```

## Usage

### Saving images

In Form\Control\Presenter

```php

	/**
	 * @inject
	 * @var IPub\DeviceDetection\ImageStorage
	 */
	public $storage;


	public function handleUpload(Nette\Http\FileUpload $file)
	{
		$this->storage->upload($fileUpload); // saves to %wwwDir%/media/original/filename.jpg

		# or

		$this->storage->setNamespace("products")->upload($fileUpload); // saves to %wwwDir%/media/products/original/filename.jpg
	}
```

### Using in Latte

```html
<a href="{img products/filename.jpg}"><img n:img="filename.jpg, 200x200, fill" /></a>
```

output:

```html
<a href="/media/products/original/filename.jpg"><img n:img="/media/200x200_fill/filename.jpg" /></a>
```

### Resizing flags

For resizing (third argument) you can use these keywords - `fit`, `fill`, `exact`, `stretch`, `shrink_only`. For details see comments above [these constants](http://api.nette.org/2.0/source-common.Image.php.html#105)
