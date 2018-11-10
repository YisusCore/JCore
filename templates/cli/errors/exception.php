Una Excepción no controlada ha sido encontrado

Tipo:        <?= get_class($exception), "\n"; ?>
Mensaje:     <?= $message, "\n"; ?>
Archivo:     <?= $filepath . ' #' . $line; ?>

Proceso:
<?php
foreach ($trace as $ind => $error)
{
	?>
	Archivo:   <?= $error['file'], "\n"; ?>
	Linea:     <?= $error['line'], "\n"; ?>
	Función:   <?= $error['function'], "\n\n"; ?>
	<?php
}
?>
