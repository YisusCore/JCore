Un error de PHP ha sido encontrado

Código:      <?= $severity, "\n"; ?>
Mensaje:     <?= $message, "\n"; ?>
Archivo:     <?= $filepath . ' #' . $line; ?>

Proceso:
<?php
foreach ($trace as $ind => $error)
{
	?>
	Archivo:  <?= $error['file'], "\n"; ?>
	Linea:    <?= $error['line'], "\n"; ?>
	Función:  <?= $error['function'], "\n\n"; ?>
	<?php
}
?>
