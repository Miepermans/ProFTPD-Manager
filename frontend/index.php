<?php

include_once "head.html";
include "../include/dbinfo.inc.php";
include "../include/config.inc.php";

mysql_connect($aDBInfo['address'],$aDBInfo['username'],$aDBInfo['password']) or die(mysql_error());
mysql_select_db($aDBInfo['database']);

function rand_string( $length ) {
  $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
  return substr(str_shuffle($chars),0,$length);
}

if(isset($_POST['username'])) {
  if($_POST['username'] == "username") {
  } else {
    $username = $_POST['username'];
  }
}

if(isset($_POST['password'])) {
  $password = $_POST['password'];
}

if(isset($username)) {
  $homedir = $config['path'].$username;
}

$uid = $config['uid'];
$gid = $config['gid'];
$shell = $config['shell'];

if(isset($username) && isset($password)) {
  mysql_query("INSERT INTO `ftpuser` (`userid`,`passwd`,`uid`,`gid`,`homedir`,`shell`) VALUES('$username','$password','$uid','$gid','$homedir','$shell')")
  or die(mysql_error());
}

if(isset($_POST['id'])) {
  foreach($_POST['id'] as $id) {
    mysql_query("UPDATE `ftpuser` SET `deleted` = '1' WHERE `id` = '$id'")
    or die(mysql_error());
  }
}
echo '


    <div class="container">
      <h1>Create FTP accounts</h1>
      <p>
        <form action "/frontend/index.php" method="post">
        <table class="table table-condensed">
          <thead>
            <tr>
              <th>Username</th>
              <th>Password</th>
              <th>Home Directory</th>
              <th>delete</th>
            </tr>
          </thead>
          <tbody>';

        $query_fillusers = "SELECT id, userid, passwd, homedir, deleted FROM ftpuser";
        $query_hosts = mysql_query($query_fillusers)
        or die(mysql_error());

        while ($row = mysql_fetch_row($query_hosts)){

          if ($row[4] == "1") {
          } else {
            echo "<tr>";
            echo "<td>"; echo $row[1]; echo "</td>";
            echo "<td>"; echo $row[2]; echo "</td>";
            echo "<td>"; echo $row[3]; echo "</td>";
            echo "<td><label class=checkbox><input name=id[] value=$row[0] type=checkbox></label</td>";
            echo "</tr>";
          }
        }    


        echo '
          <tr>
          <td><input name=username type=text value=username></input></td>
          <td><input name=password type=text value='; echo rand_string(8); echo'></input></td>
          </tr>
        <tbody>
        </table>
      <input type="submit">
      </form>
      </p>

    </div> <!-- /container -->
';
 
 include_once "footer.html";
 ?>