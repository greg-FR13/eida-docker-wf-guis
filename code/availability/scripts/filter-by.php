<?php

  /* PHP Script that returns
   * ORFEUS Data Center networks, station, locations and channels
   */

  $DBUSER = "*";
  $DBPASS = "*";
  $DBNAME = "*";
  $DBHOST = "*";

  $ARCHIVE = "ODC";

  function getNetworkQuery() {

    global $ARCHIVE;

    return "
      SELECT
        code
      FROM
        Network
      WHERE
        archive = '$ARCHIVE'
    ";

  }

  function getStationQuery() {

    global $ARCHIVE;

    return "
      SELECT DISTINCT
        Station.code
      FROM
        Station
      LEFT JOIN
        Network
      ON
        Station._parent_oid = Network._oid
      WHERE
        Network.code = ?
      AND
        Station.archive = '$ARCHIVE'
    ";

  }

  function getLocationQuery() {

    global $ARCHIVE;

    return "
      SELECT DISTINCT
        SensorLocation.code
      FROM
        SensorLocation
      LEFT JOIN
        Station
      ON
        SensorLocation._parent_oid = Station._oid
      LEFT JOIN
        Network
      ON
        Station._parent_oid = Network._oid
      WHERE
        Network.code = ?
      AND
        Station.code = ?
      AND
        Station.archive = '$ARCHIVE'
    ";

  }

  function getChannelQuery() {

    global $ARCHIVE;

    return "
      SELECT DISTINCT
        Stream.code
      FROM
        Stream
      LEFT JOIN
        SensorLocation
      ON
        Stream._parent_oid = SensorLocation._oid
      LEFT JOIN
        Station
      ON
        SensorLocation._parent_oid = Station._oid
      LEFT JOIN
        Network
      ON
        Station._parent_oid = Network._oid
      WHERE
        Network.code = ?
      AND
        Station.code = ?
      AND
        SensorLocation.code = ?
      AND
        Station.archive = '$ARCHIVE'
    ";

  }
  
  # Open connection
  $mysqli = new mysqli(
    $DBHOST,
    $DBUSER,
    $DBPASS,
    $DBNAME
  );
  
  # Failed
  if(mysqli_connect_error()) {
    return header("HTTP/1.1 500 Internal Server Error");
  }

  # All three are set
  if(isset($_GET["network"]) && isset($_GET["station"]) && isset($_GET["location"])) {

    $query = getChannelQuery();

    if(!($stmt = $mysqli->prepare($query))) {
      return header("HTTP/1.1 500 Internal Server Error");
    }

    if(!$stmt->bind_param("sss", $_GET["network"], $_GET["station"], $_GET["location"])) {
      return header("HTTP/1.1 500 Internal Server Error");
    }

  } elseif(isset($_GET["network"]) && isset($_GET["station"])) {

    $query = getLocationQuery();

    if(!($stmt = $mysqli->prepare($query))) {
      return header("HTTP/1.1 500 Internal Server Error");
    }

    if(!$stmt->bind_param("ss", $_GET["network"], $_GET["station"])) {
      return header("HTTP/1.1 500 Internal Server Error");
    }

  } elseif(isset($_GET["network"])) {

    $query = getStationQuery();

    if(!($stmt = $mysqli->prepare($query))) {
      return header("HTTP/1.1 500 Internal Server Error");
    }

    if(!$stmt->bind_param("s", $_GET["network"])) {
      return header("HTTP/1.1 500 Internal Server Error");
    }

  } else {

    $query = getNetworkQuery();

    if(!($stmt = $mysqli->prepare($query))) {
      return header("HTTP/1.1 500 Internal Server Error");
    }

  }

  $stmt->execute();

  $stmt->bind_result($value);

  while($stmt->fetch()) {

    if($value == "") {
      $value = "--";
    }

    $rows[] = $value;
  }

  if(count($rows) == 0) {
    return header("HTTP/1.1 204 No Content");
  }

  header("Content-Type: application/json");

  echo json_encode($rows);

?>
