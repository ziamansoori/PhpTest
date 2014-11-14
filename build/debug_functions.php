<?php
function sp($str)
{
	if(DEBUG == 1)
	{
		echo $str."<br />";
	}
}

function pre($arr)
{
	if(DEBUG == 1)
	{
		echo "<PRE>";
		print_r($arr);
		echo "</PRE>";
	}
}
?>