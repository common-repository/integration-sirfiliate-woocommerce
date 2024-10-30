# Introduction

This module gives the ability to run validation for field values. It has the following pre-installed features:

* Extends module [Action Bulker](https://gitlab.com/woosa/wp-plugin-modules/action-bulker) and runs a validation for the given items
* Extends module [Meta](https://gitlab.com/woosa/wp-plugin-modules/meta) and runs a validation for each metadata before saving its value
* Extends module [Product](https://gitlab.com/woosa/wp-plugin-modules/product) and runs a validation for the given product variations
* Extends module [Settings](https://gitlab.com/woosa/wp-plugin-modules/settings) and runs a validation for the given settins. It also disabled settings if the criteria is met

## Dependencies

* [Meta](https://gitlab.com/woosa/wp-plugin-modules/meta)
* [Option](https://gitlab.com/woosa/wp-plugin-modules/option)
* [Util](https://gitlab.com/woosa/wp-plugin-modules/util)

## Setup

* Installing via composer requires only to include the `index.php` file from root in your code
* Replace all occurences of `_wsa_namespace_` with your unique namespace
* Replace all occurences of `_wsa_text_domain_` with your translation text domain