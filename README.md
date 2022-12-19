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

**How to pass callback function?**

> The callback function could be a string referring to a class in the application `MyNamespace\Http\Listener@myHookListener`, an array callback `[$object, 'method']` or a globally registered function `global_function`, anonymous function.

Example using anonymous function:
```php
add_action('user_created', function($user) {
    $user->sendWelcomeMail();
}, 20, 1);
```

Example using referring to a class's method:
```php
add_action('user_created', 'MyNamespace\Http\MyClass@myMethod', 20, 1);
```

Example using array callback:
```php
add_action('user_created', [$object, 'myMethod'], 20, 1);
```

## Usage

### Actions

Wherever you want, you can create a new action in you Laravel application:

```php
do_action('user_created', $user);
```
Here, `user_created` is the name of the action, which will use later when the action will be listening. And `$user` is the parameters, which will be found whenever you listen the action. These can be anything.

To listen to your actions, you attach listeners. These are best added to your `AppServiceProvider` `boot()` method.

For example if you wanted to hook in to the above hook, you could do:

```php
add_action('user_created', function($user) {
    $user->sendWelcomeMail();
}, 20, 1);
```

The first argument must be the name of the action. The second would be a closures, callbacks and anonymous functions. The third specify the order in which the functions associated with a particular action are executed. Lower numbers correspond with earlier execution, and functions with the same priority are executed in the order in which they were added to the action. Default value: 10. And fourth, the number of arguments the function accepts. Default value: 1


### Filters
Filters always have to have data coming in and data going out to ensure the data is output in the browser (your content may be passed through other filters before getting output in the browser). By comparison, actions, which are similar to filters, do not require that anything to be returned, although data can be returned through actions as well.

Basically, filters are functions that can be used in Laravel application to pass data through. They allows developers to modify the default behavior of a specific function.


Here's an example of how filter used in a real application.

`Post.php` is a model or class , where it builds a query to fetch all published posts

```php
class Post extend Model
{
    public function getPublished()
    {
        return Post::where('published_at', '>', now());
    }
}
```

Using filter we can modify this query:

```php
class Post extend Model
{
    public function getPublished()
    {
        return apply_filters('posts_published', Post::where('published_at', '>', now());
    }
}
```

Now, in the entry point of application like any module or plugin's you can modify this post published query.

In Module's or Plugin's service provider (preferably in the boot method) we'll add a listener for the filter.
```php
class ModuleServiceProvider extends ServiceProvider
{
    public function boot()
    {
        add_filter('posts_published', function($query) {
            return $query->where('status', 'active');
        });
    }
}
```


## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
