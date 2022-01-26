<?php
include "include.php";

$devToken = 'YOUR DEVELOPER TOKEN';
$printLog = false; // show request & response logs or no

// create new client
$id = OneBoxID_Client::Init($devToken, $printLog);

// get client
$id = OneBoxID_Client::Get();

// auth: sign in or sign up
// method will send verify code to email
try {
    $result = $id->auth(
        'phone',
        '380504479530',
    );
    print_r($result);
} catch (OneBoxID_Exception $exception) {
    print $exception;
}

try {
    $result = $id->auth(
        'phone',
        '380504479530',
        'zalupka1' // email-code OR sms-code OR password
    );
    print_r($result);
} catch (OneBoxID_Exception $exception) {
    print $exception;
}

// get information about currency user (by token)
try {
    $result = $id->userinfo(
        '391ccae746a99b925eb013222499f366'
    );
    print_r($result);
} catch (OneBoxID_Exception $exception) {
    print $exception;
}

// logout / sign out
try {
    $result = $id->logout(
        '391ccae746a99b925eb013222499f366'
    );
    print_r($result);
} catch (OneBoxID_Exception $exception) {
    print $exception;
}

// update profile (change some fields)
// leave field empty if you do not wan't to change it
try {
    $result = $id->update(
        '391ccae746a99b925eb013222499f366',
        'logintype',
        'email',
        'phone',
        'password',
        'first name',
        'last name',
        'middle name',
        'company name'
    );
    print_r($result);
} catch (OneBoxID_Exception $exception) {
    print $exception;
}

// verify and accept changes after update() method
try {
    $result = $id->verify(
        'phone',
        '380504479530',
        'smscode'
    );
    print_r($result);
} catch (OneBoxID_Exception $exception) {
    print $exception;
}
