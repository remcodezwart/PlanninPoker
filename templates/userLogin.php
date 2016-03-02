<h2>Ingelogd</h2>
<table>
	<tr>
		<th>Naam</th>
		<th>Onderwerp</th>
		<th>Datum dat de kamer werd gemaakt</th>
		<th>Eigenaar</th>
	</tr>

<?php foreach($Chambers as $chamber){
?>
	<tr>
		<td><?php echo $chamber['Name'] ?></td>
		<td><?php echo $chamber['subject'] ?></td>
		<td><?php echo $chamber['date'] ?></td>
		<td><?php echo $chamber['owner'] ?></td>
	</tr>
<?php
	}
?>
</table>
<a href="delete.php">Acount verwijderen?</a>	