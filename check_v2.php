<?php include "includes423.php"; ?>
<?php

$address=strip_all($_GET["address"]);
$mode=strip_all($_GET["mode"]);
$proposal_id=strip_all($_GET["proposal_id"]);
$run_mode = $mode;

//validate all inputs and reset the sht if anyone has tried passing badness
if ((!empty($address) and !validate_tnam1($address)) or (!empty($proposal_id) and !validate_proposal_id($proposal_id))) {
	$run_mode = 'default';
	$address = '';
	$proposal_id = '';
	$error = 'input valid proposal id or tnam1!';
} elseif ($mode !== 'missing' and $mode !== 'cast' and $mode !== 'cast_simple' and $mode !== 'proposal' and $mode !== 'list_proposals') {
	$run_mode = 'default';
	$address = '';
	$proposal_id = '';
	if (!empty($mode)) {
		$error = 'don\'t play around with the parameters too much!';
	}
} elseif ((($mode === 'missing' or $mode === 'cast' or $mode === 'cast_simple')and empty($address)) or ($mode === 'proposal' and empty($proposal_id))) {
	$run_mode = 'default';
	$error = 'don\'t play around with the parameters too much!';
}


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

<?php
if (!empty($maintenance)) {
  echo("<div>Maintenance note: $maintenance</div><br><br>");
}
?>

<div>READ CAREFULLY:<br><br>

This tool lets you check proposals that have gone into voting (more precisely that someone has already voted on) by address, to let you see either which proposals you have voted (complete) or which proposals anyone else has voted on that you have not voted on yourself. Note that this will typically not include proposals for future epochs -  These are unfortunate limitations of the data model we employ. You will also be able to see all votes for a particular proposal, and draw a list of all proposals (currently a little bland).<br><br>

We will be adding functions continously. Join our <a href="https://github.com/opsecx/Simple-Votes-Checker" target="_blank">github</a> for helping out!<br><br>

<?php 

$filename = strip_all(basename($_SERVER['PHP_SELF'])); 



if (!empty($error)) {
  echo("<div style=\"color:red\">$error</div><br>");
}

?>



<form action="<?php echo($filename);?>" method="get">
	Address (tnam):<input type="text" name="address" size="41" value="<?php echo($address);?>"><br><br>
	<button type="submit" name="mode" value="missing">Votes Missing</button> 
	<button type="submit" name="mode" value="cast_simple">Votes Cast Simple</button>
<button type="submit" name="mode" value="cast">Votes Cast Complete</button><br><br>
Proposal Id:<input type="text" name="proposal_id" size ="11"><br><br>
<button type="submit" name="mode" value="proposal">Proposal Votes</button>
<button type="submit" name="mode" value="list_proposals">List all proposals (WIP)</button>
</form>

<?php
//assume $dbconn opened by includes

if ($run_mode == 'cast') {
  $query = "SELECT ('x' || right(vote_proposal_id::bytea::text, 6))::bit(24)::int AS proposal_id, vote, voter, commit_height, header_time, tx_id FROM shielded_expedition.vote_proposal LEFT JOIN shielded_expedition.transactions ON tx_id = hash LEFT JOIN shielded_expedition.blocks ON transactions.block_id = blocks.block_id WHERE voter = '$address' ORDER BY 1 ASC";
} elseif ($run_mode == 'cast_simple') {
  $query = "SELECT DISTINCT ('x' || right(vote_proposal_id::bytea::text, 6))::bit(24)::int AS proposal_id,vote,voter FROM shielded_expedition.vote_proposal WHERE voter = '$address' ORDER BY 1 ASC";
} elseif ($run_mode == 'missing') {
  $query = "SELECT DISTINCT ('x' || right(vote_proposal_id::bytea::text, 6))::bit(24)::int AS proposal_id FROM shielded_expedition.vote_proposal WHERE vote_proposal_id NOT IN (SELECT DISTINCT vote_proposal_id FROM shielded_expedition.vote_proposal WHERE voter = '$address') ORDER BY 1 ASC"; 
} elseif ($run_mode == 'proposal') {
  $query = "SELECT ('x' || right(vote_proposal_id::bytea::text, 6))::bit(24)::int AS proposal_id, vote, voter, commit_height, header_time, tx_id FROM shielded_expedition.vote_proposal LEFT JOIN shielded_expedition.transactions ON tx_id = hash LEFT JOIN shielded_expedition.blocks ON transactions.block_id = blocks.block_id WHERE ('x' || right(vote_proposal_id::bytea::text, 6))::bit(24)::int = $proposal_id ORDER BY 1 ASC";
} elseif ($run_mode == 'list_proposals') {
  $query = "SELECT DISTINCT ('x' || right(vote_proposal_id::bytea::text, 6))::bit(24)::int AS proposal_id FROM shielded_expedition.vote_proposal ORDER BY 1 ASC";
}

if ($run_mode == 'cast' or $run_mode == 'missing' or $run_mode == 'proposal' or $run_mode == 'list_proposals' or $run_mode == 'cast_simple') {

$results = pg_query($dbconn, $query)
 or die('could not fetch results');

echo "<table>\n";

if ($run_mode == 'missing') {
	echo('<th>proposal-id</th>');
} elseif ($run_mode == 'cast') {
	echo('<th>proposal-id</th><th>vote cast</th><th>address</th><th>block</th><th>date-time</th><th>transaction id</th>');
} elseif ($run_mode == 'cast_simple') {
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
