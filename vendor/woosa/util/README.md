# Introduction

The the scope of this module to speed up the development by offering util and reusable functions.

## Setup

* Installing via composer requires only to include the `index.php` file from root in your code
* Replace all occurences of `_wsa_namespace_` with your unique namespace
* Replace all occurences of `_wsa_text_domain_` with your translation text domain

## How to use

Extract an array property without checking if that prop really exists:

```php
$arr = [
   'key_1' => 'abcd',
   'some_value1',
   'key_2' => [
      111,
      222,
      333
   ],
];

Util::array($arr)->get('key_1'); //outputs 'abcd'
Util::array($arr)->get('0'); //outputs 'some_value1'
Util::array($arr)->get('key_2/1'); //outputs '222'
```

Extract array property with specific validation:

```php
$arr = [
   'email' => 'customer\\"@gmail.com',
   'email2' => 'customer_gmail.com',
];

Util::array($arr)->get_email('email'); //outputs 'customer@gmail.com'
Util::array($arr)->get_email('email2'); //outputs 'false' - invalid email
```

Convert object to array:

```php
Util::obj_to_arr($object);
```

Check whether or not a string is json:

```php
Util::is_json($string);
```

Decode a string if is a valid JSON:

```php
Util::maybe_decode_json($string);
```

Prints a variable (especially arrays/objects) in a readable way:

```php
Util::print($variable);
```

Prefix a string with the plugin prefix:

```php
Util::prefix($string);
```

Remove the plugin prefix from a string:

```php
Util::unprefix($string);
```

Converts a string (e.g. 'yes' or 'no') to bool:

```php
//these values will return true, any other than these will return false
Util::string_to_bool('yes');
Util::string_to_bool('true');
Util::string_to_bool('1');
Util::string_to_bool(1);
```

Builds the final URL based on the given query params:

```php
Util::build_url('https://example.com/test', ['param1' => 'value1']);//https://example.com/test?param1=value1
```

Generates a random string:

```php
Util::random_string();//R4ttreD5rf
```

Registers and enqueues the given JS/CSS files.

```php
//register and enqueue CSS/JS files with the same name and prefix
Uti::enqueue_scripts([
   [
      'name' => 'admin_file',
      'css' => [
         'path' => 'my_path/to_the_folder_file/'//output: my_path/to_the_folder_file/{prefix}-admin_file.css
      ],
      'js' => [
         'path' => 'my_path/to_the_folder_file/'//output: my_path/to_the_folder_file/{prefix}-admin_file.js
      ]
   ]
]);

//register and enqueue CSS/JS files with different names and prefix
Uti::enqueue_scripts([
   [
      'css' => [
         'name' => 'admin_css_file',
         'path' => 'my_path/to_the_folder_file/'//output: my_path/to_the_folder_file/{prefix}-admin_css_file.css
      ],
      'js' => [
         'name' => 'admin_js_file',
         'path' => 'my_path/to_the_folder_file/'//output: my_path/to_the_folder_file/{prefix}-admin_js_file.js
      ]
   ]
]);

//register and enqueue CSS/JS files with no prefix in the name
Uti::enqueue_scripts([
   [
      'css' => [
         'handle' => 'admin_css_file',
         'path' => 'my_path/to_the_folder_file/'//output: my_path/to_the_folder_file/admin_css_file.css
      ],
      'js' => [
         'handle' => 'admin_js_file',
         'path' => 'my_path/to_the_folder_file/'//output: my_path/to_the_folder_file/admin_js_file.js
      ]
   ]
]);

//enqueue only - no register
Uti::enqueue_scripts([
   [
      'css' => [
         'handle' => 'admin_css_file',
         'register' => false
      ],
      'js' => [
         'handle' => 'admin_js_file',
         'register' => false
      ]
   ]
]);

//enqueue conditionally
Uti::enqueue_scripts([
   [
      'css' => [
         'handle' => 'admin_css_file',
         'enqueue' => is_checkout()
      ],
      'js' => [
         'handle' => 'admin_js_file',
         'enqueue' => is_checkout()
      ]
   ]
]);
```