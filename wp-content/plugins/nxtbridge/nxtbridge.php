<?php

/*
  Plugin Name: NXTBridge AE
  Plugin URI: https://nxter.org/nxtbridge
  Version: 1.0.5
  Author: scor2k 
  Description: Show Nxt asset information on your Wordpress sites.
  License: GPLv2 or later.
  License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/
global $api;
$version = '1.0.5'; // NOT FORGET TO CHANGE !!!

$api = '//api.nxter.org/v2';
//$api = 'http://python-srv:8000/v2';

/*****************************************************************************************************/
require ('lib/assets-info.php');        // NXTBridgeAssetInfo and NXTBridgeAssetPrices and NXTBridgeTop50
require ('lib/assets-graphics.php');    // NXTBridgeAssetStock and NXTBridgeAssetCandlestick 
/*****************************************************************************************************/
require ('config.php');                 // actions and registered scripts and hooks
/*****************************************************************************************************/
require('lib/options.php');             // NXTBridge Admin settings
/*****************************************************************************************************/
require('lib/fakepage.php');            // NXTBridge Fake Page
/*****************************************************************************************************/

/*****************************************************************************************************/
// FILTERS for frontpage

add_filter('the_content', 'nxtbridge_top50');
add_filter('the_content', 'nxtbridge_info');
add_filter('the_content', 'nxtbridge_price');
add_filter('the_content', 'nxtbridge_stockchart');
add_filter('the_content', 'nxtbridge_candlestick');
/*****************************************************************************************************/

?>
