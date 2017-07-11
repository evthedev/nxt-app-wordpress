<?php
  // Admin options

  class NXTBridgeSettingsPage {
    private $options;

    public function __construct() {
      add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
      add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    // add options page
    public function add_plugin_page () {
      add_options_page( 
        'NXTBridge Admin',
        'NXTBridge',
        'manage_options',
        'nxtbridge_settings',
        array( $this, 'create_admin_page' )
      );
    }

    // Options page callback
    public function create_admin_page() {
      $this->options = get_option('NXTBridge');
      ?>
      <div class='wrap'>
        <h2>NXTBridge Settings</h2>
        <form method='POST' action='options.php'>
        <?php
          // prints out all settings fields
          settings_fields('NXTBridge_settings_group');
          do_settings_sections('nxtbridge_settings');
          //submit_button();
        ?>
        </form>
      </div>
      <?php
    }

    // register and add setting
    public  function page_init() {

      register_setting(
        'NXTBridge_settings_group',
        'NXTBridge',
        array( $this, 'sanitize' )
      );

      add_settings_section( 
        'NXTBridge_assets_section',
        'Shortcuts for use with NXTBridge',
        array( $this, 'print_section_info'),
        'nxtbridge_settings'
      );

/*
      add_settings_field( 
        'NXTBridge_top50_prefix',
        'TOP50 URL prefix:',
        array( $this, 'top50_callback' ),
        'nxtbridge_settings',
        'NXTBridge_assets_section'
      );
*/

/*
      add_settings_field( 
        'NXTBridge_top50_default',
        'TOP50 Default URL:',
        array( $this, 'defaulturl_callback' ),
        'nxtbridge_settings',
        'NXTBridge_assets_section'
      );
*/
    
/*
     add_settings_field( 
        'NXTBridge_show_on_site',
        'Show round Logo on the site:',
        array( $this, 'showonsite_callback' ),
        'nxtbridge_settings',
        'NXTBridge_assets_section'
      );
*/

    }

      
    // Sanitize each settings field as needed
    // $param array $input contains all settings fields as array keys

    public function sanitize( $input ) {
      $new_input = array();
      if ( isset( $input['NXTBridge_top50_prefix'] ) ) {
        $new_input['NXTBridge_top50_prefix'] = sanitize_text_field( $input['NXTBridge_top50_prefix'] );
      }

      if ( isset( $input['NXTBridge_top50_default'] ) ) {
        $new_input['NXTBridge_top50_default'] = sanitize_text_field( $input['NXTBridge_top50_default'] );
      }

      if ( isset( $input['NXTBridge_show_on_site'] ) ) {
        $new_input['NXTBridge_show_on_site'] = '1';
      } else { $new_input['NXTBridge_show_on_site'] = '0'; } 

      return $new_input;
    }


    // Print the sections text
    public function print_section_info() {
      //print '- URL prefix must begin and end with "/". It will be using for create unique url for assets in TOP50 list. If you do not want to use prefix, just keep it empty. <br>';
      //print '- Default URL will be using when asset page does not exists.';
      //print 'You can show or hide the Nxt logo on the main page. If user click on the logo, he will be forwarded to NxtBridge-Ledger (user should have account on this site).';
      print '<ul>';
      print '<li>[NXTBridgeAssetInfo id=12422608354438203866] - Show short information about any asset. </li> ';
      print '<li>[NXTBridgeAssetStock id=12422608354438203866] - Show price chart </li>';
      print '<li>[NXTBridgeAssetPrice id=12422608354438203866] - Show last 10 asks and bids for asset</li> ';
      print '<li>[NXTBridgeAssetCandlestick id=12422608354438203866] - Show price and volume information on candlestick chart</li> ';
      print '<li>[NXTBridgeTop50] - Show top 20 (not 50, sorry) assets with maximum volume information on the last 7 days.</li> ';
      print '</ul>';
    }

    // get settings options array and print one of its values

    public function top50_callback() {
      printf(
        '<input type="text" id="NXTBridge_top50_prefix" name="NXTBridge[NXTBridge_top50_prefix]" value="%s" />',
        isset( $this->options['NXTBridge_top50_prefix'] ) ? esc_attr( $this->options['NXTBridge_top50_prefix']) : ''
      );
    }

    public function defaulturl_callback() {
      printf(
        '<input type="text" id="NXTBridge_top50_default" name="NXTBridge[NXTBridge_top50_default]" value="%s" />',
        isset( $this->options['NXTBridge_top50_default'] ) ? esc_attr( $this->options['NXTBridge_top50_default']) : ''
      );
    }

    public function showonsite_callback() {
      $checked = '';
      $val = isset( $this->options['NXTBridge_show_on_site'] ) && $this->options['NXTBridge_show_on_site'] == '1' ? true : false;
      if ( $val ) {
        $checked = 'checked';
      }
      printf( '<input type="checkbox" id="NXTBridge_show_on_site" name="NXTBridge[NXTBridge_show_on_site]" %s/>', $checked);
    }



  } // end class


  if ( is_admin() ) {
    $my_settings_page = new NXTBridgeSettingsPage(); 
  }

?>
