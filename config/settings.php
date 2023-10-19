<?php
$result = [];
// $result["APP_PORT"] = 6100;
$result["SERVER_NAME"] = "dev";
if ($_SERVER['HTTP_HOST'] == "localhost:6100" || env('APP_ENV')=="local"){ //เครื่อง Programmer
    $result["SERVER_NAME"] = "dev";
}else if ($_SERVER['HTTP_HOST'] == "localhost:8070" || $_SERVER['HTTP_HOST'] == "10.1.112.113:8070" || env('APP_ENV')!="local"){   
    $result["SERVER_NAME"] = "pro";
}

return $result;
?>