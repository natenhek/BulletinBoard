<?php
// Start the session
session_start();
?>

<?php 
	function countReplies_Helper($postID){
	 	$servername = "localhost";
		$username = "root";
		$password = "";
		$dbname = "bbs";
 
		$conn = new mysqli($servername, $username, $password, $dbname);
 
		if ($conn->connect_error){
			die ("Connection failed: " . $conn->connect_error);
		}

		$count = 0;

		$sql_GetReply = "SELECT message_id, parent_id, postedBy, subject, content, date FROM postings WHERE parent_id = " . $postID;
		$result_getReply = $conn->query($sql_GetReply);
		if($result_getReply->num_rows > 0){
			$count = $count + $result_getReply->num_rows;
			while($row = $result_getReply->fetch_assoc()){
				$sql_GetParentPost = "SELECT message_id, postedBy, subject, content, date FROM postings WHERE message_id = " . $row['parent_id'];
				$result_GetParentPost = $conn->query($sql_GetParentPost);		
				$count = countReplies($row['message_id'], $count);
			}
		}
		$conn->close();
		return $count;
	}

	function countReplies($postID, $count){
		$servername = "localhost";
		$username = "root";
		$password = "";
		$dbname = "bbs";
 
		$conn = new mysqli($servername, $username, $password, $dbname);
 
		if ($conn->connect_error){
			die ("Connection failed: " . $conn->connect_error);
		}

		$sql_GetReply = "SELECT message_id, parent_id, postedBy, subject, content, date FROM postings WHERE parent_id = " . $postID;
		$result_getReply = $conn->query($sql_GetReply);
		if($result_getReply->num_rows > 0){
			$count = $count + $result_getReply->num_rows;
			while($row = $result_getReply->fetch_assoc()){
				$sql_GetParentPost = "SELECT message_id, postedBy, subject, content, date FROM postings WHERE message_id = " . $row['parent_id'];
				$result_GetParentPost = $conn->query($sql_GetParentPost);	
				$count = countReplies($row['message_id'], $count);
			}
		}
		$conn->close();
		return $count;
	}
?>

<!DOCTYPE html>
<head>
<title>tBoard</title>
<link rel="icon" type="image/vnd.microsoft.icon" href="http://localhost/BulletinBoard/images/elephant" />
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
<style>
.center {
    margin: auto;
	width: 40%;
}

html, body {
    height:100%;
} 

body{ 
	background-image:url("http://localhost/BulletinBoard/images/bg"); 
	-webkit-background-size: cover;
	-moz-background-size: cover;
	-o-background-size: cover;
	background-size: cover;
}

.table-striped > tbody > tr:nth-child(odd) > td, .table-striped > tbody > tr:nth-child(odd) > th {
   background-color: rgba(0,0,0,0.4);
}

.table-striped > tbody > tr:nth-child(even) > td, .table-striped > tbody > tr:nth-child(even) > th {
   background-color: rgba(0,0,0,0);
}

/* Button used to open the contact form - fixed at the bottom of the page */
.open-button {
  background-color: #555;
  color: white;
  padding: 16px 20px;
  border: none;
  cursor: pointer;
  opacity: 0.8;
  position: sticky;
  bottom: 23px;
  right: 28px;
  width: 280px;
}

/* The Modal (background) */
.modal {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 1; /* Sit on top */
    padding-top: 100px; /* Location of the box */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgb(0,0,0); /* Fallback color */
    background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

/* Modal Content */
.modal-content {
    background-color: #fefefe;
    margin: auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
}

/* The Close Button */
.close {
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: #000;
    text-decoration: none;
    cursor: pointer;
}

</style>
</head>

<body>
<nav class="navbar navbar-expand-sm bg-dark navbar-dark" style="opacity: 0.8;">
	<ul class="navbar-nav">
        
        <?php 
            echo "<li class=\"nav-item active\">";
            echo "<a class=\"nav-link\" href=\"Board.php\">
					<div>
						<img border=\"0\" src=\"http://localhost/BulletinBoard/images/elephant\" width=\"45\" height=\"45\">
					</div>
					<div>
						tBoard
					</div>
					</a>";
            echo "</li>";
        ?>
	</ul>
	<ul class="navbar-nav ml-auto">
		<li class="nav-item">
			<form class="form-inline" method="get">
			<input class="form-control mr-sm-2" type="text" name="filter" placeholder="Search" required>
			<button class="btn btn-primary" type="submit">Search</button>
			</form>
		</li>
	  <?php 
		if(isset($_SESSION['user'])){
            echo "<li class=\"nav-item\">
                        <a class=\"nav-link\">User: " . $_SESSION['username'] . "</a>" .
                     "</li>";
			echo "<form action=\"Logout.php\" id=\"FormLogout\" method=\"post\">";
				echo "<li class=\"nav-item\">";
					echo "<a class=\"nav-link\" href=\"javascript:{}\" onclick=\"document.getElementById('FormLogout').submit(); return false;\">Logout</a>";
				echo "</li>";
				echo "</form>";
		}
		else{
			echo "<li><a class=\"nav-link\" href=\"Register.php\">Sign Up</a></li>";
			echo "<li><a class=\"nav-link\" href=\"Signin.php\">Login</a></li>";
		}
	  ?>
	  
    </ul>
</nav>

 <?php
 	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "bbs";
 
	$conn = new mysqli($servername, $username, $password, $dbname);
 
	if ($conn->connect_error){
	die ("Connection failed: " . $conn->connect_error);
	}

	if(isset($_SESSION['user'])){

		$sql_GetName = "SELECT name FROM bbusers WHERE email = '" . $_SESSION['user'] . "'";
		$result_GetName = $conn->query($sql_GetName);
		mysqli_data_seek($result_GetName,0);
		$name = mysqli_fetch_row($result_GetName)[0];
	}

	if(isset($_GET['filter']) && strpos($_GET['filter'], ';') == false){
		$sql_GetPosts = "SELECT t1.message_id, t1.poster_id, t1.postedBy, t1.subject, t1.date FROM postings t1 WHERE parent_id = 0 AND t1.subject LIKE '%" . $_GET['filter'] ."%' ORDER BY date";
	}
	else{
		$sql_GetPosts = "SELECT t1.message_id, t1.poster_id, t1.postedBy, t1.subject, t1.date FROM postings t1 WHERE parent_id = 0 ORDER BY date";
	}


	$result_GetPosts = $conn->query($sql_GetPosts);

	$conn->close();
 ?>


<div class="container-fluid" style="padding:10px;"> 
<div id="divTable" style="background-color: rgba(0,0,0,0.4);">
<?php if($result_GetPosts->num_rows > 0) { ?>
	<table class="table table-bordered table-striped table-hover" style="border:5px;color:white;">
	<thead>
		<tr class="bg-dark" style="color:white;opacity: 0.8;">
		<td style="font-weight:bold;width:50%;">Subject</td>
		<td style="font-weight:bold;width:27%;">Posted By</td>
		<td style="font-weight:bold;width:3$;"><i class="fas fa-comments" style="font-size:24px"></i></td>
		<td style="font-weight:bold;width:20%;">Post Date</td>
		</tr>
	</thead>
	<tbody>
    <?php while($row = $result_GetPosts->fetch_assoc()) { ?>
		<tr>
			<td><a <?php echo "href=\"Post.php?PostID=" . $row['message_id'] ."\""; ?> style="text-decoration:none;color:Azure;"> <?php echo $row['subject']; ?> </a></td>
			<td><?php echo $row['postedBy'] ?></td>
			<td><?php echo countReplies_Helper($row['message_id']); ?></td>
			<td><?php echo $row['date'] ?></td>
		</tr>
	<?php } ?>
	<tbody>
	</table>
<?php } ?>
</div>

 <?php 
	if(isset($_SESSION['user'])){
 ?>
	<div id="myModal" class="modal" style="display:none;">
		<div class="modal-content">
			<span class="close" onclick="closeForm()">&times;</span>
			<form action="PostMessage.php" method="post">
				Name:  <?php echo $_SESSION['username']; ?> <br>
				<input type="hidden" name="postBy" value="<?php echo $_SESSION['user'];?>">
				<div class="form-inline">
					<label>Subject:&nbsp;</label>
					<input type="text" class="form-control" name="postedSubject" style="width:500px;" required>
				</div>
				<div class="form-group">
					<label>Message:</label>
					<textarea class="form-control" rows="5" id="comment" name="content" style="width:800px;" required></textarea>
				</div>
				<input type="submit" value="Submit new post" class="btn btn-primary">
			</form>
		</div>
	</div>
	<div style="width:100%;">
	<button id="buttonOpenForm" class="open-button bg-dark" style="float:right;" onclick="openForm()">Submit A Post</button>
	</div>
<?php } ?>
</div>

<script>
function openForm(){
	document.getElementById('myModal').style.display ="block";
	document.getElementById('divTable').style.display ="none";
	document.getElementById('buttonOpenForm').style.display ="none";
};
function closeForm(){
	document.getElementById('myModal').style.display ="none";
	document.getElementById('divTable').style.display ="block";
	document.getElementById('buttonOpenForm').style.display ="block";
};
</script>

</body>


</html>