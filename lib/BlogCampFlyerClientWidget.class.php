<?php
/**
 * @package BlogCampFlyerClient
 * @subpackage lib
 */
class BlogCampFlyerClientWidget
{
  
  /**
   * Executes basic configuration
   * 
   * @author oncletom
   * @static
   * @return null
   */
  function configure()
  {
    $class = __CLASS__;

    add_action('widgets_init', array($class, 'init'));
  }

  /**
   * Displays Widget on WP front sidebar
   * 
   * @author oncletom, celinecham
   * @static
   * @return null
   */
  function display($args)
  {
    extract($args);

    require_once dirname(__FILE__).'/BlogCampFlyerEvent.class.php';

    $id = get_option('blogcamp_flyer_id');
    $countries = get_option('blogcamp_flyer_countries');
    $countries = !$countries ? array() : unserialize($countries);

    /*
     * We must loop on every country to find the city
     * @todo improve that to avoid a loop on frontend
     */
    foreach ($countries as $country=> $cities)
    {
      if (isset($cities[$id]['events']) && !empty($cities[$id]['events']))
      {
        foreach ($cities[$id]['events'] as $event)
        {
          /*
           * Yay, we have an event
           */
          if ($event->isValid())
          {
            echo $before_widget;
            echo $before_title.$event->get('name').$after_title;
            echo '<div class="content" style="text-align:center">';
            echo $event->show();
            echo '</div>';

            printf('<p>Organisé par <a href="%s">%s</a>.</p>',
              $cities[$id]['blogUrl'],
              $cities[$id]['name']
            );
            echo $after_widget;
            break;
          }
        }
      }
      
      break;
    }
  }

  /**
   * Displays Widget controls in WP Admin
   * 
   * @todo checks wether it works for WP 2.7 as security has evolved
   * @author oncletom, celinecham
   * @static
   * @return null
   */
  function displayControls()
  {
    $countries = unserialize(get_option('blogcamp_flyer_countries'));

    /*
     * It's time to update options
     */
    if (isset($_POST['blogcamp_flyer_id']))
    {
      update_option('blogcamp_flyer_id', $_POST['blogcamp_flyer_id'], true);
    }

    $city_id = get_option('blogcamp_flyer_id');
    ?>
    <select name="blogcamp_flyer_id">
      <option value=""></option>
      <?php foreach ($countries as $country => $cities): ?>
        <optgroup label="<?php echo $country ?>">
        <?php foreach ($cities as $id => $city): ?>
          <option value="<?php echo $id ?>"<?php echo $city_id == $id ? ' selected="selected"' : ''?>><?php echo $city['cityName'] ?></option>
        <?php endforeach ?>
        </optgroup>
      <?php endforeach ?>
    </select>
    <?php
  }

  /**
   * Initialize Widget (called with a hook generally)
   * 
   * @author oncletom, celinecham
   * @static
   * @return null
   */
  function init()
  {
    $class = __CLASS__;
    register_sidebar_widget('BlogCamp Flyer', array($class, 'display'));
    register_widget_control('BlogCamp Flyer', array($class, 'displayControls'));
  }
}
