# FlexModelBundle

[![Software License][icon-license]](LICENSE.md)

FlexModel integration for Symfony 3+.

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
        new FlexModel\FlexModelBundle(),
        // ...
    );
}
```

### Configure the bundle

Add the following configuration to your `config.yml` file:

``` yml
# app/config/config.yml

flex_model:
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

# Credits

- [Niels Nijens][link-author]
- [All Contributors][link-contributors]

## License

This package is licensed under the MIT License. Please see the [LICENSE file](LICENSE.md) for details.

[icon-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg

[link-symfony-form-documentation]: http://symfony.com/doc/current/book/forms.html
[link-author]: https://github.com/niels-nijens
[link-contributors]: ../../contributors
