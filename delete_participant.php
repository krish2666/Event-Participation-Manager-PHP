<?php
require "db_connect.php";
$id=isset($_POST["id"])?intval($_POST["id"]):0;
$event_id=isset($_POST["event_id"])?intval($_POST["event_id"]):0;
if($id>0){
  $stmt=$conn->prepare("DELETE FROM participants WHERE id=?");
  $stmt->bind_param("i",$id);
  $stmt->execute();
  $stmt->close();
}
header("Location: view_event.php?id=".$event_id);