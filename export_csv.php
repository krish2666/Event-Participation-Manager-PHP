<?php
require "db_connect.php";
$id=isset($_GET["id"])?intval($_GET["id"]):0;
$stmt=$conn->prepare("SELECT title FROM events WHERE id=?");
$stmt->bind_param("i",$id);
$stmt->execute();
$event=$stmt->get_result()->fetch_assoc();
$stmt->close();
if(!$event){ die("Event not found"); }
header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=\"participants_event_".$id.".csv\"");
$out=fopen("php://output","w");
fputcsv($out,["Name","Email","Phone","Status","Registered At"]);
$stmt=$conn->prepare("SELECT name,email,phone,attended,registered_at FROM participants WHERE event_id=? ORDER BY registered_at DESC");
$stmt->bind_param("i",$id);
$stmt->execute();
$res=$stmt->get_result();
while($r=$res->fetch_assoc()){
  fputcsv($out,[$r["name"],$r["email"],$r["phone"],$r["attended"]?"Attended":"Registered",$r["registered_at"]]);
}
fclose($out);
$stmt->close();