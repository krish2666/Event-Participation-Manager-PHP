<?php
require "db_connect.php";
$title=isset($_POST["title"])?trim($_POST["title"]):"";
$location=isset($_POST["location"])?trim($_POST["location"]):"";
$event_date=isset($_POST["event_date"])?trim($_POST["event_date"]):"";
$description=isset($_POST["description"])?trim($_POST["description"]):"";
if($title!==""&&$location!==""&&$event_date!==""){
  $stmt=$conn->prepare("INSERT INTO events(title,location,event_date,description) VALUES(?,?,?,?)");
  $stmt->bind_param("ssss",$title,$location,$event_date,$description);
  $stmt->execute();
  $id=$stmt->insert_id;
  $stmt->close();
  header("Location: view_event.php?id=".$id);
  exit;
}
header("Location: index.php");