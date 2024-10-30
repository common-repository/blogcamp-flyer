<?php
/*
Plugin Name: Blogcamp Flyer
Plugin URI: http://blogcamp.fr/widgets/
Description: Affichez le prochain BlogCamp dans un widget ! <a href="widgets.php"><strong>Ajouter/configurer le widget</strong></a>
Version: 1.0.1-dev
Author: BlogCamp France
Author URI: http://blogcamp.fr/
*/

//modify this constant to override plugin default
//usefull for testing or your own purpose
//@todo add a filter to plug plugins over this one
//define('BLOGCAMP_FLYER_SERVER_URL', '');

require dirname(__FILE__).'/lib/BlogCampFlyerClientConfiguration.class.php';
require dirname(__FILE__).'/lib/BlogCampFlyerClientWidget.class.php';
BlogCampFlyerClientConfiguration::configure(__FILE__);
BlogCampFlyerClientWidget::configure();
