<?php
require "db_connect.php";
$search=isset($_GET["q"])?trim($_GET["q"]):"";
$sql="SELECT id,title,location,event_date,created_at FROM events";
$params=[];
$types="";
if($search!==""){
  $sql.=" WHERE title LIKE ? OR location LIKE ?";
  $stmt=$conn->prepare($sql." ORDER BY event_date DESC, id DESC");
  $s="%".$search."%";
  $stmt->bind_param("ss",$s,$s);
  $stmt->execute();
  $events=$stmt->get_result()->fetch_all(MYSQLI_ASSOC);
  $stmt->close();
}else{
  $sql.=" ORDER BY event_date DESC, id DESC";
  $events=$conn->query($sql)->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="stylesheet" href="style.css">
<title>Event Participation Manager</title>
</head>
<body>
<div class="container">
<header class="topbar">
<h1>Event Participation Manager</h1>
<form class="search" method="get">
<input type="text" name="q" placeholder="Search by title or location" value="<?php echo htmlspecialchars($search); ?>">
<button type="submit">Search</button>
<a href="index.php" class="clear">Clear</a>
</form>
</header>

<section class="grid two">
<div class="card">
<h2>Create Event</h2>
<form method="post" action="event_create.php" class="form">
<label>Title</label>
<input name="title" required>
<label>Location</label>
<input name="location" required>
<label>Date & Time</label>
<input type="datetime-local" name="event_date" required>
<label>Description</label>
<textarea name="description" rows="4"></textarea>
<button type="submit">Create</button>
</form>
</div>

<div class="card">
<h2>All Events</h2>
<div class="list">
<?php if(!$events){ ?>
<p class="muted">No events found</p>
<?php } else { foreach($events as $e){ ?>
<a class="list-item" href="view_event.php?id=<?php echo $e["id"]; ?>">
<div class="li-main">
<b><?php echo htmlspecialchars($e["title"]); ?></b>
<span class="pill"><?php echo date("d M Y, H:i",strtotime($e["event_date"])); ?></span>
</div>
<div class="li-sub">
<span><?php echo htmlspecialchars($e["location"]); ?></span>
<span class="muted">Created <?php echo date("d M Y",strtotime($e["created_at"])); ?></span>
</div>
</a>
<?php }} ?>
</div>
</div>
</section>
</div>
</body>
</html>