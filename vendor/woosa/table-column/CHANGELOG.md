### 1.0.4 - 2022-04-05

* [FIX] - Check and remove columns if the post type does not match

### 1.0.3 - 2022-03-23

* [FIX] - Do not pass the second argument to `call_user_func_array()` as key array, this might cause errors like `Uncaught Error: Unknown named parameter...` if it's not correctly implemented