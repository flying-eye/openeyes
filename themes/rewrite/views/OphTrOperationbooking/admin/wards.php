<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>
<div class="box admin">
	<h2>Wards</h2>
	<form id="admin_wards" class="panel">
		<table class="grid">
			<thead>
			<tr>
			<th><input type="checkbox" id="checkall" class="wards" /></th>
			<th>Site</th>
			<th>Name</th>
			<th>Code</th>
			<th>Restrictions</th>
			</tr>
			</thead>
			<tbody>
			<?php
			$criteria = new CDbCriteria;
			$criteria->order = "display_order asc";
			foreach (OphTrOperationbooking_Operation_Ward::model()->findAll() as $i => $ward) {?>
				<tr class="clickable sortable "<?php if ($i%2 == 0) {?>even<?php } else {?>odd<?php }?>" data-attr-id="<?php echo $ward->id?>">
				<td><input type="checkbox" name="ward[]" value="<?php echo $ward->id?>" class="wards" /></td>
				<td><?php echo $ward->site->name?></td>
				<td><?php echo $ward->name?></td>
				<td><?php echo $ward->code?>&nbsp;</td>
				<td><?php echo $ward->restrictionText?></td>
			<?php }?>
			</tbody>
			<tfoot>
			<tr>
				<td colspan="8">
			<?php echo EventAction::button('Add', 'add_ward', null, array('class' => 'small', 'id'=>'et_add_ward'))->toHtml()?>
			<?php echo EventAction::button('Delete', 'delete_ward', null, array('class' => 'small'))->toHtml()?>
				</td>
			</tr>
			</tfoot>
		</table>
	</form>
</div>

<div id="confirm_delete_wards" title="Confirm delete ward" style="display: none;">
	<div>
		<div id="delete_wards">
			<div class="alertBox" style="margin-top: 10px; margin-bottom: 15px;">
				<strong>WARNING: This will remove the wards from the system.<br/>This action cannot be undone.</strong>
			</div>
			<p>
				<strong>Are you sure you want to proceed?</strong>
			</p>
			<div class="buttonwrapper" style="margin-top: 15px; margin-bottom: 5px;">
				<input type="hidden" id="medication_id" value="" />
				<button type="submit" class="classy red venti btn_remove_wards"><span class="button-span button-span-red">Remove ward(s)</span></button>
				<button type="submit" class="classy green venti btn_cancel_remove_wards"><span class="button-span button-span-green">Cancel</span></button>
				<img class="loader" src="<?php echo Yii::app()->createUrl('img/ajax-loader.gif')?>" alt="loading..." style="display: none;" />
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	handleButton($('#et_delete_ward'),function(e) {
		e.preventDefault();

		if ($('input[type="checkbox"][name="ward[]"]:checked').length <1) {
			new OpenEyes.Dialog.Alert({
				content: "Please select the ward(s) you wish to delete."
			}).open();
			enableButtons();
			return;
		}

		$.ajax({
			'type': 'POST',
			'url': baseUrl+'/OphTrOperationbooking/admin/verifyDeleteWards',
			'data': $('form#wards').serialize()+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
			'success': function(resp) {
				if (resp == "1") {
					enableButtons();

					if ($('input[type="checkbox"][name="ward[]"]:checked').length == 1) {
						$('#confirm_delete_wards').attr('title','Confirm delete ward');
						$('#delete_wards').children('div').children('strong').html("WARNING: This will remove the ward from the system.<br/><br/>This action cannot be undone.");
						$('button.btn_remove_wards').children('span').text('Remove ward');
					} else {
						$('#confirm_delete_wards').attr('title','Confirm delete wards');
						$('#delete_wards').children('div').children('strong').html("WARNING: This will remove the wards from the system.<br/><br/>This action cannot be undone.");
						$('button.btn_remove_wards').children('span').text('Remove wards');
					}

					$('#confirm_delete_wards').dialog({
						resizable: false,
						modal: true,
						width: 560
					});
				} else {
					new OpenEyes.Dialog.Alert({
						content: "One or more of the selected wards have active future bookings and so cannot be deleted."
					}).open();
					enableButtons();
				}
			}
		});
	});

	$('button.btn_cancel_remove_wards').click(function(e) {
		e.preventDefault();
		$('#confirm_delete_wards').dialog('close');
	});

	handleButton($('button.btn_remove_wards'),function(e) {
		e.preventDefault();

		// verify again as a precaution against race conditions
		$.ajax({
			'type': 'POST',
			'url': baseUrl+'/OphTrOperationbooking/admin/verifyDeleteWards',
			'data': $('form#wards').serialize()+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
			'success': function(resp) {
				if (resp == "1") {
					$.ajax({
						'type': 'POST',
						'url': baseUrl+'/OphTrOperationbooking/admin/deleteWards',
						'data': $('form#wards').serialize()+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
						'success': function(resp) {
							if (resp == "1") {
								window.location.reload();
							} else {
								new OpenEyes.Dialog.Alert({
									content: "There was an unexpected error deleting the wards, please try again or contact support for assistance",
									onClose: function() {
										enableButtons();
										$('#confirm_delete_wards').dialog('close');
									}
								}).open();

							}
						}
					});
				} else {
					new OpenEyes.Dialog.Alert({
						content: "One or more of the selected wards now have active future bookings and so cannot be deleted.",
						onClose: function() {
							enableButtons();
							$('#confirm_delete_wards').dialog('close');
						}
					}).open();
				}
			}
		});
	});
</script>