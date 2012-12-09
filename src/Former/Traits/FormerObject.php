<?php
/**
 * FormerObject
 *
 * Base Former object that allows chained attributes setting
 */
namespace Former\Traits;

use \Underscore\Arrays;

abstract class FormerObject
{
  /**
   * The Form open tag's attribute
   * @var array
   */
  protected $attributes = array();

  /**
   * Dynamically set attributes
   *
   * @param  string $method     An attribute
   * @param  array  $parameters Its value(s)
   */
  public function __call($method, $parameters)
  {
    // Redirect calls to the Control Group
    if (method_exists($this->controlGroup, $method)) {
      call_user_func_array(array($this->controlGroup, $method), $parameters);

      return $this;
    }

    // Replace underscores
    $method = str_replace('_', '-', $method);

    // Get value and set it
    $value = Arrays::get($parameters, 0, 'true');
    $this->setAttribute($method, $value);

    return $this;
  }

  /**
   * Get a Field variable or an attribute
   *
   * @param  string $attribute The desired attribute
   * @return string            Its value
   */
  public function __get($attribute)
  {
    if(isset($this->$attribute)) return $this->$attribute;
    else return Arrays::get($this->attributes, $attribute);
  }

  /**
   * Set an attribute
   *
   * @param string $attribute An attribute
   * @param string $value     Its value
   */
  public function setAttribute($attribute, $value = null)
  {
    $this->attributes[$attribute] = $value;

    return $this;
  }

  /**
   * Set a bunch of parameters at once
   *
   * @param array   $attributes An associative array of attributes
   * @param boolean $merge      Whether they should be merged to the old ones
   */
  public function setAttributes($attributes, $merge = true)
  {
    $attributes = (array) $attributes;

    $this->attributes = $merge
      ? array_merge($this->attributes, $attributes)
      : $attributes;

    return $this;
  }

  /**
   * Add a class to the current field
   *
   * @param string $class The class to add
   */
  public function addClass($class)
  {
    if(is_array($class)) $class = implode(' ', $class);

    $this->attributes = $this->app['former.helpers']->addClass($this->attributes, $class);

    return $this;
  }

  /**
   * Render the current field
   *
   * @return string The field wrapped
   */
  public function __toString()
  {
    return $this->render();
  }

}
