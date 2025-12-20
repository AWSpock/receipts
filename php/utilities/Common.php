<?php

// function ReturnCurrentPath()
// {
//     $path = "";
//     $req = $_SERVER['REDIRECT_URL'];
//     switch ($req) {
//         case "/":
//             $path = "/index";
//             break;
//         default:
//             $path = $req;
//             break;
//     }
//     return $path;
// }

function IncludeCSS($file)
{
    if (file_exists(__DIR__ . "/../../pub" . $file)) {
        $v = filemtime(__DIR__ . "/../../pub" . $file);
        echo "<link rel='stylesheet' type='text/css' href='//receipts.spockfamily.net" . $file . "?v=" . $v . "' />\n";
    }
}
function IncludeJS($file)
{
    if (file_exists(__DIR__ . "/../../pub" . $file)) {
        $v = filemtime(__DIR__ . "/../../pub" . $file);
        echo "<script src='//receipts.spockfamily.net" . $file . "?v=" . $v . "'></script>\n";
    }
}

function ReturnUserIP()
{
    // Get real visitor IP behind CloudFlare network
    if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
        $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
    }
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = $_SERVER['REMOTE_ADDR'];

    if (filter_var($client, FILTER_VALIDATE_IP)) {
        $ip = $client;
    } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
        $ip = $forward;
    } else {
        $ip = $remote;
    }

    return $ip;
}

function FormatMoney($val)
{
    $formatter_us = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
    return $formatter_us->formatCurrency($val, 'USD') . PHP_EOL;
}

function returnUploadExceptionMessage($code)
{
    $message = "";
    switch ($code) {
        case UPLOAD_ERR_INI_SIZE:
            $message = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
            break;
        case UPLOAD_ERR_FORM_SIZE:
            $message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
            break;
        case UPLOAD_ERR_PARTIAL:
            $message = "The uploaded file was only partially uploaded";
            break;
        case UPLOAD_ERR_NO_FILE:
            $message = "No file was uploaded";
            break;
        case UPLOAD_ERR_NO_TMP_DIR:
            $message = "Missing a temporary folder";
            break;
        case UPLOAD_ERR_CANT_WRITE:
            $message = "Failed to write file to disk";
            break;
        case UPLOAD_ERR_EXTENSION:
            $message = "File upload stopped by extension";
            break;

        default:
            $message = "Unknown upload error";
            break;
    }
    return $message;
}
