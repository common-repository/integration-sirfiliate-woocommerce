# Introduction

This module is a wrapper for `get_option()`, `update_option()`, `delete_option()`, `get_transient()`, `set_transient()` and `delete_transient()`. It comes in handy because it automatically prefixes the option names with the plugin prefix so we do not need to specify it everytime.

## Dependencies

* [Util](https://gitlab.com/woosa/wp-plugin-modules/util)

## Setup

* Installing via composer requires only to include the `index.php` file from root in your code
* Replace all occurences of `_wsa_namespace_` with your unique namespace