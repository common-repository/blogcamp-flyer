<?php
/**
 * @package BlogCampFlyerClient
 * @subpackage lib
 */
class BlogCampFlyerClientConfiguration
{
  
  /**
   * Defines main hooks and variables
   * 
   * @author oncletom
   * @static
   * @return 
   */
  function configure($controller_path)
  {
    $class = __CLASS__;

    if (!defined('WP_CONTENT_DIR'))
    {
      define('WP_CONTENT_DIR', ABSPATH.'wp-content');
    }

    if (!defined('WP_PLUGIN_DIR'))
    {
      define('WP_PLUGIN_DIR', WP_CONTENT_DIR.'/plugins');
    }

    /*
     * Calculating full plugin path
     */
    $controller_real_path = WP_PLUGIN_DIR.'/blogcamp-flyer/'.basename($controller_path);

    /*
     * Activation/Deactivation Hooks
     */
    register_activation_hook($controller_real_path, array($class, 'executeActivation'));
    register_deactivation_hook($controller_real_path, array($class, 'executeDeactivation'));
  }

  /**
   * Configures the plugin on activation
   * Schedules the XML parser
   * 
   * @author oncletom
   * @static
   * @return 
   */
  function executeActivation()
  {
    $class = __CLASS__;
    $server_url = defined('BLOGCAMP_FLYER_SERVER_URL') && BLOGCAMP_FLYER_SERVER_URL ? BLOGCAMP_FLYER_SERVER_URL : 'http://blogcamp.fr/';

    add_option('blogcamp_flyer_server_url', $server_url, '', true);
    add_option('blogcamp_flyer_id', '', '', true);
    add_option('blogcamp_flyer_countries', '', '', false);

    wp_schedule_event(time(), 'hourly', 'BlogCampFlyerConfigurationUpdateEvents');
  }

  /**
   * Uninstall plugin settings
   * 
   * @author oncletom
   * @return 
   */
  function executeDeactivation()
  {
    $class = __CLASS__;

    delete_option('blogcamp_flyer_server_url');
    delete_option('blogcamp_flyer_id');
    delete_option('blogcamp_flyer_countries');

    wp_clear_scheduled_hook('BlogCampFlyerConfigurationUpdateEvents');
  }

  /**
   * Retrieves remote events and update the local cache
   * 
   * @author oncletom
   * @static
   * @return 
   */
  function executeEventsUpdate()
  {
    require_once dirname(__FILE__).'/BlogCampFlyerParser.class.php';
  
    $ws_uri = get_option('blogcamp_flyer_server_url').'ws-blogcamp/';
  
    $parser = new BlogCampFlyerParser($ws_uri);
    $countries = $parser->parse();
  
    update_option('blogcamp_flyer_countries', serialize($countries));
  }
}


/**
 * 
 * It is a function as WP Cron does not support valid callbacks ...
 * 
 * @author oncletom
 * @return null
 */
add_action('BlogCampFlyerConfigurationUpdateEvents', array('BlogCampFlyerClientConfiguration', 'executeEventsUpdate'));