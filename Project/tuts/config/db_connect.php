<?php 

//connect to database
$conn= mysqli_connect('localhost','root','','mydatabase');
//checq the connection
if(!$conn)
{
echo 'Connetion error: '.mysqli_connect_error();

}

 ?>