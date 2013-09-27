<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8"/>
		<title>Kevin Kan: COMP 1006 Final Project </title>
		<link rel="stylesheet" type="text/css" href="styles.css" media="screen" />
	</head>
	<body>
	<!--
	Author: Kevin Kan
	Student No. 200213257
	Date April. 9 2013
	Version 2 
	Title: COMP 1006 Final Project
	For: R.MacWilliam
	-->

	<?php
		// Start the session
		require_once('startsession.php');
		
		require 'database.php';
		$dbc = mysqli_connect(DBHOST,DBUSER,DBPASS,DBNAME);
		// Show the navigation menu
		require_once('navmenu.php');
		if (isset($_SESSION['username'])) {
			$isValid=false;
			$OrderName = mysqli_real_escape_string($dbc,trim($_POST['order']));
			$OrderPayment= mysqli_real_escape_string($dbc,trim($_POST['payment']));
			$OrderItem= mysqli_real_escape_string($dbc,trim($_POST['item']));
			$OrderCost=mysqli_real_escape_string($dbc,trim($_POST['cost']));
			$Email=mysqli_real_escape_string($dbc,trim($_POST['email']));
			$idvalue =mysqli_real_escape_string($dbc,trim($_POST['idvalue']));
	   
			$idtodelete= mysqli_real_escape_string($dbc,trim($_GET['idtodelete']));
			$idtoedit = mysqli_real_escape_string($dbc,trim($_GET['idtoedit']));
			

			//should we update the record?
		if(!empty($idvalue))
		{
			$weAreUpdating=true;
			//update the correct record
			$updatequery ="UPDATE crud SET name='$OrderName', billmethod='$OrderPayment', item='$OrderItem', cost=$OrderCost, email='$Email' WHERE id=".$idvalue;
			mysqli_query($dbc,$updatequery);
			echo"<p>update sent</p>";
			
		}
		else //enables and ensures edit is not called
		{
			$weAreUpdating=false;
			echo'<p>Submit New Order</p>';
		}

		//does the user wish to edit?
		if(!empty($idtoedit))
		{
			
			//make the form show this order name
			$editQuery="SELECT * FROM crud WHERE id=".$idtoedit;
			$editData=mysqli_query($dbc, $editQuery);
			while($editRow=mysqli_fetch_array($editData))
			{
				$OrderName= mysqli_real_escape_string(dbc,trim($editRow['name']));
				$OrderPayment=mysqli_real_escape_string(dbc,trim($editRow['billmethod']));
				$OrderItem=mysqli_real_escape_string(dbc,trim($editRow['item']));
				$OrderCost=mysqli_real_escape_string(dbc,trim($editRow['cost']));
				$Email=mysqli_real_escape_string(dbc,trim($editRow['email']));			
			}
			echo "Editing the record:".$idtoedit." Order Name:".$OrderName;
		}
		if(!empty($idtodelete))
		{
			$deletequery = "delete from crud where id=$idtodelete";
			mysqli_query($dbc,$deletequery);
		}
		
		//If the user is NOT updating and
		//Did the user press submit ?
		// Insert a new record
		if (isset($_POST['submit'])&& !$weAreUpdating)
		{
			//**************************************VALIDATION***************************************
			if (preg_match("/^[0-9a-zA-Z]{3}/",$OrderName))
			{
				 echo "<p>You need at least 3 charaters long for an order name</p>";
				 echo"<p></p>";
			}
			elseif(preg_match("/^$/",$OrderName)||preg_match("/^$/",$OrderPayment)|| preg_match("/^$/",$OrderItem)|| preg_match("/^$/",$OrderCost)|| preg_match("/^$/",$Email))
			{
				echo "<p>You must fill in the following fields</p>";
				if (preg_match("/^$/",$OrderName))
				{
					echo "<p>Order Name</p>";
				}
				 if(preg_match("/^$/",$OrderPayment))
				 {
					echo "<p> Payment Method</p>";
				 }
				 if(preg_match("/^$/",$OrderItem))
				 {
					echo "<p>Item Ordered</p>";
				 }
				 if(preg_match("/^$/",$OrderCost))
				 {
					echo "<p>Total Cost</p>";
				 }
				 if(preg_match("/^$/",$OrderEmail))
				 {
					echo "<p>E-mail</p>";
				 }
			}
			elseif(!preg_match("/^[0-9.]*$/",$OrderCost))
			{
				echo"You must enter in a decimal number";
			}
			else
			 {
				 $isValid=true;
				 // Do what needs to be done - SAVE 
				 $insertStatement = "insert into crud (name,billmethod,item,cost,email) values ('$OrderName','$OrderPayment','$OrderItem',$OrderCost,'$Email')";
				 $results = mysqli_query($dbc,$insertStatement);
				 echo'<p>New Record Addition Sucessful</p>';
			 }
		}
	  /*************************************************END OF VALIDATION******************************************/
	  
			//create a command to send to the db200213257 database
			$query = "SELECT * FROM crud ORDER BY id";
			$data = mysqli_query($dbc, $query);

		if (!$isValid)
		{
			?>
		<h1>Final Project</h1>
		<p>This is the final Project login with CRUD. This form is a mock billing information page for a company.
		This form contains sticky radio buttons with both server and client side validations.
		Further note, the cost validation is only for the point in which there is a number.
		</p>
			<form enctype="multi-part/form-data" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
			<fieldset>
				<legend>Basic Order Form</legend><!--adds a title to the form-->
				
				<fieldset>
					<legend>Order Information</legend><!-- required element makes this not empty, forces some value-->
					<div>
						<label for="order">Order Name</label>
						<input type="text" value="<?php echo $OrderName ?>" name="order" id="order" required="required"/>
					</div>
					<div>
						<label for="item">Item Ordered</label>
						<input type="text" value="<?php echo $OrderItem ?>" name="item" id="item"required="required"/>
					</div>
					<div>
						<label for="cost">Total Cost</label>
						<input type="text" value="<?php echo $OrderCost?>" name="cost" id="cost" required="required"/>
					</div>
					<div>
						<label for="email">E-mail</label>
						<!-- below is HTML5 self validating e-mail type pattern. will check for vaild e-mail address-->
						<input type="email" value="<?php echo $Email?>" name="email" id="email" required="required"/>
					</div>
				</fieldset>
				
				<fieldset>
					<legend>Payment Method</legend>
					<div>
						<label for="email">Master Card</label>
						<!-- Bellow php code works from, check if $OrderPayment has value, if so then check which one and turn on that one.
						If so check off this button--> 
						<input type="radio" value="Master Card" name="payment" id="Payment_MasterCard" required="required"
						<?php if($OrderPayment=="Master Card") echo 'checked="checked"'; ?>/>
					</div>
					<div>
						<label for="email">VISA</label>
						<input type="radio" value="VISA" name="payment" id="Payment_VISA" required="required"
						<?php if($OrderPayment=="VISA") echo 'checked="checked"'; ?>/>
					</div>
					<div>
						<label for="email">Cash Order</label>
						<input type="radio" value="Cash Order" name="payment" id="Payment_CashOrder" required="required"
						<?php if($OrderPayment=="Cash Order") echo 'checked="checked"';  ?>/>
					</div>
				</fieldset>
				<input type="hidden" id ="idvalue" name ="idvalue" value="<?php echo $idtoedit ?>"/>
				<input type="submit" value="Save" name="submit" class="send" />
			</fieldset>
			</form>
		<?php }?>

		<?php
			echo'<table>';
			echo'<tr class="crudtable">';
			echo'<th scope="col">Name of Order</th>';
			echo'<th scope="col">Payment Method</th>';
			echo'<th scope="col">Item Ordered</th>';
			echo'<th scope="col">Cost</th>';
			echo'<th scope="col">E-mail</th>';
			echo'<th scope="col"></th>';
			echo'<th scope="col"></th></tr>';
			while ($row = mysqli_fetch_array($data)) //populate the table for as long as there are varibles in table
			{
				echo'<tr class= "crudtable">';
				echo '<td>'.$row['name'].'</td>';
				echo '<td>'.$row['billmethod'].'</td>';
				echo '<td>'.$row['item'].'</td>';
				echo '<td>'.$row['cost'].'</td>';
				echo '<td>'.$row['email'].'</td>';
				echo "<td><a href = \"".$_SERVER['PHP_SELF']."?idtodelete=".$row['id']."\"> Delete</a></td>";
				echo "<td><a href = \"".$_SERVER['PHP_SELF']."?idtoedit=".$row['id']."\"> Edit </a></td>".'.</tr>';
			}
		mysqli_close($dbc);
	}
	?>
	</table>
	</body>
</html>