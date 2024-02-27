
<?php include "includes423.php"; ?>
<?php

$address=strip_all($_GET["address"]);
$mode=strip_all($_GET["mode"]);

?>

<html>

<head>

<style>

table, th, td {
border: 1px solid;

}


</style>

<title>Votes Checker (Limited)</title>

</head>

<body>

<h2>Votes Checker (Limited)</h2>

<div>READ CAREFULLY:<br><br>

This tool lets you check proposals that have gone into voting (more precisely that someone has already voted on) by address, to let you see either which proposals you have voted (complete) or which proposals anyone else has voted on that you have not voted on yourself. (note that this will typically not include proposals for future epochs). These are unfortunate limitations of the data model we employ.<br><br>

We may also add some stats for the proposals in the database.<br><br>

<?php 

$filename = strip_all(basename($_SERVER['PHP_SELF'])); 

if ((empty($address) and empty($mode)) or validate_tnam1($address) == false) {
	$run_mode = 'default'; } 
else {
	$run_mode = $mode;
}


if (empty($address) == false and validate_tnam1($address) == false) {
	echo('<div style="color:red">bad format, enter valid tnam1!</div><br>');
}

?>



<form action="<?php echo($filename);?>" method="get">
	Address (tnam):<input type="text" name="address" size="41" value="<?php echo($address);?>"><br><br>
	<button type="submit" name="mode" value="missing">Votes Missing</button> 
	<button type="submit" name="mode" value="cast">Votes Cast</button>
</form>

<?php
//assume $dbconn opened by includes

if ($run_mode == 'cast') {
  $query = "SELECT DISTINCT ('x' || right(vote_proposal_id::bytea::text, 6))::bit(24)::int AS proposal_id,vote,voter FROM shielded_expedition.vote_proposal WHERE voter = '$address' ORDER BY 1 ASC";
} elseif ($run_mode == 'missing') {
  $query = "SELECT DISTINCT ('x' || right(vote_proposal_id::bytea::text, 6))::bit(24)::int AS proposal_id FROM shielded_expedition.vote_proposal WHERE vote_proposal_id NOT IN (SELECT DISTINCT vote_proposal_id FROM shielded_expedition.vote_proposal WHERE voter = '$address') ORDER BY 1 ASC"; 

}

if ($run_mode <> 'default') {

$results = pg_query($dbconn, $query)
 or die('could not fetch results');

echo "<table>\n";

if ($run_mode == 'missing') {
	echo('<th>proposal-id</th>');
} elseif ($run_mode == 'cast') {
	echo('<th>proposal-id</th><th>vote cast</th><th>address</th>');
}


while ($line = pg_fetch_array($results, null, PGSQL_ASSOC)) {
    echo "\t<tr>\n";
    foreach ($line as $col_value) {
        echo "\t\t<td>$col_value</td>\n";
    }
    echo "\t</tr>\n";
}
echo "</table>\n";

pg_free_result($results);
pg_close($dbconn);

//echo("test");

//echo($dbconn);

//phpinfo();

}




?>


</body>

</html>
