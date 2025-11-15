<?php
require "db_connect.php";
$id=isset($_POST["id"])?intval($_POST["id"]):0;
$event_id=isset($_POST["event_id"])?intval($_POST["event_id"]):0;
if($id>0){
  $row=$conn->query("SELECT attended FROM participants WHERE id=".$id)->fetch_assoc();
  if($row){
    $new=$row["attended"]?0:1;
    $stmt=$conn->prepare("UPDATE participants SET attended=? WHERE id=?");
    $stmt->bind_param("ii",$new,$id);
    $stmt->execute();
    $stmt->close();
  }
}
header("Location: view_event.php?id=".$event_id);