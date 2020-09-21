<?php
	ob_start();
	session_start();
	$error = NULL;
	$timePosted = NULL;

	$mysql = new mysqli('localhost', 'root', '', 'imtuts');

	if (isset($_POST['post'])) {
		$id = $_SESSION['id'];
		$post_message = $_POST['message'];
		$file = $_FILES['file'];
		$category = $_POST['category'];

		if (empty($post_message) && empty($file)) {
			$error = "Write or upload something to post!";
		} else {
			if ($category == 'Select a category') {
				$error = "Select a category for your post!";
			} else {
				
				$fileName = $_FILES['file']['name'];
				$fileTmpName = $_FILES['file']['tmp_name'];
				$fileSize = $_FILES['file']['size'];
				$fileError = $_FILES['file']['error'];
				$fileType = $_FILES['file']['type'];
	
				$fileExt = explode('.', $fileName);
				$fileActualExt = strtolower(end($fileExt));
	
				$allowed = array('jpeg', 'jpg', 'png', 'gif');
	
				if (in_array($fileActualExt, $allowed)) {
					if ($fileError === 0) {
						if ($fileSize < 1000000) {
							$fileNameNew = uniqid('', true).".".$fileActualExt;
							$fileDestination = 'images/'.$fileNameNew;
							move_uploaded_file($fileTmpName, $fileDestination);
	
							$sql = "INSERT INTO post (posterId, postMessage, postImage, postCategory) VALUES ('$id', '$post_message', '$fileNameNew', '$category')";
							$query = $mysql->query($sql);
	
							if ($query) {
								header('Location: index.php?posted-successfully');
							}
	
						} else {
							$error = "the image size is too big!";
						}
						
					} else {
						$error = "There was an error uploading your image!";
					}
					
				}elseif ($fileActualExt == '') {
					$sql = "INSERT INTO post (posterId, postMessage, postImage, postCategory) VALUES ('$id', '$post_message', '$fileNameNew', '$category')";
					$query = $mysql->query($sql);
	
					if ($query) {
						header('Location: index.php?posted-successfully');
					}
				} else {
					$error = "Image format not supported!";
				}
				
				
			}
		}
	}

	if (isset($_GET['post'])) {
		$postId = $_GET['post'];
		$likerid = $_GET['likerid'];

		$sql = "INSERT INTO post_likes (postId, likerId) VALUES ('$postId', '$likerid')";
		$result = $mysql->query($sql);

		if ($result) {
			header('refresh:0, index.php');
		}
	}

	if (isset($_GET['postid'])) {
		$postId = $_GET['postid'];
		$unliker = $_GET['unlikerid'];

		$sql = "DELETE FROM post_likes WHERE postId='$postId' AND likerId='$unliker'";
		$result = $mysql->query($sql);

		if ($result) {
			header('refresh:0, index.php');
		}
	}

	function getPosts($mysql){

		$id = $_SESSION["id"];
		$cat1 = $_SESSION['cat1'];
		$cat2 = $_SESSION['cat2'];
		$cat3 = $_SESSION['cat3'];
		$cat4 = $_SESSION['cat4'];
		$cat5 = $_SESSION['cat5'];

		if (isset($_GET['postcat'])) {
			
			include ('postcategory.php');

		} else {
			$sql = "SELECT * FROM post WHERE postCategory IN('$cat1', '$cat2', '$cat3', '$cat4', '$cat5') ORDER BY RAND() LIMIT 10";
			$query = $mysql->query($sql);

			while ($row = $query->fetch_assoc()) {
				$posterId = $row['posterId'];
				$sql2 = "SELECT * FROM users WHERE id='$posterId'";
				$query2 = $mysql->query($sql2);

				if ($row2 = $query2->fetch_assoc()) {
					$post = $row['id'];
					$sql4 = "SELECT * FROM post_likes WHERE postId='$post' AND likerId='$id'";
					$query4 = $mysql->query($sql4);	

					$sql8 = "SELECT * FROM post_likes WHERE postId='$post'";
					$query8 = $mysql->query($sql8);	
					$likes = $query8->num_rows;			

					date_default_timezone_set('Africa/Lagos');

						$postTime = $row['postDate'];
						$time_ago = strtotime($postTime);
						$current_time = date("Y-m-d H:i:s");
						$time_now = strtotime($current_time);
						$time_difference = $time_now - $time_ago;
						$seconds	= $time_difference;
						$minutes	= round($seconds / 60);
						$hours		= round($seconds / 3600);
						$days		= round($seconds / 86400);
						$weeks		= round($seconds / 604800);
						$months		= round($seconds / 2629440);
						$years		= round($seconds / 31553280);

						if ($seconds <= 60) {
							$timePosted = "Just now";
						} elseif($minutes <= 60){
							if ($minutes == 1) {
								$timePosted = "1 minute ago";
							} else {
								$timePosted = "$minutes minutes ago";
							}
						} elseif ($hours <= 24) {
							if ($hours == 1) {
								$timePosted = "1 hour ago";
							} else {
								$timePosted = "$hours hours ago";
							}
						} elseif ($days <= 7) {
							if ($days == 1) {
								$timePosted = "yesterday";
							} else {
								$timePosted = "$days days ago";
							}
						} elseif ($weeks <= 4.3) {
							if ($weeks == 1) {
								$timePosted = "1 week ago";
							} else {
								$timePosted = "$weeks weeks ago";
							}
						} elseif ($months <= 12) {
							if ($months == 1) {
								$timePosted = "1 month ago";
							} else {
								$timePosted = "$months months ago";
							}
						} else {
							if ($years == 1) {
								$timePosted = "1 year ago";
							} else {
								$timePosted = "$years years ago";
							}
							
						}

					
							

					echo "<div class='each-post'>
							<div class='each-post-body'>
									<a href='post.php?post-id=".$row['id']."'><div class='clearfix post-head'>
										<div class='float-left poster-img'>
											<img src='../account/upload/".$row2['prof_image'].".png' alt='img'>
										</div>

										<div class='float-left poster-uname'>
											<strong>".$row2['firstname']." ".$row2['lastname']."</strong><br>
											<span>
												<small>".$timePosted."</small>
											</span>
											<span style='background:lightgrey;color:#000;border-radius: 5px;'>
												<small style='color:#fff;margin:5px;'>".$row['postCategory']."</small>
											</span>
										</div>
									</a>";
										if ($row['posterId'] == $_SESSION['id']) {
											echo "<div class='dropdown'>
													<i class='fas fa-ellipsis-h'></i>
													<div class='dropdown-content' style='right: -20px;'>
														<a href='#'><i class='fas fa-trash'></i> Delete Post</a>
													</div>
												</div>";
										} else {
											echo "<div class='dropdown'>
													<i class='fas fa-ellipsis-h'></i>
													<div class='dropdown-content' style='right: -20px;'>
														<a href='#'><i class='fas fa-ban'></i> Report Post</a>
													</div>
												</div>";
										}
										
									
								echo "</div>
							<div class='line-lightgrey'></div>
							<div class='post-message'>".$row['postMessage']."</div>";
									
							if (!empty($row['postImage'])) {
								echo "<center style='background:#000;'>
										<div class='photo'>
											<img src='images/".$row['postImage']."' alt=''>
										</div>
									</center>";
							}
									
							if ($row4 = $query4->fetch_assoc()) {

								echo "<small style='color:lightgrey;margin:5px;'>".$likes . " Like(s)</small>";

								echo "<div class='like-count'>
										<a href='index.php?postid=".$row['id']."&unlikerid=".$id."'>
											<i class='fas fa-thumbs-up'></i>
										</a>
										<a href='post.php?post-id=".$row['id']."'>
											view all comments
										</a>
									</div>
									<div class='line-lightgrey'></div>";

							} else {

								echo "<small style='color:lightgrey;margin:5px;'>".$likes . " Like(s)</small>";

								echo "<div class='like-count'>
										<a href='index.php?post=".$row['id']."&likerid=".$id."'>
											<i class='far fa-thumbs-up'></i>
										</a>
										<a href='post.php?post-id=".$row['id']."'>
											view all comments
										</a>
									</div>
									<div class='line-lightgrey'></div>";
							}
				}
				
				$sql6 = "SELECT * FROM post_comments WHERE postId='$post' ORDER BY commentTime DESC LIMIT 1";
				$query6 = $mysql->query($sql6);

				while ($row6 = $query6->fetch_assoc()) {
					$userid = $row6['commenterId'];
					$sql7 = "SELECT * FROM users WHERE id='$userid'";
					$query7 = $mysql->query($sql7);

					if ($row7 = $query7->fetch_assoc()) {

						date_default_timezone_set('Africa/Lagos');

						$postTime = $row6['commentTime'];
						$time_ago = strtotime($postTime);
						$current_time = date("Y-m-d H:i:s");
						$time_now = strtotime($current_time);
						$time_difference = $time_now - $time_ago;
						$seconds	= $time_difference;
						$minutes	= round($seconds / 60);
						$hours		= round($seconds / 3600);
						$days		= round($seconds / 86400);
						$weeks		= round($seconds / 604800);
						$months		= round($seconds / 2629440);
						$years		= round($seconds / 31553280);

						if ($seconds <= 60) {
							$timePosted = "Just now";
						} elseif($minutes <= 60){
							if ($minutes == 1) {
								$timePosted = "1 minute ago";
							} else {
								$timePosted = "$minutes minutes ago";
							}
						} elseif ($hours <= 24) {
							if ($hours == 1) {
								$timePosted = "1 hour ago";
							} else {
								$timePosted = "$hours hours ago";
							}
						} elseif ($days <= 7) {
							if ($days == 1) {
								$timePosted = "yesterday";
							} else {
								$timePosted = "$days days ago";
							}
						} elseif ($weeks <= 4.3) {
							if ($weeks == 1) {
								$timePosted = "1 week ago";
							} else {
								$timePosted = "$weeks weeks ago";
							}
						} elseif ($months <= 12) {
							if ($months == 1) {
								$timePosted = "1 month ago";
							} else {
								$timePosted = "$months months ago";
							}
						} else {
							if ($years == 1) {
								$timePosted = "1 year ago";
							} else {
								$timePosted = "$years years ago";
							}
							
						}
						

						echo "<div class='clearfix each-comment'>
								<div class='float-left commenter-img'>
									<img src='../account/upload/".$row7['prof_image'].".png' alt='img'>
								</div>

								<div class='float-left commenter-uname' style='color:grey;'>
									<a href='profile.php?user=".$row7['id']."' style='color:#000;font-weight:bold;'>".$row7['firstname']." ".$row7['lastname']."</a><br>".$row6['commentMessage']."
								</div>
							</div>";

							if ($row6['commenterId'] == $_SESSION['id']) {
								echo "<div class='comment-links'>
										<small>
											<a href='#'>Delete</a> - 
										</small>
										<small style='color:lightgrey;'>".$timePosted."</small>
									</div>";
							} else {
								echo "<div class='comment-links'>
										<small>
											<a href='index.php?comment=".$row6['id']."&replier=".$id."'>Reply</a> - 
										</small>
										<small style='color:lightgrey;'>".$timePosted."</small>
									</div>";
							}
							
							
					}
					
				}

				$sql3 = "SELECT * FROM users WHERE id='$id'";
				$query3 = $mysql->query($sql3);

				if ($row3 = $query3->fetch_assoc()) {
					echo "<div>
							<form method='post'>
								<div class='post-comments' style='margin:10px;'>

									<div style='width:8%;float:left;'>
										<img src='../account/upload/".$row3['prof_image'].".png' alt='img'>
									</div>

									<input type='hidden' name='postid' value='".$post."'>

									<div style='width:72%;float:left;'>
										<input type='text' name='comment-box' placeholder='Write a comment...' class='form-control'>
									</div>

									<div style='width:20%;float:left;'>
										<input type='submit' name='comment' style='width:100%;' value='Comment' class='btn btn-info'>
									</div>
									<div class='clear'></div>
								</div>
												
							</form>
						</div>";
				}

					echo "</div>
						</div>";
					
			}
		}
		
		
		
		if (isset($_POST['comment'])) {
			$comment = $_POST['comment-box'];
			$post_id = $_POST['postid'];
			$commenter = $id;

			if (empty($comment)) {
				
			} else {
				$sql5 = "INSERT INTO post_comments (postId, commenterId, commentMessage) VALUES ('$post_id', '$commenter ', '$comment')";
				$query5 = $mysql->query($sql5);

				if ($query5) {
					header('Location: index.php#commentedsuccessfully');
				}
			}
			
		}
	}
		
	

	function getOnlineusers($mysql){
		

			$userid = $_SESSION["id"];
			$sql2 = "SELECT * FROM users WHERE verified='1' AND online='1'";
			$query2 = $mysql->query($sql2);

			if ($query2) {
				$sql = "SELECT * FROM users WHERE online='1' AND id!=$userid";
				$query = $mysql->query($sql);

				while($row2 = $query->fetch_assoc()) {

					echo "<div id='usersonline' class='clearfix' width='100%' style='margin:4px;'>
						    <span class='float-left' style='width:20%;padding:0px;'>
						    	<img src='../account/upload/".$row2['prof_image'].".png' style='width: 100%;border-radius: 50%;border:1px solid lightgrey;'>

						    	<i class='fa fa-circle' style='position:absolute;font-size:13px;color:#8dcff4;margin-left:-8px;margin-top:22px;'></i>
						    </span>
						    <span class='float-left' style='width:70%;margin-top:5px;text-align:left;'>
						    	<p style='margin-left:4px;'>".$row2['firstname']." ".$row2['lastname']."</p>
						    </span>
							<a href='../chat/index.php?receiverId=".$row2['id']."'>
								<span class='float-right' style='width:10%;margin-top:7px;color:green;'> 
									<i class='fas fa-envelope'></i> 
								</span>
							</a>
						</div>";
			
				}
			}
	}
	


	function getnumberofnlineusers($mysql){
		$sql = "SELECT * FROM users WHERE online='1' AND verified='1'";
		$query = $mysql->query($sql);

		if($row = $query->num_rows) {
		 	if ($row < 1) {
		 		echo "0";
		 	} else {
		 		$users = $row - 1;
		 		echo $users;
		 	}
		 }
	}

	ob_end_flush();
?>
