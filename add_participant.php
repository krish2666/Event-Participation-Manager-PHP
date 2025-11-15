<?php
require "db_connect.php";
$event_id=isset($_POST["event_id"])?intval($_POST["event_id"]):0;
$name=isset($_POST["name"])?trim($_POST["name"]):"";
$email=isset($_POST["email"])?trim($_POST["email"]):"";
$phone=isset($_POST["phone"])?trim($_POST["phone"]):"";
if($event_id>0&&$name!==""&&$email!==""){
  $stmt=$conn->prepare("INSERT INTO participants(event_id,name,email,phone) VALUES(?,?,?,?)");
  $stmt->bind_param("isss",$event_id,$name,$email,$phone);
  $stmt->execute();
  $stmt->close();
}
header("Location: view_event.php?id=".$event_id);