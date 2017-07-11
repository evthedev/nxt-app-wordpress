<?php
/*
* Plugin Name: Fake Page Plugin 2
* Plugin URI: http://scott.sherrillmix.com/blog/blogger/creating-a-better-fake-post-with-a-wordpress-plugin/
* Description: Creates a fake page without a 404 error (based on <a href="http://headzoo.com/tutorials/wordpress-creating-a-fake-post-with-a-plugin">Sean Hickey's Fake Plugin Page</a>)
* Author: Scott Sherrill-Mix
* Author URI: http://scott.sherrillmix.com/blog/
* Version: 1.1
*/
 
class FakePage {
    var $url = '';
    /**
     * The slug for the fake post.  This is the URL for your plugin, like:
     * http://site.com/about-me or http://site.com/?page_id=about-me
     * @var string
     */
    var $page_slug = 'ae-';
   
    /**
     * The title for your fake post.
     * @var string
     */
    var $page_title = 'NXTBridge Asset page';
   
    /**
     * Allow pings?
     * @var string
     */
    var $ping_status = 'open';
       
    /**
     * Class constructor
     */
    //function FakePage()
    function __construct()
    {
        /**
         * We'll wait til WordPress has looked for posts, and then
         * check to see if the requested url matches our target.
         */
        add_filter('the_posts',array(&$this,'detectPost'));
    }
 
   
    /**
     * Called by the 'detectPost' action
     */
    function createPost()
    {
   
        /**
         * What we are going to do here, is create a fake post.  A post
         * that doesn't actually exist. We're gonna fill it up with
         * whatever values you want.  The content of the post will be
         * the output from your plugin.
         */  
       
        /**
         * Create a fake post.
         */
        $post = new stdClass;
       
        /**
         * The author ID for the post.  Usually 1 is the sys admin.  Your
         * plugin can find out the real author ID without any trouble.
         */
        $post->post_author = 1;
       
        /**
         * The safe name for the post.  This is the post slug.
         */
        $post->post_name = $this->page_slug;
       
        /**
         * Not sure if this is even important.  But gonna fill it up anyway.
         */
        $post->guid = get_bloginfo('wpurl') . '/' . $this->page_slug;
       
       
        /**
         * The title of the page.
         */
        $post->post_title = $this->page_title;
        //$post->post_title = $this->url;
       
        /**
         * This is the content of the post.  This is where the output of
         * your plugin should go.  Just store the output from all your
         * plugin function calls, and put the output into this var.
         */
        $post->post_content = $this->getContent();
       
        /**
         * Fake post ID to prevent WP from trying to show comments for
         * a post that doesn't really exist.
         */
        // -1 did not work with sitepress-multilingual-cms plugin
        $post->ID = 999999999;
       
        /**
         * Static means a page, not a post.
         */
        $post->post_status = 'static';
       
        /**
         * Turning off comments for the post.
         */
        $post->comment_status = 'closed';
       
        /**
         * Let people ping the post?  Probably doesn't matter since
         * comments are turned off, so not sure if WP would even
         * show the pings.
         */
        $post->ping_status = $this->ping_status;
       
        $post->comment_count = 0;
       
        /**
         * You can pretty much fill these up with anything you want.  The
         * current date is fine.  It's a fake post right?  Maybe the date
         * the plugin was activated?
         */
        $post->post_date = current_time('mysql');
        $post->post_date_gmt = current_time('mysql', 1);
 
        return($post);   
    }
   
    function getContent()
    {
      global $api;
      $msg = '';

      // DEFAULT ASSET
      $asset_id = '12422608354438203866';

      $asset_id = substr( $this->url, strrpos( $this->url, '-') + 1 );

      $msg .= sprintf("<div class='nb-asset-info' id='%s_info'></div>", $asset_id );
      $msg .= "<br>";
      $msg .= sprintf("<div class='nb-prices' id='%s_prices'></div>", $asset_id);
      $msg .= "<br><br>";
      $msg .= sprintf("<div class='nb-candlestick' id='%s_candlestick'></div>", $asset_id );

      ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      $msg .= "<script>";
      $msg .= "jQuery(document).ready(function($){";

      $msg .= sprintf("$.getJSON('%s/asset/%s/show-info', function (info) {", $api, $asset_id);
      $msg .= "if ( info.data ) { "; 
      $msg .= sprintf("$('#%s_info').html( info.data ); }})", $asset_id);
      $msg .= sprintf(".fail( function() { $('#%s_info').html('Error while load info about asset ID= %s.'); } );", $asset_id, $asset_id);

      $msg .= sprintf("$.getJSON('%s/asset/%s/show-prices', function (info) {", $api, $asset_id);
      $msg .= "if ( info.data ) { "; 
      $msg .= sprintf("$('#%s_prices').html( info.data ); }}) ", $asset_id);
      $msg .= sprintf(".fail( function() { $('#%s_prices').html('Error while load prices about asset ID= %s.'); } );", $asset_id, $asset_id);

      $msg .= sprintf("$.getJSON('%s/asset/%s/get-candlestick-and-volume', function (info) {", $api, $asset_id);
      $msg .= sprintf("if ( info.data ) { data = info.data; "); 

      $msg .= sprintf("var ohlc = [], volume = [], dataLength = data.length, i=0;");
      $msg .= sprintf("var groupingUnits = [[ 'week', [1] ], [ 'month', [1,2,3,6] ]];");  
      $msg .= sprintf("for (i;i<dataLength; i++) {");
      $msg .= sprintf("ohlc.push([ data[i][0], data[i][1], data[i][2], data[i][3], data[i][4] ]);");
      $msg .= sprintf("volume.push([ data[i][0], data[i][5] ]);");
      $msg .= sprintf("}");

      $msg .= sprintf("Highcharts.stockChart('%s_candlestick', {", $asset_id);
      $msg .= sprintf("rangeSelector: { buttons: [{ type: 'week', count: 1, text: '1W'}, { type: 'month', count: 1, text: '1M'}, { type: 'all', count: 1, text: 'All'} ], selected: 1, inputEnabled: false }, title: {text: 'Asset prices and volumes'}, \n");
      $msg .= sprintf("yAxis: [\n");  
      $msg .= sprintf("{ labels: { align: 'right', x: -3}, title: { text: 'OHLC'   }, height: '60%%', offset: 0, lineWidth: 2 }, \n");  
      $msg .= sprintf("{ labels: { align: 'right', x: -3}, title: { text: 'Volume' }, top: '65%%', height: '35%%', offset: 0, lineWidth: 2 } \n");  
      $msg .= sprintf("], \n");  
      $msg .= sprintf("series: [{type: 'candlestick', name: 'NXT', data: ohlc, dataGrouping: { units: groupingUnits } }, {type: 'column', name: 'Volume', data: volume, yAxis: 1, dataGrouping: { units: groupingUnits } } ]");  
      $msg .= sprintf("});"); // highchart
      $msg .= sprintf("}");  // if (info.data)
      $msg .= sprintf("})"); // getJSON 
      $msg .= sprintf(".fail( function() { $('#%s_candlestick').html('Error while load StockChart for asset ID= %s.'); } );", $asset_id, $asset_id);

      $msg .= "});"; // jQuery
      $msg .= "</script>";



      ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

      $msg .= sprintf("</div></div>");


      return $msg;

    }
 
    function detectPost($posts){
        global $wp;
        global $wp_query;
        global $wpdb;
        /**
         * Check if the requested page matches our target
         */

        $pos = strpos(strtolower($wp->request), 'ae-');
        //$exists = is_page ( $wp->request );

        $exists = $wpdb->get_row("SELECT post_name FROM $wpdb->posts WHERE post_name = '" . $wp->request . "'" );
        //var_dump ( $exists ) ;
        //var_dump($wp->request);

        if ( $pos === 0 && strlen($wp->request) > 9 && $exists == NULL ){
            $this->url = $wp->request;

            //Add the fake post

            $posts=NULL;
            $posts[]=$this->createPost();
       
            /**
             * Trick wp_query into thinking this is a page (necessary for wp_title() at least)
             * Not sure if it's cheating or not to modify global variables in a filter
             * but it appears to work and the codex doesn't directly say not to.
             */
            $wp_query->is_page = true;
            //Not sure if this one is necessary but might as well set it like a true page
            $wp_query->is_singular = true;
            $wp_query->is_home = false;
            $wp_query->is_archive = false;
            $wp_query->is_category = false;
            //Longer permalink structures may not match the fake post slug and cause a 404 error so we catch the error here
            unset($wp_query->query["error"]);
            $wp_query->query_vars["error"]="";
            $wp_query->is_404=false;
           
        }
        return $posts;
    }
}
 
/**
* Create an instance of our class.
*/
new FakePage;

?>
