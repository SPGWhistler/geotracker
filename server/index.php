<?php
$output = array();
if (isset($_GET['uuid'])) {
	$mongo = new MongoClient();
	$db = $mongo->locations;
	$collection = new MongoCollection($db, 'locations');
	if (isset($_GET['lat']) && isset($_GET['lng'])) {
		$collection->update(
			array('uuid' => $_GET['uuid']),
			array(
				'uuid' => $_GET['uuid'],
				'lat' => $_GET['lat'],
				'lng' => $_GET['lng'],
				'ts' => time()
			),
			array('upsert' => TRUE)
		);
		$output = array("success" => TRUE);
	} else {
		$query = array('uuid' => $_GET['uuid']);
		$cursor = $collection->find($query);
		$obj = $cursor->getNext();
		if (isset($obj)) {
			$output = array(
				'lat' => $obj['lat'],
				'lng' => $obj['lng'],
				'ts' => $obj['ts'],
			);
		}
	}
}
echo json_encode($output);
?>
