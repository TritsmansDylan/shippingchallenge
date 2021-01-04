<?php

/*connection script*/
Include ("dbconnection.php");

/*lezen database*/
$query = mysqli_query ($connect, "SELECT * from namen;");

/*table aanmaken*/
echo "<table>";
for ($i=0; $i<mysqli_num_rows($query); $i++)
   {
      echo "<tr>";
      $fetch = mysqli_fetch_array($query);
      echo "<td>$fetch[0]</td><td>$fetch[1]</td>";
      echo "</tr>";
   }
echo "</table>";
?>
