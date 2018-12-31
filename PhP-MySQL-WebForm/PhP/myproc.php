<?php

#Class representing the user-form for entering details of a visiting card
class VisitingCard
{
	#Name
	var $name;

	#Designation
	var $desg;

	#Gender
	var $gender;

	#Address
	var $addr;

	#Set of Contacts
	var $contacts;

	#Set of Email-ids
	var $emails;

	#State of Error
	var $err = false;

	#Begining of output message in case of an error
	var $errMsgBeg = "	<fieldset>
							<legend>Error Encountered:</legend>";

	#End of output message in case of an error
	var $errMsgEnd = "	<br></fieldset><br><br>
						<form action=\"myform.html\" method=\"post\">
							<input type=\"submit\" value=\"Try Again\">
						</form>";						

	#Constructor function
	function VisitingCard()
	{
		//Constructor
	}

	#Function to get user form-input
	function getInput()
	{
		$this->name = trim($_POST['name']);

		$this->desg = trim($_POST['desg']);

		$this->gender = trim($_POST['gender']);

		
		if(strcmp($this->gender, 'M') == 0)
		{
			$this->addr = trim($_POST['office']);
		}	

		elseif(strcmp($this->gender, 'F') == 0)
			$this->addr = trim($_POST['resd']);

		
		$this->contacts = array(trim($_POST['con1']));
		array_push($this->contacts, trim($_POST['con2']), trim($_POST['con3']), trim($_POST['con4']), trim($_POST['con5']));

		$this->emails = array(trim($_POST['email1']));
		array_push($this->emails, trim($_POST['email2']));

		//print_r($this->emails);

		$this->validateInput();

	}

	#Function to validate user form-input
	function validateInput()
	{
		if(preg_match('/^[a-zA-Z ]*$/', $this->name) != 1)
		{
			$this->err = true;
			$this->errMsgBeg .= "-> Error in name $this->name. Only alphabets allowed.<br>";
		}

		if(preg_match('/^[a-zA-Z ]*$/', $this->desg) != 1)
		{
			$this->err = true;
			$this->errMsgBeg .= "-> Error in designation $this->desg. Only alphabets allowed.<br>";
		}

		if(empty($this->addr))
		{
			$this->err = true;
			$this->errMsgBeg .= "-> Error in address entered. Please fill correct address field as per gender: Office address for Male user, and Residential address for Female user.<br>";	
		}

		foreach($this->emails as $ele)
		{
			if(!filter_var($ele, FILTER_VALIDATE_EMAIL))
			{
				$this->err = true;
				$this->errMsgBeg .= "-> Invalid email-id $ele. Enter correctly.<br>";
			}
		}

	}

	#Function to create a PDO object and enter the record in the Database
	function createRecord()
	{
		try
		{
			$conn = new PDO("mysql:host=localhost;dbname=contact", 'root', '');	

			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			echo "Connection Successful!<br>";
 
			$num = "";
			$eml = "";

			foreach($this->contacts as $ele)
				$num .= $ele . " ";

			foreach($this->emails as $ele)
				$eml .= $ele . " ";

			$sql = "INSERT INTO visit_card(name, designation, gender, address, contact, email) VALUES ('$this->name', '$this->desg', '$this->gender', '$this->addr', '$num', '$eml');";

			$conn->exec($sql);

			echo "Record Entered.<br>";

			$conn = null;

			echo "Database Connection Closed.<br>";

			echo "	<br><br>
					<fieldset>
					<legend>Enter another record:</legend>
					<form action=\"myform.html\" method=\"post\">
						<input type=\"submit\" value=\"New Record\">
					</form>
					</fieldset>
					<br><br>
					<fieldset>
					<legend>Print Visiting Card:</legend>
					<form action=\"print.html\" method=\"post\">
						<input type=\"submit\" value=\"Get PDF\">
					</form>
					</fieldset>";

		}
		catch(PDOException $e)
		{
			echo "Connection Error: " . $e->getMessage();

		}

	}

}

#Create a new object of the class representing the user input form
$card = new VisitingCard();

#Get input from the form
$card->getInput();

#If no error is encountered, enter the record into the Database
if(!$card->err)
	$card->createRecord();

#Otherwise display the error message
else
	echo $card->errMsgBeg . $card->errMsgEnd;

?>