<?php

//CHECK FOR ADMIN WP LOGIN
if (!current_user_can('manage_options')) {
	exit();
} 

global $wpdb, $mysqli;

$hash = getT4Shash();

$query = "SELECT * FROM t4s_forms";
$result = mysqli_query($mysqli, $query);

if (!isset($_GET['form'])) {

	echo "
		<style> .paymentFormT4S tr td { padding: 5px } </style>
		<h1>Tools 4 Shuls Custom Form Payments</h1>
		<table class='paymentFormT4S'>
			<tr><td><b>Form Name</b></td>
			<td><b>Submissions</b></td>
			<td><b>View</b></td>
			<td><b>Report</b></td>
			</tr>";

	$options = "";
	for ($i=0;$i<mysqli_num_rows($result);$i++) {
		$row = mysqli_fetch_assoc($result);
		$final = getPaymentRecords($row['id']);	
		echo "
		<tr>
			<td><b>".$row['name']."</b></td>
			<td style='text-align: right'><b>".count($final)."</b></td>
			<td><b><a href='".admin_url( '/admin.php?page=t4s-form-payments&form='.$row['id'])."'>[ view ]</a></b></td>
			<td><b><a href='".admin_url( 'admin-ajax.php' ).'?action=t4s_export_forms_callback&form='.$row['id'].'&export=xls&hash='.$hash."' target=_blank>[Export to Excel]</a></b></td>
		</tr>";
	}

	echo "</table>";
}

if (isset($_GET['form'])) {
			
	$eid = intval($_GET['form']);
	
	$sql2 = $wpdb->prepare("SELECT * FROM t4s_forms WHERE id='%d'", $eid);
	$result2 = mysqli_query($mysqli, $sql2);
	$row2 = mysqli_fetch_assoc($result2);
	
	$final = getPaymentRecords($eid);	
	
	displayPaymentRecords($final, 'Recorded Transations For '.$row2['name']);
	
} else {

	$recent = getPaymentRecords();	
	displayPaymentRecords($recent, 'Recent Form Submissions');
	
}

?>