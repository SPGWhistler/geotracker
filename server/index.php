<?php
$output = array();
$mongo = new MongoClient();
$db = $mongo->locations;
$collection = new MongoCollection($db, 'locations');
if (isset($_GET['uuid'])) {
	if (isset($_GET['lat']) && isset($_GET['lng'])) {
		$query = array('uuid' => $_GET['uuid']);
		$cursor = $collection->find($query);
		$obj = $cursor->getNext();
		$significant = TRUE;
		if (isset($obj)) {
			$dist = distance($_GET['lat'], $_GET['lng'], $obj['lat'], $obj['lng']);
			$significant = ($dist >= 0.25) ? TRUE : FALSE;
		}
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
		$output = array(
			"success" => TRUE,
			"significant" => $significant
		);
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
} elseif (isset($_GET['all'])) {
	$cursor = $collection->find();
	$output['count'] = $cursor->count();
	foreach ($cursor as $obj) {
		$output['objects'][] = $obj;
	}
}
echo json_encode($output);
exit;

function distance($lat1, $lon1, $lat2, $lon2) {
	$theta = $lon1 - $lon2;
	$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
	$dist = acos($dist);
	$dist = rad2deg($dist);
	$miles = $dist * 60 * 1.1515;
	return $miles;
}
?>
