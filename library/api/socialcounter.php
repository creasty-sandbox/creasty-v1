<?php
header( 'Content-type: text/plain' );

$url = $_GET[ 'url' ];
$network = $_GET[ 'network' ];

if( !isset( $id, $network ) || empty( $id ) || empty( $network ) )
	exit;


if( $network == 'gplus' ){
	$ch = curl_init();  
	curl_setopt( $ch, CURLOPT_URL, 'https://clients6.google.com/rpc?key=AIzaSyCKSbrvQasunBoV16zDH9R33D88CeLr9gQ' );
	curl_setopt( $ch, CURLOPT_POST, 1 );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, '[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"' . $url . '","source":"widget","userId":"@viewer","groupId":"@self"},"jsonrpc":"2.0","key":"p","apiVersion":"v1"}]' );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-type: application/json' ) );
	
	$results = curl_exec( $ch );
	curl_close( $ch );
	
	$results = json_decode( $results, true );
	
	echo $results[0]['result']['metadata']['globalCounts']['count'];
	
	exit;
}

?>