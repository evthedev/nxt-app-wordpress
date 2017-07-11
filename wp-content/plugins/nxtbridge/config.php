<?php

// global
add_action('plugins_loaded', 'nxter_update_version');

// admin 
add_action('admin_enqueue_scripts', 'nxter_admin_styles');

function nxter_admin_styles() {
  wp_register_style('nxtbridge-wallet', plugins_url('wallet.css', __FILE__), '', false, 'all');
  wp_enqueue_style('nxtbridge-wallet');
}


// frontend
add_action('wp_enqueue_scripts', 'nxter_styles');
add_action('wp_enqueue_scripts', 'nxter_scripts');
add_action('wp_footer', 'nxter_footer');

function nxter_scripts() {
  wp_register_script('highstock', plugins_url('/bower_components/highcharts/highstock.js', __FILE__), array('jquery'), false, true); // in footer
  wp_register_script('momentjs', plugins_url('/bower_components/moment/min/moment.min.js', __FILE__), array('jquery'), false, true); // in footer

  wp_enqueue_script('highstock');
  wp_enqueue_script('momentjs');
}


function nxter_styles() {
  wp_register_style('nxtbridge-grid', plugins_url('grid.min.css', __FILE__), '', false, 'all'); 
  wp_register_style('nxtbridge', plugins_url('style.min.css', __FILE__), '', false, 'all');

  wp_enqueue_style('nxtbridge');
  wp_enqueue_style('nxtbridge-grid');
}

function nxter_footer() {
  global $api;
  $opt = get_option('NXTBridge');
  $prefix = isset( $opt['NXTBridge_top50_prefix'] ) ? esc_attr( $opt['NXTBridge_top50_prefix']) : '/';
   
  //TODO: Remove prefix
  printf('<input type="hidden" id="nxter-asset-prefix" value="%s" />', $prefix);
  printf('<input type="hidden" id="nxter-api" value="%s" />', $api);

  $show = isset( $opt['NXTBridge_show_on_site'] ) ? esc_attr( $opt['NXTBridge_show_on_site'] ) : '0';

}

function nxter_update_version() {
  global $wpdb;
  global $version;

  $nxtbridge_version = get_option('NXTBridge_version');
  if ( $nxtbridge_version == '' ) { 
    // old demo version, we need to clean all users settings
    $table_name = $wpdb->prefix . "options";
    $sql = "DELETE FROM $wpdb->options WHERE `option_name` like 'NXTBridge_%' and `option_name` != 'NXTBridge_settings' ";
    $wpdb->query( $sql );
    update_option('NXTBridge_version' , $version);
  } else {
    update_option('NXTBridge_version' , $version);
  }

}


?>
