<?php
require "db_connect.php";
$id=isset($_GET["id"])?intval($_GET["id"]):0;
$stmt=$conn->prepare("SELECT id,title,location,event_date,description FROM events WHERE id=?");
$stmt->bind_param("i",$id);
$stmt->execute();
$event=$stmt->get_result()->fetch_assoc();
$stmt->close();
if(!$event){ die("Event not found"); }
$filter=isset($_GET["status"])?$_GET["status"]:"";
$q=isset($_GET["q"])?trim($_GET["q"]):"";
$sql="SELECT id,name,email,phone,attended,registered_at FROM participants WHERE event_id=?";
$args=[$id];
$types="i";
if($filter==="attended"){ $sql.=" AND attended=1"; }
if($filter==="registered"){ $sql.=" AND attended=0"; }
if($q!==""){ $sql.=" AND (name LIKE ? OR email LIKE ? OR phone LIKE ?)"; $types.="sss"; $like="%".$q."%"; $args[]=$like; $args[]=$like; $args[]=$like; }
$sql.=" ORDER BY registered_at DESC,id DESC";
$stmt=$conn->prepare($sql);
$stmt->bind_param($types,...$args);
$stmt->execute();
$rows=$stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$total=$conn->query("SELECT COUNT(*) c FROM participants WHERE event_id=".$id)->fetch_assoc()["c"];
$attended=$conn->query("SELECT COUNT(*) c FROM participants WHERE event_id=".$id." AND attended=1")->fetch_assoc()["c"];
$registered=$total-$attended;
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="stylesheet" href="style.css">
<title><?php echo htmlspecialchars($event["title"]); ?></title>
</head>
<body>
<div class="container">
<a href="index.php" class="back">&larr; Back</a>
<div class="hero">
<h1><?php echo htmlspecialchars($event["title"]); ?></h1>
<p><?php echo htmlspecialchars($event["location"]); ?> • <?php echo date("d M Y, H:i",strtotime($event["event_date"])); ?></p>
<p class="muted"><?php echo nl2br(htmlspecialchars($event["description"])); ?></p>
<div class="stats">
<div class="stat"><span><?php echo $total; ?></span>Total</div>
<div class="stat"><span><?php echo $attended; ?></span>Attended</div>
<div class="stat"><span><?php echo $registered; ?></span>Registered</div>
</div>
</div>

<section class="grid two">
<div class="card">
<h2>Add Participant</h2>
<form method="post" action="add_participant.php" class="form">
<input type="hidden" name="event_id" value="<?php echo $event["id"]; ?>">
<label>Name</label>
<input name="name" required>
<label>Email</label>
<input type="email" name="email" required>
<label>Phone</label>
<input name="phone">
<button type="submit">Add</button>
</form>
</div>

<div class="card">
<h2>Participants</h2>
<form class="filters" method="get">
<input type="hidden" name="id" value="<?php echo $event["id"]; ?>">
<input type="text" name="q" placeholder="Search name, email or phone" value="<?php echo htmlspecialchars($q); ?>">
<select name="status">
<option value="">All</option>
<option value="registered" <?php if($filter==="registered") echo "selected"; ?>>Registered</option>
<option value="attended" <?php if($filter==="attended") echo "selected"; ?>>Attended</option>
</select>
<button type="submit">Apply</button>
<a class="clear" href="view_event.php?id=<?php echo $event["id"]; ?>">Reset</a>
<a class="ghost" href="export_csv.php?id=<?php echo $event["id"]; ?>">Export CSV</a>
</form>

<div class="table">
<div class="t-head">
<div>Name</div><div>Email</div><div>Phone</div><div>Status</div><div>Action</div>
</div>
<?php if(!$rows){ ?>
<div class="empty">No participants found</div>
<?php } else { foreach($rows as $r){ ?>
<div class="t-row">
<div><?php echo htmlspecialchars($r["name"]); ?></div>
<div><?php echo htmlspecialchars($r["email"]); ?></div>
<div><?php echo htmlspecialchars($r["phone"]); ?></div>
<div><?php echo $r["attended"]?"Attended":"Registered"; ?></div>
<div class="row-actions">
<form method="post" action="toggle_participation.php" class="inline">
<input type="hidden" name="id" value="<?php echo $r["id"]; ?>">
<input type="hidden" name="event_id" value="<?php echo $event["id"]; ?>">
<button type="submit"><?php echo $r["attended"]?"Mark Registered":"Mark Attended"; ?></button>
</form>
<form method="post" action="delete_participant.php" class="inline danger">
<input type="hidden" name="id" value="<?php echo $r["id"]; ?>">
<input type="hidden" name="event_id" value="<?php echo $event["id"]; ?>">
<button type="submit">Delete</button>
</form>
</div>
</div>
<?php }} ?>
</div>
</div>
</section>
</div>
</body>
</html>