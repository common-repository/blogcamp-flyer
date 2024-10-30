<?php
/**
 * @package BlogCampFlyerClient
 * @subpackage lib
 */
class BlogCampFlyerEvent
{
  /**
   * Available attributes
   * 
   * @private
   * @var
   */
  var $_attributes = array(
    'address' =>          '',
    'blogAnnounceUrl' =>  '',
    'date' =>             '',
    'eventUrl' =>         '',
    'facebookEventUrl' => '',
    'flickrUrl' =>        '',
    'name' =>             '',
    'place' =>            '',
    'widget' =>       ''
  );

  /**
   * Event constructor
   * 
   * @author 
   * @return 
   */
  function BlogCampFlyerEvent()
  {
  }

  /**
   * Generic getter
   * 
   * @author oncletom
   * @return string
   * @param string $attribute
   * @param string $alt_value[optional]
   */
  function get($attribute, $alt_value = '')
  {
    return stripslashes(isset($this->_attributes[$attribute]) ? $this->_attributes[$attribute] : $alt_value);
  }

  /**
   * Checks if the event is valid or not
   * 
   * We check:
   * - date validity (we want only future events)
   * 
   * @author oncletom
   * @return Boolean
   */
  function isValid()
  {
    return strtotime($this->_attributes['date']) <= time() ? false : true;
  }

  /**
   * Generic setter
   * 
   * @author oncletom
   * @return null
   * @param string $attribute
   * @param string $value
   */
  function set($attribute, $value)
  {
    if (isset($this->_attributes[$attribute]))
    {
      $this->_attributes[$attribute] = $value;
    }
  }

  /**
   * Displays a widget as HTML
   * 
   * @author oncletom
   * @return string
   */
  function show($return = true)
  {
    if ($return === true)
    {
      return $this->get('widget');
    }
    else
    {
      print $this->get('widget');
    }
  }
}