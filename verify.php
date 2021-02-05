<?php
	if (isset($_GET['vkey'])) {
		//process verification
		$vkey = $_GET['vkey'];

		$mysqli = new mysqli('xxx', 'xxx', 'xxx', 'xxx');

		$resultSet = $mysqli->query("SELECT verified,vkey FROM users WHERE verified = 0 AND vkey = '$vkey' LIMIT 1");

		if ($resultSet->num_rows == 1) {
			//validate email
			$update = $mysqli->query("UPDATE users SET verified = 1 WHERE vkey ='$vkey' LIMIT 1");

			if ($update) {
				echo "<script>
						alert('Your email has been successfully verified!');
					</script>";

				header('Location: tutme.ml/register/setcat/');
			}else{
				echo $mysqli->error;
			}
		}else{
			echo "This account is invalid or has already been verified...";
		}
	}else{
		die();
	}
?>
