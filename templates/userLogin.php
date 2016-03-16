<h2>Ingelogd</h2>
<p>Opmerking alleen de eigenaar kan zijn/haar kamer verwijderen</p>
<table>
	<tr>
		<th>Naam</th>
		<th>Onderwerp</th>
		<th>Eigenaar</th>
		<th></th>
		<th></th>
	</tr>
<?php foreach($Chambers as $chamber){
?>	
	<tr>
		<td><?=$chamber['Name'] ?></td>
		<td><?=$chamber['subject'] ?></td>
		<td><?=$chamber['owner'] ?></td>
		<td><a target="_blank" href="chamber.php?chamber=<?=$chamber['id'] ?>">Kamer ingaan</a></td>
		<td><a href="chamber_delete.php?id=<?=$chamber['id']?>">kamer verwijderen</a></td>
	</tr>
<?php
	}
?>
</table>
<a href="chamber_Create.php">Kamer toevoegen</a><br>
<a href="delete.php">Acount verwijderen</a>	