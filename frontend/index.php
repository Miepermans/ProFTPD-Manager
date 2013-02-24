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
    $username = htmlentities($username);
    $username = addslashes($username);
    $username = mysql_real_escape_string($username);
  }
}

if(isset($_POST['password'])) {
    $password = $_POST['password'];
    $password = htmlentities($password);
    $password = addslashes($password);
    $password = mysql_real_escape_string($password);
}

if(isset($username)) {
  $homedir = $config['path'].$username;
}

if (isset($_POST['valid'])) {
  $valid = $_POST['valid'];
} 

$uid = $config['uid'];
$gid = $config['gid'];
$shell = $config['shell'];

if(isset($username) && isset($password)) {
  if ($valid == "0") {
    mysql_query("INSERT INTO `ftpuser` (`userid`,`passwd`,`uid`,`gid`,`homedir`,`shell`,`validtill`) VALUES('$username','$password','$uid','$gid','$homedir','$shell','0000-00-00 00:00:00')")
    or die(mysql_error());
  } else {
    mysql_query("INSERT INTO `ftpuser` (`userid`,`passwd`,`uid`,`gid`,`homedir`,`shell`,`validtill`) VALUES('$username','$password','$uid','$gid','$homedir','$shell', CURRENT_DATE + INTERVAL $valid WEEK)")
    or die(mysql_error());
  }
}

if(isset($_POST['id'])) {
  foreach($_POST['id'] as $id) {
    $passrand = rand_string(15);
    $temppass = "deleted".$passrand;
    mysql_query("UPDATE `ftpuser` SET `deleted` = '1', `passwd` = '$temppass' WHERE `id` = '$id'")
    or die(mysql_error());
  }
}
echo '


    <div class="container">
      <h1>Create 8021X accounts</h1>
      <p>
        <form action "/frontend/index.php" method="post">
        <table class="table table-condensed">
          <thead>
            <tr>
              <th>Username</th>
              <th>Password</th>
              <th>Home Directory</th>
              <th>Account valid till</th>
              <th>Delete</th>
            </tr>
          </thead>
          <tbody>';

        $query_fillusers = "SELECT id, userid, passwd, homedir, validtill, deleted FROM ftpuser";
        $query_hosts = mysql_query($query_fillusers)
        or die(mysql_error());

        while ($row = mysql_fetch_row($query_hosts)){

          if ($row[5] == "1") {
          } else {
            if ($row[4] == "0000-00-00 00:00:00") {
              $dt = "Unlimited";
            } else {
              $dt = new DateTime($row[4]);
              $dt = $dt->format('Y-M-d');
            }
            echo "<tr>";
            echo "<td>"; echo $row[1]; echo "</td>";
            echo "<td>"; echo $row[2]; echo "</td>";
            echo "<td>"; echo $row[3]; echo "</td>";
            echo "<td>"; echo $dt; echo "</td>";
            echo "<td><label class=checkbox><input name=id[] value=$row[0] type=checkbox></label</td>";
            echo "</tr>";
          }
        }    


        echo '
          <tbody>
          <tr>
          <td><input name=username type=text value=username></input></td>
          <td><input name=password type=text value='; echo rand_string(8); echo'></input></td>
          <td><select name=valid>
            <option value=1 >1 Week valid</option>
            <option value=2 >2 Weeks valid</option>
            <option value=4 selected>4 Weeks valid</option>
            <option value=0 >Unlimited valid</option>
            </select>
          </td>
          </tr>
        
        </table>
      <input type="submit">
      </form>
      </p>

    </div> <!-- /container -->
';
 
 include_once "footer.html";
 ?>