<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedactivity
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Activity.php 6590 2012-26-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Advancedactivity_Api_Activity extends Core_Api_Abstract {

  /**
   * Loader for parsers
   * 
   * @var Zend_Loader_PluginLoader
   */
  protected $_pluginLoader;


  // Parsing

  /**
   * Activity template parsing
   * 
   * @param string $body
   * @param array $params
   * @return string
   */
  public function assemble($body, array $params = array()) {
    // Translate body
    $body = $this->getHelper('translate')->direct($body);

    // Do other stuff
    preg_match_all('~\{([^{}]+)\}~', $body, $matches, PREG_SET_ORDER);
    foreach ($matches as $match) {
      $tag = $match[0];
      $args = explode(':', $match[1]);
      $helper = array_shift($args);

      $helperArgs = array();
      foreach ($args as $arg) {
        if (substr($arg, 0, 1) === '$') {
          $arg = substr($arg, 1);
          $helperArgs[] = ( isset($params[$arg]) ? $params[$arg] : null );
        } else {
          $helperArgs[] = $arg;
        }
      }

      $helper = $this->getHelper($helper);
      $r = new ReflectionMethod($helper, 'direct');
      $content = $r->invokeArgs($helper, $helperArgs);
      $content = preg_replace('/\$(\d)/', '\\\\$\1', $content);
      $body = preg_replace("/" . preg_quote($tag) . "/", $content, $body, 1);
    }

    return $body;
  }

  /**
   * Gets the plugin loader
   * 
   * @return Zend_Loader_PluginLoader
   */
  public function getPluginLoader() { // Customize this functions 
    if (null === $this->_pluginLoader) {
      $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR
              . 'modules' . DIRECTORY_SEPARATOR
              . 'Advancedactivity';
      $this->_pluginLoader = new Zend_Loader_PluginLoader(array(
                  'Advancedactivity_Model_Helper_' => $path . '/Model/Helper/'
              ));
    }

    return $this->_pluginLoader;
  }

  /**
   * Get a helper
   * 
   * @param string $name
   * @return Activity_Model_Helper_Abstract
   */
  public function getHelper($name) {
    $name = $this->_normalizeHelperName($name);
    if (!isset($this->_helpers[$name])) {
      $helper = $this->getPluginLoader()->load($name);
      $this->_helpers[$name] = new $helper;
    }

    return $this->_helpers[$name];
  }

  /**
   * Normalize helper name
   * 
   * @param string $name
   * @return string
   */
  protected function _normalizeHelperName($name) {
    $name = preg_replace('/[^A-Za-z0-9]/', '', $name);
    //$name = strtolower($name);
    $name = ucfirst($name);
    return $name;
  }

}