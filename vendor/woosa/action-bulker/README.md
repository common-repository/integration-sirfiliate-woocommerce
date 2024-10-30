# Introduction

This module gives the ability to extend easily the bulk action dropdown of any custom post type. It comes with the following pre-installed features:

* It has the option to run a custom validation for each item before the action applies
* It has the option to either perform the action via scheduled actions or instantly

## Dependencies

* [Util](https://gitlab.com/woosa/wp-plugin-modules/util)

## Setup

* Installing via composer requires only to include the `index.php` file from root in your code
* Replace all occurences of `_wsa_namespace_` with your unique namespace
* Replace all occurences of `_wsa_text_domain_` with your translation text domain

## Optional

* Use module [Validation](https://gitlab.com/woosa/wp-plugin-modules/validation) for a built-in logic which already exdends this module and runs a validation per each item
* Use module [Action Scheduler](https://gitlab.com/woosa/wp-plugin-modules/action-scheduler) for a built-in logic which already exdends this module and performs scheduled actions

## How to use

Example of how to define a bulk action for WooCommerce product:

```php
add_filter(PREFIX . '\action_bulker\actions', 'my_custom_actions');

function my_custom_actions($items){

   $items['my_action_id'] = [
      'label'         => __('Cool Action Label', 'my_text_domain'),
      'post_type'     => ['product'], //the post type where to add the action
      'callback'      => [__CLASS__, 'my_callback_function'],
      'bulk_perform'  => true, //whether or not to run the action for each item indivitually or per entire list of items
      'schedulable'   => true, //whether or not to be a scheduled action
      'validate_item' => true, //whether or not to run a validation per item
   ];

   return $items;
}
```