<?php

if (!function_exists('hook')) {
    /**
     * instance of Hooks class
     *
     * @return object
     */
    function hook()
    {
        return app('hooks');
    }
}

if (!function_exists('add_filter')) {

    /**
     * add_filter
     *
     * @param string $tag
     * @param callback $callback
     * @param integer $priority
     * @param integer $accepted_args
     * @return boolean
     */
    function add_filter($tag, $callback, $priority = 10, $accepted_args = 1)
    {
        return hook()->add_filter($tag, getHookCallback($callback), $priority, $accepted_args);
    }
}

if (!function_exists('apply_filters')) {

    /**
     * Call the functions added to a filter hook.
     *
     * @param string $tag
     * @param mixed $value
     * @return mixed
     */
    function apply_filters($tag, $value)
    {
        return hook()->apply_filters($tag, $value);
    }
}

if (!function_exists('do_action')) {

    /**
     * Execute functions hooked on a specific action hook
     *
     * @param string $tag
     * @param mixed $arg
     * @return void
     */
    function do_action($tag, $arg = '')
    {
        hook()->do_action($tag, $arg);
    }
}

if (!function_exists('add_action')) {

    /**
     * Add a action for do_action
     *
     * @param string $tag
     * @param callback $callback
     * @param integer $priority
     * @param integer $accepted_args
     * @return void
     */
    function add_action($tag, $callback, $priority = 10, $accepted_args = 1)
    {
        return hook()->add_action($tag, getHookCallback($callback), $priority, $accepted_args);
    }
}

if (!function_exists('getHookCallback')) {

    /**
     * get callback function
     *
     * @param string $tag
     * @return void
     */
    function getHookCallback($callback)
    {
        if (is_string($callback) && strpos($callback, '@')) {
            $callback = explode('@', $callback);
            return [app('\\'.$callback[0]), $callback[1]];
        } 
        
        if (is_string($callback)) {
            return [app('\\'.$callback), 'handle'];
        } 
        
        if (is_callable($callback)) {
            return $callback;
        } 
        
        if (is_array($callback)) {
            return $callback;
        }

        throw new \Exception($callback . ' is not a Callable', 1);
    }
}
