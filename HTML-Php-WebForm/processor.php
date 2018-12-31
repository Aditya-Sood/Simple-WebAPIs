<?php

# Include the external library
require('fpdf.php');

# Class representing the HTML user form and its input
class NumberOperations
{
	/*
	** Member Variables
	*/
	# Set of input values
	var $numArr;

	# Prefix of output string
	var $optText = "REQUIRED OUPUT:\n\n\n";

	# Boolean flag for input error
	var $inputErr = false;

	# Prefix of error message (HTML)
	//(Uses a fieldset attribute to mark a boundary around the error)
	var $errMssgBeg = "	<fieldset>
						<legend>Erroneous Input:</legend>";

	# Suffix of error message (HTML)
	//(Specifies a button for re-directing to the form)
	var $errMssgEnd = "	</fieldset>
						<br>
						<form action=\"phpform.html\">
							<input type=\"submit\" value=\"Try Again\">
						</form>";				

	# Class constructor
	function NumberOperations()
	{
		# No specific initialisation required.
	}

	# To extract input from the POST request recieved from the form
	function getInput()
	{
		# Extracts the values from the user input string
		$this->numArr = explode(" ", trim($_POST['numbers']));

		# Validate the extracted input
		$this->validateInput();
	}

	# To validate the user input as per project specifications
	//Viz: Input must be an even integer.
	function validateInput()
	{
		//Iterate over the user input array
		foreach ($this->numArr as $val)
		{
			# Check for non-integer input
			if(preg_match('/^[0-9]*$/', $val) == 0)
			{
				$this->inputErr = true;
				$this->errMssgBeg .= "-> $val is NOT an integer. Invalid input.<br>";
			}

			# Check for odd integer input
			else
			{
				if(($val % 2) != 0)
				{
					$this->inputErr = true;
					$this->errMssgBeg .= "-> $val is NOT even. Invalid input.<br>";
				}
			}

		}
	}

	# Compute the required output and add it to the output string
	function computeOutput()
	{
		# Compute ascending order sort, if requested
		if($_POST['sortascen'] == 'y')
		{
			//Sort in ascending order
			sort($this->numArr);

			//Add result to output string
			$this->optText .= "# Ascending Order - ";
			foreach($this->numArr as $val) {
				
				 $this->optText .= " ". $val;
			}

			$this->optText .= "\n\n";
		}

		# Compute descending order sort, if requested
		if($_POST['sortdescen'] == 'y')
		{
			//Sort in descending order
			rsort($this->numArr);

			//Add result to output string
			$this->optText .= "# Descending Order - ";
			foreach($this->numArr as $val) {
				
				 $this->optText .= " ". $val;
			}

			$this->optText .= "\n\n";
		}

		# Compute maximum value, if requested
		if($_POST['max'] == 'y')
		{
			rsort($this->numArr);

			//Add result to output string
			$this->optText .= "# Max Value - " . $this->numArr[0];

			$this->optText .= "\n\n";
		}

		# Compute minimun value, if requested
		if($_POST['min'] == 'y')
		{
			sort($this->numArr);

			//Add result to output string
			$this->optText .= "# Min Value - " . $this->numArr[0];

			$this->optText .= "\n\n";
		}

		# Compute range, if requested
		if($_POST['range'] == 'y')
		{
			sort($this->numArr);

			//Compute max, min values
			$min = $this->numArr[0];
			$max = $this->numArr[count($this->numArr) - 1]; 

			//Add result to output string
			$this->optText .= "# Range - " . ($max - $min);

			$this->optText .= "\n\n";
		}

		# Compute mean, if requested
		if($_POST['mean'] == 'y')
		{
			//Compute mean
			$mean = array_sum($this->numArr) / count($this->numArr);

			//Add result to output string
			$this->optText .= "# Mean Value - " . $mean;

			$this->optText .= "\n\n";
		}

		# Compute count of values, if requested
		if($_POST['count'] == 'y')
		{
			//Add result to output string
			$this->optText .= "# Total Count - " . count($this->numArr);

			$this->optText .= "\n\n";
		}

		# Return the output string
		return $this->optText;	
	}

	function getErrStatus()
	{
		return $this->inputErr;
	}

	function getErrMssg()
	{
		return ($this->errMssgBeg . $this->errMssgEnd);
	}
}

# Create an object of the external library
$pdf = new FPDF();

# Create a page in the PDF
$pdf->AddPage();
# Set the font to be used in the PDF
$pdf->SetFont('Arial', 'B', 16);

# Create an object representing the HTML user form
$numOp = new NumberOperations();
# Extract input from the POST request of the form
$numOp->getInput();

# Check for error in the user input

if($numOp->getErrStatus() == false)
{
	//Display the PDF output for correct inputs
	$optText = $numOp->computeOutput();
	$pdf->Write(5, $optText);
	$pdf->Output();
}

else if($numOp->getErrStatus() == true)
{
	//For incorrect input, display the error message (HTML)
	echo $numOp->getErrMssg();
}

?>