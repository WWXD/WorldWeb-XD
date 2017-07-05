<?php
if (!defined('BLARG')) die();
// Catchall page, for legacy blargboard pages

// Set the page and id, as REQUEST
$_REQUEST['page'] = $pageParams['page'];

if (isset($pageParams['id']))
    $_REQUEST['id'] = $pageParams['id'];

// Set the page, in both POST and GET when required
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_POST['page'] = $pageParams['page'];

    if (isset($pageParams['id']))
        $_POST['id'] = $pageParams['id'];
} else {
    $_GET['page'] = $pageParams['page'];

    if (isset($pageParams['id']))
        $_GET['id'] = $pageParams['id'];
}


require_once(__DIR__ . '/' . $pageParams['page'] . '.php');