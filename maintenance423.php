<?php 

$maintenance = "";

$query_m = "WITH cte AS (SELECT MAX(header_height) AS last_block FROM $schema.blocks) SELECT header_height || ' (' || TO_CHAR(header_time::timestamp, 'DD-MM-YYYY HH24:MI:SS') || ' UTC)' AS latest_block FROM $schema.blocks WHERE header_height = (SELECT last_block FROM cte)";

$results_m = pg_query($dbconn, $query_m)
	or die('could not fetch latest indexed block number');

$latest_indexed_block = pg_fetch_array($results_m, null, PGSQL_NUM)[0];

$maintenance = $maintenance . " The tools are synced up to block height $latest_indexed_block. Please check this corresponds to latest block height.";

pg_free_result($results_m);

?>
