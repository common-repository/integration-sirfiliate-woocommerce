### 1.6.0 - 2022-08-03

* [FEATURE] - New method `Util_Price::round_up()` to round the price up

### 1.5.1 - 2022-07-20

* [FIX] - Add default time limit when the `php_time_limit` is equal to `0`

### 1.5.0 - 2022-07-15

* [FEATURE] - New methods `Uti::get_upload_path()` and `Util::get_upload_url()` to retrieve path and URL of the uploads directory

### 1.4.1 - 2022-06-29

* [FIX] - Solve the error `Type error Util::dimension_to_cm number format accepts only float`
* [FIX] - Ensure `Util::get_template()` does not add slashes in absolute or relative path

### 1.4.0 - 2022-05-11

* [FIX] - Check if the price is numeric before to add the calculation
* [FEATURE] - New class `Util_File` dedicated for working with files
* [FEATURE] - New class `Util_Status` dedicated for displaying UI statuses
* [DEPRECATION] - The method `Util::get_status_html()` is deprecated, use `Util::status()->render()` instead
* [DEPRECATION] - The method `Util::status_list()` is deprecated, use `Util::status()->list()` instead

### 1.3.2 - 2022-04-14

* [FIX] - Replace deprecated constant `FILTER_SANITIZE_STRING` with `FILTER_DEFAULT`
* [TWEAK] - Add new method `Util_Array::get_post_content()` to extract from an array sanitized string allowed to be used as a post content

### 1.3.1 - 2022-03-23

* [FIX] - Use `untrailingslashit()` instead of `trim()` for assest paths

### 1.3.0 - 2022-03-09

* [FEATURE] - New method to calculate the price discount - `Util::price()->discount()`
* [DEPRECATION] - The method `Util::calculate_price_with_addition()` is deprecated, use `Util::price()->addition()` instead

### 1.2.2 - 2022-02-28

* [FIX] - Set the max execution time to 300 seconds and the max memory to 2GB