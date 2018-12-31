<?php

#Include the external library FPDF for generating PDF output
require('fpdf.php');

#Class representing the user-form for requesting generation of the Visiting Cards PDF
class PrintVisitingCard
{
	#Name on the visiting card(s)
	var $name;

	#State of error
	var $err = false;

	#PDF output string
	var $output = "";

	#Begining of output message in case of an error
	var $errMsgBeg = "	<fieldset>
							<legend>Error Encountered:</legend>";

	#End of output message in case of an error
	var $errMsgEnd = "	<br></fieldset><br><br>
						<form action=\"print.html\" method=\"post\">
							<input type=\"submit\" value=\"Try Again\">
						</form>";

	#Function to retrieve name from the form
	function getName()
	{
		$this->name = trim($_POST['name']);

		$this->validateInput();

	}

	#Function to validate that the name entered is correct
	function validateInput()
	{
		if(preg_match('/^[a-zA-Z ]*$/', $this->name) != 1)
		{
			$this->err = true;
			$this->errMsgBeg .= "-> Error in name $this->name. Only alphabets allowed.<br>";
		}
	}

	#Function to obtain data from the database and generate the PDF
	function getRecord()
	{
		try
		{
			$conn = new PDO("mysql:host=localhost;dbname=contact", 'root', '');	

			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$sql = "SELECT * FROM visit_card WHERE name='$this->name'";

			$result = $conn->query($sql);

			$result->setFetchMode(PDO::FETCH_ASSOC);

			while($rec = $result->fetch())
			{
				$this->output .= "#VISITING CARD#\n\nName: ";

				if(strcmp($rec['gender'], 'M') == 0)
					$this->output .= "Mr. ";

				elseif(strcmp($rec['gender'], 'F') == 0)
					$this->output .= "Ms. ";

				$this->output .= $rec['name'] . "\n\nDesignation: " . $rec['designation'] . "\n\nAddress: " . $rec['address'] . "\n\nContact: ";

				$cont = explode(" ", trim($rec['contact']));

				foreach($cont as $ele)
				{
					$this->output .= $ele . ", ";
				}

				$this->output .= "\n\nEmail-id: ";

				$eml = explode(" ", trim($rec['email']));

				foreach($eml as $ele)
				{
					$this->output .= $ele . ", ";
				}

				$this->output .= "\n\n---------------------------------------------------------------\n\n";				
			}


			$this->getPDF();

		}
		catch(PDOException $e)
		{
			echo "Connection Error: " . $e->getMessage();
		}
	}

	#Function to generate the PDF
	function getPDF()
	{
		$pdf = new FPDF();
		$pdf->AddPage();
		$pdf->SetFont('Arial', 'I', 16);
		$pdf->Write(5, $this->output);
		$pdf->Output();

	}
}

#Create an object representing the user form for requesting generation of a visiting card
$card = new PrintVisitingCard();

#Get user input name
$card->getName();

#If no error is encountered, Get relevant data from the database and generate the PDF of visiting cards
if(!$card->err)
	$card->getRecord();

#Otherwise display the error message
else
	echo $card->errMsgBeg . $card->errMsgEnd;

?>