<?php

namespace Millat\LaravelHooks;

/**
 * PHP Hooks Class
 *
 * The PHP Hooks Class is a fork of the WordPress filters hook system 
 * into any php based system
 *
 * This class is based on the WordPress plugin API
 */
if (!class_exists('Hooks')){
  class Hooks
  {
    /**
     * $filters hooks
     * @var array
     */
    public $filters = [];

    /**
     * merged filters
     *
     * @var array
     */
    protected $merged_filters = [];

    /**
     * $actions 
     *
     * @var array
     */
    protected $actions = [];

    /**
     * $current_filter
     *
     * @var array
     */
    public $current_filter = [];
    
    /**
     * constructor
     *
     * @param mixed $args
     */
    public function __construct($args = null)
    {
      $this->filters = [];
      $this->merged_filters = [];
      $this->actions = [];
      $this->current_filter = [];
    }

    /**
     * add_filter
     *
     * @param string $tag
     * @param callback $callback
     * @param integer $priority
     * @param integer $accepted_args
     * @return boolean
     */
    public function add_filter($tag, $callback, $priority = 10, $accepted_args = 1)
    {
      $idx =  $this->hookUniqueId($tag, $callback, $priority);
      $this->filters[$tag][$priority][$idx] = ['function' => $callback, 'accepted_args' => $accepted_args];
      unset( $this->merged_filters[ $tag ] );
      return true;
    }

    /**
     * Removes a function from a specified filter hook
     *
     * @param string $tag
     * @param callback $callbackFunction
     * @param integer $priority
     * @return boolean
     */
    public function remove_filter( $tag, $callbackFunction, $priority = 10 )
    {
      $callbackFunction = $this->hookUniqueId($tag, $callbackFunction, $priority);

      $r = isset($this->filters[$tag][$priority][$callbackFunction]);

      if ( true === $r) {
        unset($this->filters[$tag][$priority][$callbackFunction]);
        if ( empty($this->filters[$tag][$priority]) ) {
          unset($this->filters[$tag][$priority]);
        }
        unset($this->merged_filters[$tag]);
      }
      return $r;
    }

    /**
     * Remove all of the hooks from a filter
     *
     * @param string $tag
     * @param mixed $priority
     * @return boolean
     */
    public function remove_all_filters($tag, $priority = false)
    {
      if( isset($this->filters[$tag]) ) {
        if ( false !== $priority && isset($this->filters[$tag][$priority]) ) {
          unset($this->filters[$tag][$priority]);
        } 
        unset($this->filters[$tag]);
      }

      if ( isset($this->merged_filters[$tag]) ) {
        unset($this->merged_filters[$tag]);
      }
        

      return true;
    }

    /**
     * Check if any filter has been registered
     *
     * @param string $tag
     * @param callback $callback
     * @return mixed
     */
    public function has_filter($tag, $callback = false)
    {
      $isExist = !empty($this->filters[$tag]);
      
      if ( false === $callback || false == $isExist ) {
        return $isExist;
      }

      if ( !$idx = $this->hookUniqueId($tag, $callback, false) ) {
        return false;
      }

      foreach ( array_keys($this->filters[$tag]) as $priority ) {
        if ( isset($this->filters[$tag][$priority][$idx]) ) {
          return $priority;
        }
      }
      return false;
    }

    /**
     * Call the functions added to a filter hook.
     *
     * @param string $tag
     * @param mixed $value
     * @return mixed
     */
    public function apply_filters($tag, $value)
    {
      $args = [];
      
      if ( isset($this->filters['all']) ) {
        $this->current_filter[] = $tag;
        $args = func_get_args();
        $this->callAllHooks($args);
      }

      if ( !isset($this->filters[$tag]) ) {
        if ( isset($this->filters['all']) ) {
          array_pop($this->current_filter);
        }
        return $value;
      }

      if ( !isset($this->filters['all'] )) {
        $this->current_filter[] = $tag;
      }
      
      if ( !isset( $this->merged_filters[$tag] )) {
        ksort($this->filters[$tag]);
        $this->merged_filters[ $tag ] = true;
      }

      reset($this->filters[ $tag ]);

      if (empty($args)) {
        $args = func_get_args();
      }

      do {
        foreach(current($this->filters[$tag]) as $theCurrent ) {
          if ( !is_null($theCurrent['function']) ) {
            $args[1] = $value;
            $value = call_user_func_array($theCurrent['function'], array_slice($args, 1, (int) $theCurrent['accepted_args']));
          }
        }
      } while ( next($this->filters[$tag]) !== false );

      array_pop($this->current_filter);

      return $value;
    }

    /**
     * apply_filters_ref_array
     *
     * @param string $tag
     * @param mixed $args
     * @return mixed
     */
    public function apply_filters_ref_array($tag, $args)
    {
      if ( isset($this->filters['all']) ) {
        $this->current_filter[] = $tag;
        $all_args = func_get_args();
        $this->callAllHooks($all_args);
      }

      if ( !isset($this->filters[$tag]) ) {
        if ( isset($this->filters['all']) ) {
          array_pop($this->current_filter);
        }
        return $args[0];
      }

      if ( !isset($this->filters['all']) ) {
        $this->current_filter[] = $tag;
      }
      
      if ( !isset( $this->merged_filters[ $tag ] ) ) {
        ksort($this->filters[$tag]);
        $this->merged_filters[$tag] = true;
      }

      reset($this->filters[$tag]);

      do {
        foreach( (array) current($this->filters[$tag]) as $theCurrent ) {
          if ( !is_null($theCurrent['function']) ) {
            $args[0] = call_user_func_array($theCurrent['function'], array_slice($args, 0, (int) $theCurrent['accepted_args']));
          }
        }
      } while ( next($this->filters[$tag]) !== false );

      array_pop($this->current_filter);

      return $args[0];
    }

    /**
     * Add a action for do_action
     *
     * @param string $tag
     * @param callback $callback
     * @param integer $priority
     * @param integer $accepted_args
     * @return void
     */
    public function add_action($tag, $callback, $priority = 10, $accepted_args = 1)
    {
      return $this->add_filter($tag, $callback, $priority, $accepted_args);
    }

    /**
     * Check if any action has been registered
     *
     * @param string $tag
     * @param callback $callbackToCheck
     * @return boolean
     */
    public function has_action($tag, $callbackToCheck = false)
    {
      return $this->has_filter($tag, $callbackToCheck);
    }

    /**
     * Removes a function from a specified action
     *
     * @param string $tag
     * @param callback $function_to_remove
     * @param integer $priority
     * @return void
     */
    public function remove_action( $tag, $callback, $priority = 10 )
    {
      return $this->remove_filter( $tag, $callback, $priority );
    }

    /**
     * Remove all of the hooks from an action
     *
     * @param string $tag
     * @param integer $priority
     * @return void
     */
    public function remove_all_actions($tag, $priority = false)
    {
      return $this->remove_all_filters($tag, $priority);
    }

    /**
     * Execute functions hooked on a specific action hook
     *
     * @param string $tag
     * @param mixed $arg
     * @return void
     */
    public function do_action($tag, $arg = '')
    {
      if ( ! isset($this->actions) ) {
        $this->actions = array();
      }

      if ( ! isset($this->actions[$tag]) ) {
        $this->actions[$tag] = 1;
      } else {
        ++$this->actions[$tag];
      }
      
      if ( isset($this->filters['all']) ) {
        $this->current_filter[] = $tag;
        $all_args = func_get_args();
        $this->callAllHooks($all_args);
      }

      if ( !isset($this->filters[$tag]) ) {
        if ( isset($this->filters['all']) ) {
          array_pop($this->current_filter);
        }
        return;
      }

      if ( !isset($this->filters['all']) ) {
        $this->current_filter[] = $tag;
      }

      $args = [];
      if ( is_array($arg) && 1 == count($arg) && isset($arg[0]) && is_object($arg[0]) ) {
        $args[] =& $arg[0];
      } else {
        $args[] = $arg;
      }

      for ( $index = 2; $index < func_num_args(); $index++ ) {
        $args[] = func_get_arg($index);
      }
      
      if ( !isset( $this->merged_filters[ $tag ] ) ) {
        ksort($this->filters[$tag]);
        $this->merged_filters[ $tag ] = true;
      }

      reset( $this->filters[ $tag ] );

      do {
        foreach ( (array) current($this->filters[$tag]) as $the_ ) {
          if ( !is_null($the_['function']) ) {
            call_user_func_array($the_['function'], array_slice($args, 0, (int) $the_['accepted_args']));
          }
        }
      } while ( next($this->filters[$tag]) !== false );

      array_pop($this->current_filter);
    }

    /**
     * do_action_ref_array
     *
     * @param string $tag
     * @param mixed $args
     * @return void
     */
    public function do_action_ref_array($tag, $args)
    {
      if ( ! isset($this->actions) ) {
        $this->actions = array();
      }

      if ( ! isset($this->actions[$tag]) ) {
        $this->actions[$tag] = 1;
      } else {
        ++$this->actions[$tag];
      }

      if ( isset($this->filters['all']) ) {
        $this->current_filter[] = $tag;
        $all_args = func_get_args();
        $this->callAllHooks($all_args);
      }

      if ( !isset($this->filters[$tag]) ) {
        if ( isset($this->filters['all']) ) {
          array_pop($this->current_filter);
        }
        return;
      }

      if ( !isset($this->filters['all']) ) {
        $this->current_filter[] = $tag;
      }
      
      if ( !isset( $merged_filters[ $tag ] ) ) {
        ksort($this->filters[$tag]);
        $merged_filters[ $tag ] = true;
      }

      reset( $this->filters[ $tag ] );

      do {
        foreach( (array) current($this->filters[$tag]) as $theCurrent ) {
          if ( !is_null($theCurrent['function']) ) {
            call_user_func_array($theCurrent['function'], array_slice($args, 0, (int) $theCurrent['accepted_args']));
          }
        }
      } while ( next($this->filters[$tag]) !== false );

      array_pop($this->current_filter);
    }

    /**
     * Retrieve the number of times an action is fired
     *
     * @param string $tag
     * @return integer
     */
    public function did_action($tag)
    {
      if ( ! isset( $this->actions ) || ! isset( $this->actions[$tag] ) ) {
        return 0;
      }

      return $this->actions[$tag];
    }

    /**
     * HELPERS
     */

    /**
     * current filter
     *
     * @return void
     */
    public function current_filter()
    {
      return end( $this->current_filter );
    }

    /**
     * current action
     *
     * @return void
     */
    function current_action()
    {
      return $this->current_filter();
    }
    
    /**
     * doing_filter
     *
     * @param mixed $filter
     * @return void
     */
    function doing_filter( $filter = null )
    {
      if ( null === $filter ) {
        return ! empty( $this->current_filter );
      } 
      return in_array( $filter, $this->current_filter );
    }
    
    /**
     * doing_action
     *
     * @param mixed $action
     * @return void
     */
    public function doing_action( $action = null )
    {
      return $this->doing_filter( $action );
    }
    
    /**
     * Unique ID for storage and retrieval
     *
     * @param string $tag
     * @param callback $function
     * @param integer $priority
     * @return void
     */
    private function hookUniqueId($tag, $function, $priority)
    {
      static $filter_id_count = 0;

      if ( is_string($function) ) {
        return $function;
      }

      if ( is_object($function) ) {
        $function = array( $function, '' );
      } else {
        $function = (array) $function;
      }

      if (is_object($function[0]) ) {
        if ( function_exists('spl_object_hash') ) {
          return spl_object_hash($function[0]) . $function[1];
        } else {
          $obj_idx = get_class($function[0]).$function[1];
          if ( !isset($function[0]->filter_id) ) {
            if ( false === $priority ) {
              return false;
            }
            $obj_idx .= isset($this->filters[$tag][$priority]) ? count((array)$this->filters[$tag][$priority]) : $filter_id_count;
            $function[0]->filter_id = $filter_id_count;
            ++$filter_id_count;
          } else {
            $obj_idx .= $function[0]->filter_id;
          }

          return $obj_idx;
        }
      } else if ( is_string($function[0]) ) {
        return $function[0].$function[1];
      }
    }

    /**
     * callAllHooks
     *
     * @param mixed $args
     * @return void
     */
    public function callAllHooks($args) {
      reset( $this->filters['all'] );
      do {
        foreach( (array) current($this->filters['all']) as $theCurrent ) {
          if ( !is_null($theCurrent['function']) ) {
            call_user_func_array($theCurrent['function'], $args);
          }
        }
      } while ( next($this->filters['all']) !== false );
    }
  }
}

