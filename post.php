<?php
$url = 'http://localhost:8088/simple-service-webapp/api/users/register';
 
$url = 'http://localhost:8088/simple-service-webapp/api/users/createBucket';

$url = 'http://localhost:8088/simple-service-webapp/api/colleges/addCollege';
//Initiate cURL.
$ch = curl_init($url);
 
//The JSON data.
$jsonData = array(
        'username' => 'rat5181',
        'srispassword' => 'xx',
        'rating' => 0,
        'college' => 1,
        'role' => 1,
);

$jsonData=array(
'name'=>'ParallelSystems',
'subjectid'=>1,
'userid'=>7
);


 
$jsonData=array(
'name'=>'ParallelSystems',
'type'=>'xx',
'userid'=>7
);
 
//Encode the array into JSON.
$jsonDataEncoded = json_encode($jsonData);
 
//Tell cURL that we want to send a POST request.
curl_setopt($ch, CURLOPT_POST, 1);
 
//Attach our encoded JSON string to the POST fields.
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
 
//Set the content type to application/json
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 
 
//Execute the request
$result = curl_exec($ch)
?>
