# Introduction

This is the base module which gives the ability to build a plugin by using the other modules. As pre-installed features:

* It fires hooks for each state: `activated`, `deactivated`, `upgraded` and `uninstalled`
* Used in conjunction with [Dependency](https://gitlab.com/woosa/wp-plugin-modules/dependency) it peforms a check for the dependencies before to initiate the plugin
* It gives the ability to insert action links to the plugin (e.g. Settings, Logs, Doc, etc)
* It loads the translation based on the given text domain
* It sets an instance of the website including the domain and URL of it

## Dependencies

* [Option](https://gitlab.com/woosa/wp-plugin-modules/option)
* [Util](https://gitlab.com/woosa/wp-plugin-modules/util)

## How to use

To initialize the module in your code just add:

```php
Module_Core_Hook::init();
```