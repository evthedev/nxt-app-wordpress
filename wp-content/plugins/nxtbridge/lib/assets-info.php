<?php //**********

/*****************************************************************************************************/
function nxtbridge_info( $content ) {
  global $api;
  $id = '/ id=[0-9]*/';
  $pattern = '/\[NXTBridgeAssetInfo .*\]/';
  preg_match_all($pattern, $content, $result); //find all Asset patterns
  $result = array_unique($result[0]); // remove duplicates

  for ( $i=0; $i<count($result); $i++ ) {
    preg_match($id, $result[$i], $asset_id);
    if ( count($asset_id) > 0 ) {
      $asset_id = str_replace(' id=', '', $asset_id[0]);

      $a[$i] = "";
      $a[$i] .= sprintf("<div class='nb-asset-info' id='%s_info'></div>", $asset_id );

      $a[$i] .= "<script>";
      $a[$i] .= "jQuery(document).ready(function($){";
      $a[$i] .= sprintf("$.getJSON('%s/asset/%s/show-info', function (info) {", $api, $asset_id);
      $a[$i] .= "if ( info.data ) { "; 
      $a[$i] .= sprintf("$('#%s_info').html( info.data ); }})", $asset_id);
      $a[$i] .= sprintf(".fail( function() { $('#%s_info').html('Error while load info about asset ID= %s.'); } );", $asset_id, $asset_id);
      $a[$i] .= "});"; // jQuery
      $a[$i] .= "</script>";
    }

  }

  if ( ! empty ($a) ) {
    $content = str_replace($result, $a, $content);
  }

  return $content;
}

/*****************************************************************************************************/

function nxtbridge_price( $content ) {
  global $api;
  $id = '/ id=[0-9]*/';
  $pattern = '/\[NXTBridgeAssetPrice .*\]/';
  preg_match_all($pattern, $content, $result); //find all Asset patterns
  $result = array_unique($result[0]); // remove duplicates

  for ( $i=0; $i<count($result); $i++ ) {
    preg_match($id, $result[$i], $asset_id);
    if ( count($asset_id) > 0 ) {
      $asset_id = str_replace(' id=', '', $asset_id[0]);

      $a[$i] = sprintf("<div class='nb-container-fluid' ><div class='nb-row nb-prices' id='%s_prices'></div></div>", $asset_id);

      $a[$i] .= "<script>";
      $a[$i] .= "jQuery(document).ready(function($){";
      $a[$i] .= sprintf("$.getJSON('%s/asset/%s/show-prices', function (info) {", $api, $asset_id);
      $a[$i] .= "if ( info.data ) { "; 
      $a[$i] .= sprintf("$('#%s_prices').html( info.data ); }}) ", $asset_id);
      $a[$i] .= sprintf(".fail( function() { $('#%s_prices').html('Error while load prices about asset ID= %s.'); } );", $asset_id, $asset_id);
      $a[$i] .= "});"; // jQuery
      $a[$i] .= "</script>";


    }
  }

  if ( ! empty ($a) ) {
    $content = str_replace($result, $a, $content);
  }

  return $content;
}

/*****************************************************************************************************/
function nxtbridge_top50( $content ) {
  global $api;
  $pattern = '/\[NXTBridgeTop50]/';
  preg_match_all($pattern, $content, $result); //find all Asset patterns
  $result = array_unique($result[0]); // remove duplicates

  $a[0] = "<div class='nb-container-fluid' id='nb-top50-assets'></div>";
  $a[0] .= "<script>";
  $a[0] .= "jQuery(document).ready(function($){";
  $a[0] .= sprintf("$.getJSON('%s/asset/top', function (info) {", $api);
  $a[0] .= "if ( info.data ) { "; 
  $a[0] .= sprintf("$('#nb-top50-assets').html( info.data ); }}) ");
  $a[0] .= sprintf(".fail( function() { $('#nb-top50-assets').html('Error while load TOP20.'); } );");
  $a[0] .= "});"; // jQuery
  $a[0] .= "</script>";

  $content = str_replace($result, $a, $content);
  return $content;
}



?>
