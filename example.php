<?php

/*
 *
 *  Copyright (C) 2016 Libelium Comunicaciones Distribuidas S.L.
 *  http://www.libelium.com
 *
 *  This program is distributed WITHOUT ANY WARRANTY; without
 *  even the implied warranty of MERCHANTABILITY or FITNESS FOR A
 *  PARTICULAR PURPOSE.
 *
 *  By using it you accept the MySignals Terms and Conditions.
 *  You can find them at: http://libelium.com/legal
 *
 *
 *  Version:           0.1
 *  Design:            David Gascon
 */

include('includes/httpful.phar');

// Config
$email = 'metropolia.mysignals@gmail.com';
$password = 'myS1gnalsMP!';

// API Vars
$api_base = 'https://api.libelium.com/mysignals';
$api_headers = ['Accept' => 'application/x.webapi.v1+json'];


//1.- Login
$parameters = json_encode([
    'email' => $email,
    'password' => $password
]);
$response_login = \Httpful\Request::post($api_base . '/auth/login')
    ->sendsJson()
    ->body($parameters)
    ->addHeaders($api_headers)
    ->send();
echo "1.- Login: <br><br>".$response_login->raw_body."<hr><br>";

//Save the Token in the header array.
if($response_login->code == 200){
    $api_headers['Authorization'] = 'Bearer '.$response_login->body->token;
}


//2.- Get my members
$response_members = \Httpful\Request::get($api_base . '/members')
    ->addHeaders($api_headers)
    ->send();

echo "2.- Get my members: <br><br><pre>".json_encode($response_members->body, JSON_PRETTY_PRINT)."</pre><hr><br>";

// 2.1 get the dates when the user X was using the box


//3.- Get values from the first of my members
if(count($response_members->body->data) >= 1){
    $member_id = $response_members->body->data[1]->id;

    $parameters = [
        'member_id' => $member_id,
        'sensor_id' => 'blood_dias',
        'ts_start' => '2015-01-01 00:00:00',
        'ts_end' => '2018-01-01 13:10:00',
        'limit' => '30',
        'cursor' => '0',
        'order' => 'desc'
    ];
    $response_values = \Httpful\Request::get($api_base . '/values?'.http_build_query($parameters))
        ->addHeaders($api_headers)
        ->send();

    echo "3.- Get values from one member (member_id= '156'): <br><br><pre>".json_encode($response_values->body, JSON_PRETTY_PRINT)."</pre><hr><br>";

}	