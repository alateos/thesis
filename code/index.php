<?php
	error_reporting(1);
	include("../../../lib/db_connect.php");
	include("hit.php");
	$x = new Hit($db);
	$id = $x->registerHit(1);

?>
<script>
	function someFunction(<?php echo $id ?>) {
	
	}
</script>