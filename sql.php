<?php
class SQL {
  function __construct() {
    $link = pg_connect('host=localhost port=5433 user=orca dbname=orca password=orcaorca');
    
    if (!$link) {
        die('接続失敗です。'.pg_last_error());
    }
  }
  
  function query($query, $parameters, $class = 'stdClass') {
    $result = pg_query_params($query, $parameters);
    $response = [];

    while ($data = pg_fetch_object($result, NULL, $class)) {
      $response[] = $data;
    }
    
    return $response;
  }

  function queryNoArgument($query) {
    $result = pg_query($query);
    $response = [];
      
    while ($data = pg_fetch_object($result)) {
      $response[] = $data;
    }
    
    return $response;
  }
}
?>
