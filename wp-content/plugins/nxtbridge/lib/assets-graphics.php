<?php //*********

/*****************************************************************************************************/
function nxtbridge_stockchart( $content ) {
  global $api;
  $id = '/ id=[0-9]*/';
  $pattern = '/\[NXTBridgeAssetStock .*\]/';
  preg_match_all($pattern, $content, $result); //find all Asset patterns
  $result = array_unique($result[0]); // remove duplicates

  for ( $i=0; $i<count($result); $i++ ) {
    preg_match($id, $result[$i], $asset_id);
    if ( count($asset_id) > 0 ) {
      $asset_id = str_replace(' id=', '', $asset_id[0]);

      $a[$i] = "";
      $a[$i] .= sprintf("<div class='nb-container-fluid nb-stockchart' ><div class='nb-row' id='%s_stockchart'></div></div>", $asset_id );
      $a[$i] .= sprintf("<script>");
      $a[$i] .= "jQuery(document).ready(function($){";
      $a[$i] .= sprintf("$.getJSON('%s/asset/%s/get-trade-price', function (info) {", $api, $asset_id);
      $a[$i] .= sprintf("if ( info.data ) { data = info.data; "); 
      $a[$i] .= sprintf("$('#%s_stockchart').highcharts('StockChart', {", $asset_id);
      $a[$i] .= sprintf("rangeSelector: {selected: 0, allButtonsEnabled: true, buttons: [ { type: 'month', count: 1, text: 'Day', dataGrouping: { forced: true, units: [['day', [1]]]}}, { type: 'year', count: 1, text: 'Week', dataGrouping: { forced: true, units: [['week', [1]]]}},{ type: 'all', count: 1, text: 'Month', dataGrouping: { forced: true, units: [['month', [1]]]}}  ]},title: {text: 'Asset price'}, series: [{name: 'NXT', data: data, tooltip: {valueDecimals: 4} }]");
      $a[$i] .= sprintf("});"); // highchart
      $a[$i] .= sprintf("}");  // if (info.data)
      $a[$i] .= sprintf("})");  
      $a[$i] .= sprintf(".fail( function() { $('#%s_stockchart').html('Error while load StockChart for asset ID= %s.'); } );", $asset_id, $asset_id);
      $a[$i] .= "});"; // jQuery
      $a[$i] .= sprintf("</script>");
    }
  }

  if ( ! empty ($a) ) {
    $content = str_replace($result, $a, $content);
  }

  return $content;
}

/*****************************************************************************************************/
function nxtbridge_candlestick( $content ) {
  global $api;
  $id = '/ id=[0-9]*/';
  $pattern = '/\[NXTBridgeAssetCandlestick .*\]/';
  preg_match_all($pattern, $content, $result); //find all Asset patterns
  $result = array_unique($result[0]); // remove duplicates
  $a = array();

  for ( $i=0; $i<count($result); $i++ ) {
    preg_match($id, $result[$i], $asset_id);
    if ( count($asset_id) > 0 ) {
      $asset_id = str_replace(' id=', '', $asset_id[0]);

      $a[$i] = "";
      $a[$i] .= sprintf("<div class='nb-container-fluid nb-candlestick'><div class='nb-row' id='%s_candlestick'></div></div>", $asset_id );

      $a[$i] .= sprintf("<script>");
      $a[$i] .= "jQuery(document).ready(function($){";

        $a[$i] .= sprintf("$.getJSON('%s/asset/%s/get-candlestick-and-volume', function (info) {", $api, $asset_id);
        $a[$i] .= sprintf("if ( info.data ) { data = info.data; "); 

        $a[$i] .= sprintf("var ohlc = [], volume = [], dataLength = data.length, i=0;");
        $a[$i] .= sprintf("var groupingUnits = [[ 'week', [1] ], [ 'month', [1,2,3,6] ]];");  
        $a[$i] .= sprintf("for (i;i<dataLength; i++) {");
        $a[$i] .= sprintf("ohlc.push([ data[i][0], data[i][1], data[i][2], data[i][3], data[i][4] ]);");
        $a[$i] .= sprintf("volume.push([ data[i][0], data[i][5] ]);");
        $a[$i] .= sprintf("}");

        $a[$i] .= sprintf("Highcharts.stockChart('%s_candlestick', {", $asset_id);
        $a[$i] .= sprintf("rangeSelector: { buttons: [{ type: 'week', count: 1, text: '1W'}, { type: 'month', count: 1, text: '1M'}, { type: 'all', count: 1, text: 'All'} ], selected: 1, inputEnabled: false }, title: {text: 'Asset prices and volumes'}, \n");
        $a[$i] .= sprintf("yAxis: [\n");  
        $a[$i] .= sprintf("{ labels: { align: 'right', x: -3}, title: { text: 'OHLC'   }, height: '60%%', offset: 0, lineWidth: 2 }, \n");  
        $a[$i] .= sprintf("{ labels: { align: 'right', x: -3}, title: { text: 'Volume' }, top: '65%%', height: '35%%', offset: 0, lineWidth: 2 } \n");  
        $a[$i] .= sprintf("], \n");  
        $a[$i] .= sprintf("series: [{type: 'candlestick', name: 'NXT', data: ohlc, dataGrouping: { units: groupingUnits } }, {type: 'column', name: 'Volume', data: volume, yAxis: 1, dataGrouping: { units: groupingUnits } } ]");  
        $a[$i] .= sprintf("});"); // highchart
        $a[$i] .= sprintf("}");  // if (info.data)
        $a[$i] .= sprintf("})"); // getJSON 
        $a[$i] .= sprintf(".fail( function() { $('#%s_candlestick').html('Error while load StockChart for asset ID= %s.'); } );", $asset_id, $asset_id);

      $a[$i] .= "});"; // jQuery
      $a[$i] .= sprintf("</script>");
    }
  }

  $content = str_replace($result, $a, $content);
  return $content;
}




?>
