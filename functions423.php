<?php


function strip_all($in) {
  $in = trim($in);
  $in = stripslashes($in);
  $in = htmlspecialchars($in);
  return $in;
}

function validate_tnam1($in) {
  strip_all($in);	
  if (strlen($in) <> 45) {return false;}	
  $reg_tnam1 = '/tnam1[a-zA-Z0-9]{40}/i';	
  return preg_match($reg_tnam1, $in);
}





?>
