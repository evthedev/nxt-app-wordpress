<?php 

/*****************************************************************************************************/

function nxtbridge_tip( $content ) { 
  global $api;
  $pattern = '/\[NXTBridgeTip .*\]/';
  preg_match_all($pattern, $content, $result); //find all Asset patterns
  $result = array_unique($result[0]); // remove duplicates

  for ( $i=0; $i<count($result); $i++ ) {
    $account = explode("=", str_replace(']', '', $result[$i]), 2);
    $acc = $account[1]; // explode nxt address
    
    $length = 10;
    $uid = substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);

    
    $a[$i] = "";
    if ( strlen($acc) == 24 ) { 
      // generate button
      $a[$i] .= sprintf("<div>");
      $a[$i] .= sprintf("<label><input class='NXTBridge-tip-field' id='Tip-%s'  type='text' value='50'>", $uid);
      $a[$i] .= sprintf("<button class='NXTBridge-tip-button' class='button' data-addr='%s' data-id='%s' href='#'>Tip Me</button></label>", $acc, $uid);
      $a[$i] .= sprintf("</div>");
      $a[$i] .= sprintf("");
      $a[$i] .= sprintf("");


    } else {
      // wrong Nxt address
      $a[$i] = "";
    }
  }

  $content = str_replace($result, $a, $content);
  return $content;

}

?>
