<?php
/**
 * 
 * @package BlogCampFlyerClient
 * @subpackage lib
 */
 
require_once(ABSPATH.WPINC.'/class-snoopy.php');


class BlogCampFlyerParser
{
  /**
   * @var
   */
  var $lastTag;

  /**
   * @var
   */
  var $xml = '';

  /**
   * 
   * @return 
   * @param object $file_or_uri
   */
  function BlogCampFlyerParser($file_or_uri)
  {
    /*
     * Loading a file
     */
    if (file_exists($file_or_uri))
    {
  
      $this->xml = file($file_or_uri);
      $this->xml = implode("\r\n", $this->xml);
    }
    /*
     * Loading a remote URL
     * @see wp-includes/class-snoopy.php
     */
    elseif ($file_or_uri)
    {
  
      $http = new Snoopy();
      $http->fetch($file_or_uri);
      
      $this->xml = $http->results;
      unset($http);
    }
    else
    {
      _e('File Not Found');
      return false;
    }

  }

  /**
   * 
   * @return 
   */
  function parse()
  {
    include_once dirname(__FILE__).'/BlogCampFlyerEvent.class.php';

    $countries = array();
    $parser = xml_parser_create();

    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
    xml_parse_into_struct($parser, $this->xml, $structure);
  
    foreach ($structure as $tag)
    {
      /*
       * Extracting Country name
       */
      if($tag['level'] == 3 && $tag['tag'] == 'name')
      {
        $current_country = $tag['value'];
        $countries[$current_country] = array();
      }

      /*
       * Closing the current city or opening
       */
      if ($tag['level'] == 3 && $tag['tag'] == 'city' && $tag['type'] == 'close')
      {
        if (isset($current_city) && !empty($current_city))
        {
          $countries[$current_country][$current_city['hash']] = $current_city;
        }
        
        $current_city = array('events' => array());
      }

      /*
       * Storing City attributes
       */
      if($tag['level'] == 4 && $tag['tag'] != 'events')
      {
        $current_city[$tag['tag']] = $tag['value'];
      }

      /*
       * Starting & Closing events
       */
      if ($tag['level'] == 4 && $tag['tag'] == 'events')
      {
        if ($tag['type'] == 'close' && isset($current_events))
        {
          $current_city['events'] = $current_events;
        }

        $current_events = array();
      }
      
      /*
       * Starting & Closing event
       */
      if ($tag['level'] == 5 && $tag['tag'] == 'event')
      {
        if ($tag['type'] == 'close' && $current_event instanceof BlogCampFlyerEvent)
        {
          $current_events[] = $current_event;          
        }

        $current_event = new BlogCampFlyerEvent();
      }

      /*
       * Storing event attributes
       */
      if ($tag['level'] == 6)
      {
        $current_event->set($tag['tag'], $tag['value']);
      }
    }
  
    return $countries;
  }

}
