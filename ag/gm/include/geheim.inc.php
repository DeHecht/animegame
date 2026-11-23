<?php

defineIfNotDefined('GEHEIM_START_GEBOT', 1000000);

// Definiert wieviele Auktion insgesamt minimal geplant werden
defineIfNotDefined('GEHEIM_ANZAHL_AUKTIONEN_MIN', 1);
// Definiert wieviele Auktion insgesamt maximal geplant werden
defineIfNotDefined('GEHEIM_ANZAHL_AUKTIONEN_MAX', 2);
// Definiert wieviele Items gleichzeitig pro Auktion auslaufen
defineIfNotDefined('GEHEIM_ANZAHL_AUKTIONS_ITEMS', 4);
// Definiert wie hoch die Chance ist eine Niete zu bekommen. 0.5 = 50%
defineIfNotDefined('GEHEIM_CHANCE_NIETE', 0.5);


function displayGeheim($request) {
	if($request['action'] == 'edit') {
		setDefine('GEHEIM_START_GEBOT', $request['start']);
		setDefine('GEHEIM_ANZAHL_AUKTIONEN_MIN', $request['min']);
		setDefine('GEHEIM_ANZAHL_AUKTIONEN_MAX', $request['max']);
		setDefine('GEHEIM_ANZAHL_AUKTIONS_ITEMS', $request['anzahl']);
		setDefine('GEHEIM_CHANCE_NIETE', $request['niete']);
		echo '<a href="?choose=geheim">weiter</a>';
		return;
	}
	
	
	?>
	<form action="" method="POST">
	<input name="choose" value="geheim" type="hidden"></input>
	<input name="action" value="edit" type="hidden"></input>

	<table>
		<tr>
			<th colspan="2">Geheim Übersicht</th>
		</tr>
		<tr>
			<td>Startgebot der Geheimauktion:</td><td><input name="start" value="<?php echo GEHEIM_START_GEBOT; ?>"/></td>
		</tr>
		<tr>
			<td>Anzahl Items pro Geheimauktion:</td><td><input name="anzahl" value="<?php echo GEHEIM_ANZAHL_AUKTIONS_ITEMS; ?>"/></td>
		</tr>
		<tr>
			<td>Min Anzahl Geheimauktionen pro Woche:</td><td><input name="min" value="<?php echo GEHEIM_ANZAHL_AUKTIONEN_MIN; ?>"/></td>
		</tr>
		<tr>
			<td>Max Anzahl Geheimauktionen pro Woche:</td><td><input name="max" value="<?php echo GEHEIM_ANZAHL_AUKTIONEN_MAX; ?>"/></td>
		</tr>
		<tr>
			<td>Prozentiale Anzahl Nieten (50% => 0.5):</td><td><input name="niete" value="<?php echo GEHEIM_CHANCE_NIETE; ?>"/></td>
		</tr>
		<tr>
			<td colspan="2"><input type="submit" /></td>
		</tr>
		<tr>
			<td><a href="<?php echo $_SERVER['PHP_SELF']; ?>">Zum Hauptmenu</a>
			</td>
		</tr>
	</table>
</form>
<?php 
}

