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
  echo("<div>$maintenance</div><br><br>");
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
<button type="submit" name="mode" value="list_proposals">Proposals Stats</button>
</form>

<?php
//assume $dbconn opened by includes

if ($run_mode == 'cast') {
  $query = "SELECT id AS proposal_id, vote, voter, commit_height::numeric as block_height, TO_CHAR(header_time::timestamp, 'DD-MM-YYYY HH24:MI:SS') AS vote_time, TRIM(LEADING '\X' FROM UPPER(txid::varchar)) as tx_hash FROM $schema.tx_vote_proposal LEFT JOIN $schema.inner_transactions ON txid = hash LEFT JOIN $schema.blocks ON inner_transactions.block_id = blocks.block_id WHERE voter = '$address' ORDER BY 4 ASC";
} elseif ($run_mode == 'cast_simple') {
  $query = "SELECT DISTINCT id::numeric AS proposal_id,vote,voter FROM $schema.tx_vote_proposal WHERE voter = '$address' ORDER BY 1 ASC";
} elseif ($run_mode == 'missing') {
  $query = "SELECT DISTINCT id AS missed_proposal_id FROM $schema.tx_vote_proposal WHERE id NOT IN (SELECT DISTINCT id FROM $schema.tx_vote_proposal WHERE voter = '$address') ORDER BY 1 ASC"; 
} elseif ($run_mode == 'proposal') {
  $query = "SELECT id AS proposal_id, vote, voter, commit_height AS block_height, TO_CHAR(header_time::timestamp, 'DD-MM-YYYY HH24:MI:SS') AS vote_time, TRIM(LEADING '\X' FROM UPPER(txid::varchar)) as tx_hash FROM $schema.tx_vote_proposal LEFT JOIN $schema.inner_transactions ON txid = hash LEFT JOIN $schema.blocks ON inner_transactions.block_id = blocks.block_id WHERE id::numeric = $proposal_id ORDER BY 4 ASC";
} elseif ($run_mode == 'list_proposals') {
  $query = "SELECT id::numeric AS proposal_id, count(vote) as vote_transactions, count(distinct voter) as voters FROM $schema.tx_vote_proposal GROUP BY id ORDER BY 1 ASC";
}

if ($run_mode == 'cast' or $run_mode == 'missing' or $run_mode == 'proposal' or $run_mode == 'list_proposals' or $run_mode == 'cast_simple') {

print_query_table($query, $dbconn);

//echo("test");

//echo($dbconn);

//phpinfo();

}




?>


</body>

</html>
