<?php 

$maintenance = "";

$query_m = "select max(header_height) as latest_block from shielded_expedition.blocks";

$results_m = pg_query($dbconn, $query_m)
	or die('could not fetch latest indexed block number');

$latest_indexed_block = pg_fetch_array($results_m, null, PGSQL_NUM)[0];

$maintenance = $maintenance . " The tools are synced up to block height $latest_indexed_block. Please check this corresponds to latest block height.";

pg_free_result($results_m);

?>
