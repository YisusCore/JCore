<?php

if ( ! isset($data))
{
	trigger_error('No se ha enviado el dato $data', E_USER_NOTICE);
//	throw new Exception('No se ha enviado el dato $data');
	return;
}

is_a($data, 'sql_data') or $data = sql_data::fromArray($data);

isset($id) or $id = 'tbl' . na(10);
isset($class) or $class = [];
is_array($class) or $class = (array)$class;

array_unshift($class, 'table');
$class = array_unique($class);

isset($hidden) or $hidden = [];

$fields = $data->getCampos();
$fields = array_combine($fields, $fields);

foreach($hidden as $hide)
{
	if ( ! isset($fields[$hide]))
	{
		continue;
	}
	
	unset($fields[$hide]);
}
$fields = array_values($fields);

$data->filter_fields($fields);

isset($title) or $title = NULL;

isset($extra_data_before) or $extra_data_before = [];
isset($extra_data_after) or $extra_data_after = [];

foreach($extra_data_before as $_field => $_data)
{
	array_unshift($fields, $_field);
//	array_unshift($data, $_data);
}

foreach($extra_data_after as $_field => $_data)
{
	$fields[] = $_field;
//	$data[] = $_data;
}

isset($filter_tbody_col) or $filter_tbody_col = NULL;

isset($vertical) or $vertical = FALSE;

?>

<div class="table-responsive" id="<?= $id; ?>-wrapper">
	<table id="<?= $id; ?>" class="<?= implode(' ', $class); ?>">
		<thead>
			<?php
			if ( ! is_null($title))
			{
				?>
			<tr>
				<th colspan="<?= count($fields); ?>"><?= $title; ?></th>
			</tr>
				<?php
			}
			
			if ( ! $vertical)
			{
				?>
			<tr>
				<?php
				foreach($fields as $field)
				{
					$field = str_replace(['_', '-'],'&nbsp;', $field);
					?>
				<th><?= ucwords($field); ?></th>
					<?php
				}
				?>
			</tr>
				<?php
			}
			?>
		</thead>
		<tbody>
			<?php
			if (count($data) === 0)
			{
				isset($nodata) or $nodata = 'No hay información';
				
				?>
			<tr>
				<th colspan="<?= $vertical ? 1 : count($fields); ?>">
					<span class="text-muted"><?= $nodata; ?></span>
				</th>
			</tr>
				<?php
			}
			
			if ($vertical)
			{
				foreach($data as $row)
				{
					foreach($row as $_field => $col)
					{
						$index = array_search($_field, $fields);

						is_callable($filter_tbody_col) and $filter_tbody_col($col, $index, $data);

						$_filter_tbody_col = 'filter_tbody_' . $_field;
						isset($$_filter_tbody_col) and is_callable($$_filter_tbody_col) and $$_filter_tbody_col($col, $data);

						is_array($col) and $col = array_html($col);
						
						$_field = str_replace(['_', '-'],'&nbsp;', $_field);
						
						?>
				<tr>
					<th><?= ucwords($_field); ?></th>
					<td><?= $col; ?></td>
				</tr>
						<?php
					}
				}
			}
			else
			{
				foreach($data as $row)
				{
					?>
				<tr>
					<?php
					foreach($row as $_field => $col)
					{
						$index = array_search($_field, $fields);

						is_callable($filter_tbody_col) and $filter_tbody_col($col, $index, $data);

						$_filter_tbody_col = 'filter_tbody_' . $_field;
						isset($$_filter_tbody_col) and is_callable($$_filter_tbody_col) and $$_filter_tbody_col($col, $data);

						is_array($col) and $col = '<button type="button" class="btn btn-default btn-sm" data-modal="' . htmlspecialchars(array_html($col)) . '">VER&nbsp;INFO</button>';
						?>
					<td><?= $col; ?></td>
						<?php
					}
					?>
				</tr>
					<?php
				}
			}
			?>
		</tbody>
	</table>
</div>

<script>
;(function($){
	var modal;
	
	$('#<?= $id; ?>').on('click', '[data-modal]', function(){
		
		if ( ! modal)
		{
			modal = $([
				'<div class="modal fade" tabindex="-1" role="dialog">',
				  '<div class="modal-dialog" role="document">',
					'<div class="modal-content">',
					  '<div class="modal-body"></div>',
					'</div>',
				  '</div>',
				'</div>'
			].join('')) . appendTo('body');
		}
		
		modal.find('.modal-body').html($(this).data('modal'));
		modal.modal('show');
	});
}(jQuery))</script>