
<div style="border:1px solid #990000;padding-left:20px;margin:0 0 10px 0;">
	<h4>Una Excepción no controlada ha sido encontrado</h4>
	
	<p><b>Tipo:</b> <span><?= get_class($exception); ?></span></p>
	<p><b>Mensaje</b> <span><?= $message; ?></span></p>
	<p><b>Archivo</b> <span><?= $filepath . ' #' . $line; ?></span></p>

	<p><b>Proceso:</b></p>
	<table class="table" cellpadding="0" cellspacing="5" style="margin-left:10px"><?php
		foreach ($trace as $ind => $error)
		{
			?><tr>
			<td><b>Archivo:</b> <?= $error['file']; ?></td>
			<td><b>Linea:</b> <?= $error['line']; ?></td>
			<td><b>Función:</b> <?= $error['function']; ?></td>
		</tr><?php
		}
	?></table>
</div>
