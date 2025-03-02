<?php


function strip_all($in) {
  $in = trim($in);
  $in = stripslashes($in);
  $in = htmlspecialchars($in);
  return $in;
}

function validate_tnam1($in) {
  if (empty($in)) {
    return false;
  }    
 $in = strip_all($in);	
  if (strlen($in) <> 45) {return false;}	
  $reg_tnam1 = '/tnam1[a-zA-Z0-9]{40}$/i';	
  return preg_match($reg_tnam1, $in);
}

function validate_proposal_id($in) {
  if (empty($in)) {
    return false;
  }    
  $in = strip_all($in);
  if (strlen($in) > 25) {return false;}
  $reg_tnam1 = '/\d+$/i';
  return preg_match($reg_tnam1, $in);
}


function hyperlink_address($in, $mode_arg) {
   $in = strip_all($in);	
   $mode_arg = strip_all($mode_arg);
   if (validate_tnam1($in) == false) {die('wrong internal parameter call');}
  $uri = strip_all($_SERVER['REQUEST_URI']);
 $uri = array_shift(explode('?', $uri)); 
  $servername = strip_all($_SERVER['HTTP_HOST']);
  $newlink = 'https://' . $servername . $uri . '?address=' . $in . '&mode=' . $mode_arg . '&proposal_id=';
  $returnstring = '<a href="' . $newlink . '">' . $in . '</a>'; 
  return $returnstring;
}

function hyperlink_proposal($in, $mode_arg) {
  $uri = strip_all($_SERVER['REQUEST_URI']);
 $uri = array_shift(explode('?', $uri));
  $servername = strip_all($_SERVER['HTTP_HOST']);
  $newlink = 'https://' . $servername . $uri . '?proposal_id=' . $in;
  $returnstring = '<a href="' . $newlink . '">' . $in . '</a>';
  return $returnstring;
}

function print_query_table($in_query, $in_dbconn) {
  $results = pg_query($in_dbconn, $in_query) or die('could not fetch results');
echo("<table>");

if (!empty($table_headers)) {
  foreach ($table_headers as $x) {
     echo "<th>$x</th>";
  }
} else {
  $i = 0;
  while ($i < pg_num_fields($results)) {
    echo('<th>' . pg_field_name($results, $i) . '</th>');
  $i = $i + 1;
  }
}

while ($line = pg_fetch_array($results, null, PGSQL_ASSOC)) {

echo("\t<tr>\n");
foreach ($line as $col_value) {
   echo("\t\t<td>");
    echo($col_value);
   echo("</td>\n");
  }
  echo "\t</tr>\n";
}
echo "</table>\n";

pg_free_result($results);


}




?>
