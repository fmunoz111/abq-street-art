<?php
require_once dirname(__DIR__, 3) . "/vendor/autoload.php";
require_once dirname(__DIR__, 3) . "/php/classes/autoload.php";
require_once("/etc/apache2/capstone-mysql/encrypted-config.php");
require_once dirname(__DIR__, 3) . "/php/lib/xsrf.php";
require_once dirname(__DIR__, 3) . "/php/lib/uuid.php";
require_once dirname(__DIR__, 3) . "/php/lib/jwt.php";

use Edu\Cnm\AbqStreetArt\{
	Art
};
/**
 * * api for Art class
 *
 * @author Samantha Andrews samantharaeandrews@gmail.com and Abq Street Art
 **/
//verify the session, start if not active
if(session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}
//prepare an empty reply
$reply = new stdClass();
$reply->status = 200;
$reply->data = null;
try {
	//grab the mySQL connection
	$pdo = connectToEncryptedMySQL("/etc/apache2/capstone-mysql/streetart.ini");
	//determine which HTTP method was used
	$method = array_key_exists("HTTP_X_HTTP_METHOD", $_SERVER) ? $_SERVER["HTTP_X_HTTP_METHOD"] : $_SERVER["REQUEST_METHOD"];
	//stores the Primary Key ($crimeId) for the GET method in $id. This key will come in the URL sent by the front end. If no key is present, $id will remain empty. Note that the input is filtered.
	$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
	$artAddress = filter_input(INPUT_GET, "artAddress", FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
	$artArtist = filter_input(INPUT_GET, "artArtist", FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
	$artImageUrl = filter_input(INPUT_GET, "artImageUrl", FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
	$artLat = filter_input(INPUT_GET, "artLat", FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
	$artLocation = filter_input(INPUT_GET, "artLocation", FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
	$artLong = filter_input(INPUT_GET, "artLong", FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
	$artTitle = filter_input(INPUT_GET, "artTitle", FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
	$artType = filter_input(INPUT_GET, "artType", FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
	$artYear = filter_input(INPUT_GET, "artYear", FILTER_VALIDATE_INT);
	$userDistance = filter_input(INPUT_GET, "userDistance", FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
	// handle GET request - if id is present, that crime is returned, otherwise all crimes are returned
	if($method === "GET") {
		//set XSRF cookie
		setXsrfCookie();
		//get a specific crime or all crimes and update reply
		if(empty($id) === false) {
			$art = Art::getArtByArtId($pdo, $id);
			if($art !== null) {
				$reply->data = $art;
			}
		} else if(empty($crimeLocation) === false) {
			$crimes = Crime::getCrimeByCrimeLocation($pdo, $crimeLocation)->toArray();
			if($crimes !== null) {
				$reply->data = $crimes;
			}
		} elseif(empty($userLocationX) === false && empty($userLocationY) === false && empty($userDistance) === false) {
			$crimes = Crime::getCrimeByCrimeGeometry($pdo, new Point($userLocationX, $userLocationY), $userDistance)->toArray();
			if($crimes !== null) {
				$reply->data = $crimes;
			}
		} else if(empty($crimeDescription) === false) {
			$crimes = Crime::getCrimeByCrimeDescription($pdo, $crimeDescription)->toArray();
			if($crimes !== null) {
				$reply->data = $crimes;
			}
		} else if(empty($crimeSunriseDate) === false && empty($crimeSunsetDate) === false) {
			$crimeSunriseDate = \DateTime::createFromFormat("U", ceil($crimeSunriseDate / 1000));
			$crimeSunsetDate = \DateTime::createFromFormat("U", floor($crimeSunsetDate / 1000));
			$crimes = Crime::getCrimeByCrimeDate($pdo, $crimeSunriseDate, $crimeSunsetDate)->toArray();
			if($crimes !== null) {
				$reply->data = $crimes;
			}
		} else {
			$crimes = Crime::getAllCrimes($pdo)->toArray();
			if($crimes !== null) {
				$reply->data = $crimes;
			}
		}
	} else {
		throw (new InvalidArgumentException("Invalid HTTP Method Request"));
		// If the method request is not GET an exception is thrown
	}
// update reply with exception information
} catch(Exception $exception) {
	$reply->status = $exception->getCode();
	$reply->message = $exception->getMessage();
	$reply->trace = $exception->getTraceAsString();
} catch(TypeError $typeError) {
	$reply->status = $typeError->getCode();
	$reply->message = $typeError->getMessage();
}
// In these lines, the Exceptions are caught and the $reply object is updated with the data from the caught exception. Note that $reply->status will be updated with the correct error code in the case of an Exception.
header("Content-type: application/json");
// sets up the response header.
if($reply->data === null) {
	unset($reply->data);
}
echo json_encode($reply);