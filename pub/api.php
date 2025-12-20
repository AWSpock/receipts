<?php

include_once(__DIR__ . "/../php/utilities/Common.php");
include_once(__DIR__ . "/../php/utilities/RouteParser.php");

include_once(__DIR__ . "/../php/DataAccess/UserAuth.php");
include_once(__DIR__ . "/../php/DataAccess/DataAccess.php");

$userAuth = new UserAuth();
$routeParser = new RouteParser("api");
$data = new DataAccess();
session_start();

switch ($routeParser->ResourcePath()) {
    case "/public":
        break;
    default:
        if (!$userAuth->checkToken() || !$userAuth->checkUtility()) {
            http_response_code(401);
            echo "Unauthorized";
            die();
        }

        $uriparts = explode("/", $_SERVER['REQUEST_URI']);
        if (array_key_exists(3, $uriparts)) {
            $account_id = explode("/", $_SERVER['REQUEST_URI'])[3];
            $store_id = explode("/", $_SERVER['REQUEST_URI'])[3];
        }
        if (array_key_exists(5, $uriparts))
            $receipt_id = explode("/", $_SERVER['REQUEST_URI'])[5];
        break;
}

if (file_exists($routeParser->PagePath())) {
    try {
        header('Content-Type: application/json; charset=utf-8');
        require $routeParser->PagePath();
    } catch (DatabaseException $ex) {
        http_response_code(500);
        error_log("Database Error: " . $ex->getMessage(), 0);
        echo "Database Error: " . $ex->getMessage();
        die();
    } catch (Exception $ex) {
        http_response_code(500);
        error_log("Other Error: " . $ex->getMessage(), 0);
        echo "Other Error: " . $ex->getMessage();
        die();
    } catch (Throwable $ex) {
        http_response_code(500);
        error_log("Major Error: " . $ex->getMessage(), 0);
        echo "Major Error: " . $ex->getMessage();
        die();
    }
} else {
    http_response_code(404);
    echo "Not Found";
}
