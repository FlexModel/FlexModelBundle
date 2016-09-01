# FlexModelBundle

[![Software License][icon-license]](LICENSE.md)

FlexModel integration for Symfony.

## Installation using Composer
Run the following command to add the package to the composer.json of your project:

``` bash
$ composer require flexmodel/flexmodel-bundle
```

### Enable the bundle
Enable the bundle in the kernel:

``` php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new FlexModel\FlexModelBundle\FlexModelBundle(),
        // ...
    );
}
```

### Configure the bundle
Add the following configuration to your `config.yml` file:

``` yml
# app/config/config.yml

flex_model:
    bundle_name: AppBundle # optional, defaults to 'AppBundle'
    resource: "%kernel.root_dir%/../src/AppBundle/Resources/config/flexmodel.xml"
```

The `resource` should refer to the location of your FlexModel configuration file.


## Usage

### Generating Doctrine ORM mapping
Run the following command to generate Doctrine ORM mapping and entity classes from your FlexModel configuration:

``` bash
$ php bin/console flexmodel:generate
```

After generating the Doctrine ORM mapping you need to update your database schema:

``` bash
$ php bin/console doctrine:schema:update --force
```

### Creating a Form
To create a form from a FlexModel form configuration you need to use the `FlexModelFormType` class.

The class will retrieve the form configuration based on the name of the entity and provided 'form_name' option.

``` php
// src/AppBundle/Controller/DefaultController.php

public function newAction()
{
    $entity = ...;
    $form = $this->createForm(FlexModelFormType::class, $entity, array(
        'form_name' => '',
    ));
}

```

For more information on creating forms, please see the [Symfony documentation][link-symfony-form-documentation].

### File uploads
The FlexModelBundle provides support for file uploads within FlexModel forms.

To activate this support you need to complete the following steps:

#### 1. Configure the FlexModelBundle for file uploads
The bundle requires a location to store the file uploads.
Configure the location with the existing FlexModelBundle configuration in your `config.yml` file:

``` yml
# app/config/config.yml

flex_model:
    resource: "%kernel.root_dir%/../src/AppBundle/Resources/config/flexmodel.xml"
    file_upload_path: "%kernel.root_dir%/../../some-directory-outside-of-the-project/%kernel.environment%"
```

#### 2. Modify your Doctrine entity class
To activate file uploads for a Doctrine entity you need to implement the `UploadObjectInterface` and add getters and setters for the form fields.

For ease of use the FlexModelBundle provides an `UploadTrait` to implement both the interface and the getters and setters:

``` php
namespace AppBundle\Entity;

use FlexModel\FlexModelBundle\Model\UploadObjectInterface;
use FlexModel\FlexModelBundle\Model\UploadTrait;

class Entity implements UploadObjectInterface
{
    use UploadTrait {
        getFileUpload as getImageUpload;
        setFileUpload as setImageUpload;
        getFileUpload as getAnotherImageUpload;
        setFileUpload as setAnotherImageUpload;
    }
}

```

In the above example you see the `UploadTrait` with getters and setters for two file upload fields implemented.
Here the `getImageUpload` method maps to a FlexModel field called 'image' and `getAnotherImageUpload` maps to 'another_image'.


## Credits

- [Niels Nijens][link-author]
- [All Contributors][link-contributors]

### License

This package is licensed under the MIT License. Please see the [LICENSE file](LICENSE.md) for details.

[icon-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg

[link-symfony-form-documentation]: http://symfony.com/doc/current/book/forms.html
[link-author]: https://github.com/niels-nijens
[link-contributors]: ../../contributors
