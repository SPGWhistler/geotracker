<?php
$output = array();
if (isset($_GET['uuid'])) {
	if (isset($_GET['lat']) && isset($_GET['lng'])) {
		if (stripos($_GET['uuid'], 'emu') !== FALSE) {
			file_put_contents('tmp', $_GET['lat'] . "," . $_GET['lng']);
		}
		$output = array("success" => TRUE);
	} else {
		$data = file_get_contents('tmp');
		$output = array(
			'lat' => substr($data, 0, strpos($data, ",")),
			'lng' => substr($data, strpos($data, ",") + 1),
		);
	}
}
echo json_encode($output);
?>
