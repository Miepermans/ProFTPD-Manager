<?php

set_include_path("../");
include "include/dbinfo.inc.php";
include "include/config.inc.php";

mysql_connect($aDBInfo['address'],$aDBInfo['username'],$aDBInfo['password']) or die(mysql_error());
mysql_select_db($aDBInfo['database']);

function ClearDeletedUsers() {

        $query_fillusers = "SELECT id, deleted FROM ftpuser";
        $query_hosts = mysql_query($query_fillusers)
        or die(mysql_error());

        while ($row = mysql_fetch_row($query_hosts)){

          if ($row[1] == "1") {
            mysql_query("DELETE FROM `ftpuser` WHERE `id` = '$row[0]'")
            or die(mysql_error());
          } else {
          }
        }    
}

function ClearInvalidUsers() {

        $query_fillusers = "SELECT id, validtill FROM ftpuser";
        $query_hosts = mysql_query($query_fillusers)
        or die(mysql_error());

        while ($row = mysql_fetch_row($query_hosts)){
          if ($row[1] == "0000-00-00 00:00:00") {
          } else {
            mysql_query("DELETE FROM `ftpuser` WHERE `id` = '$row[0]' AND validtill < curDate() ")
            or die(mysql_error());
          }
        }
}

ClearDeletedUsers();
ClearInvalidUsers();

?>