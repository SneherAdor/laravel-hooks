# Laravel Hooks

The WordPress filters, actions system in Laravel.


## About

- An action interrupts the code flow to do something, and then returns back to the normal flow without modifying anything.
- A filter is used to modify something in a specific way so that the modification is then used by code later on.

[Read more about filters and actions](https://developer.wordpress.org/plugins/hooks/)

> Hooks are a way for one piece of code to interact/modify another piece of code at specific, pre-defined spots.

## Installation

```bash
composer require millat/laravel-hooks
```
**DONE!** Now you can use laravel-hooks

## Functions



```php
do_action(  string $tag,  mixed $arg )
```
This function invokes all functions attached to action hook `$tag`. It is possible to create new action hooks by simply calling this function, specifying the name of the new hook using the `$tag` parameter.

**Parameters**

- `$tag`
*(string)  (Required)  The name of the action to be executed.*

- `$arg`
*(mixed)  (Optional)  Additional arguments which are passed on to the functions hooked to the action. Default empty.*

```php
add_action( string $tag, callable $callback, int $priority = 10, int $accepted_args = 1 )
```
Actions are the hooks that the Laravel application launches at specific points during execution, or when specific events occur.

**Parameters**
- `$tag`
*(string) (Required) The name of the action to add the callback to.*

- `$callback`
*(callable) (Required) The callback to be run when the action is called.*

- `$priority`
*(int) (Optional) Used to specify the order in which the functions associated with a particular action are executed. Lower numbers correspond with earlier execution, and functions with the same priority are executed in the order in which they were added to the action. Default value: 10*

- `$accepted_args`
*(int) (Optional) The number of arguments the function accepts. Default value: 1*

```php
apply_filters( string $tag, mixed $value )
```
This function invokes all functions attached to filter hook `$tag`. It is possible to create new filter hooks by simply calling this function, specifying the name of the new hook using the `$tag` parameter.

**Parameters**
- `$tag`
*(string) (Required) The name of the filter hook.*

- `$value`
*(mixed) (Required) The value to filter.*

```php
add_filter( string $tag, callable $callback, int $priority = 10, int $accepted_args = 1 )
```
Filter hooks to allow modify various types of internal data at runtime.

**Parameters**
- `$tag`
*(string) (Required) The name of the filter to add the callback to.*

- `$callback`
*(callable) (Required) The callback to be run when the filter is applied.*

- `$priority`
*(int) (Optional) Used to specify the order in which the functions associated with a particular filter are executed. Lower numbers correspond with earlier execution, and functions with the same priority are executed in the order in which they were added to the filter. Default value: 10*

- `$accepted_args`
*(int) (Optional) The number of arguments the function accepts. Default value: 1*



## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
