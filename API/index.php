<?php

    require_once('../../scripts/dbconnect.php');
    require_once('functions.php');

    error_reporting(-1);
    ini_set('display_errors', 1);

	$command = getvar('command');

	switch ($command)
	{
        case "getBookInfo":
            $result = getBookInfo();
            break;
        case "getAuthorList":
            $result = getAuthorList();
            break;
        case "addSeries":
            $result = addSeries();
            break;
        default:
            $result = array("message"=>"Command \"$command\" not found. Service usage is ?command=name_of_command.", "success"=>false);
    }

    if ($result)
        echo json_encode($result);

?>