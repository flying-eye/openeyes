<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<?php
$this->beginContent('//patient/event_container',array('no_face'=>true));
$assetAliasPath = 'application.modules.OphTrOperationbooking.assets';
$this->moduleNameCssClass .= ' edit';
?>

<?php
$clinical = $clinical = $this->checkAccess('OprnViewClinical');

$warnings = $this->patient->getWarnings($clinical);
?>

<div class="row">
  <div class="cols-12 column">

    <section class="element">
        <?php $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
            'id' => 'operation-note-select',
            'enableAjaxValidation' => false,
        ));
        ?>

        <?php $this->displayErrors($errors) ?>

        <?php if ($warnings) { ?>
          <div class="row">
            <div class="cols-12 column">
              <div class="alert-box patient with-icon">
                  <?php foreach ($warnings as $warn) { ?>
                    <strong><?php echo $warn['long_msg']; ?></strong>
                    - <?php echo $warn['details'];
                  } ?>
              </div>
            </div>
          </div>
        <?php } ?>

      <header class="element-header">
        <h3 class="element-title">Create Operation Note</h3>
      </header>

      <input type="hidden" name="SelectBooking"/>

      <div class="field-row">
          <div class="data-value flex-layout">
            <p>
                <?php if (count($operations) > 0): ?>
                  Please indicate whether this operation note relates to a booking or an unbooked emergency:
                <?php else: ?>
                  There are no open bookings in the current episode so only an emergency operation note can be created.
                <?php endif; ?>
            </p>
          </div>
          <br/>
        <div class="data-group cols-8">
          <div class="data-value" style="padding-left: 100px">
            <table class="cols-10 last-left">
              <thead>
              <tr>
                <th>Booked Date</th>
                <th>Procedure</th>
                <th>Comments</th>
              </tr>
              </thead>
              <tbody>

              <?php foreach ($operations as $operation): ?>
                <tr>
                  <td>
                    <span class="cols-3 column <?php echo $theatre_diary_disabled ? 'hide' : '' ?>">
                    <?php if (!$theatre_diary_disabled) {
                        echo $operation->booking->session->NHSDate('date');
                    } ?>
                    </span>
                  </td>
                  <td>
                    <a href="#" class="booking-select" data-booking="booking<?= $operation->event_id ?>">
                        <?php
                        echo implode('<br />', array_map(function ($procedure) use ($operation) {
                            return $operation->eye->name . ' ' . $procedure->term;
                        }, $operation->procedures));
                        ?>
                    </a>
                  </td>
                  <td>
                      <?= $operation->comments; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
              <tr>
                <td>N/A</td>
                <td>
                  <a href="#" class="booking-select" data-booking="emergency">Emergency / Unbooked<a>
                </td>
                <td></td>
              </tr>
              </tbody>
            </table>

          </div>
        </div>
          <?php $this->displayErrors($errors, true) ?>
          <?php $this->endWidget(); ?>
    </section>
  </div>
</div>
<script type="text/javascript">
  $(function () {
    $('.booking-select').on('click', function () {
      $('[name="SelectBooking"]').val($(this).data('booking'));
      $('#operation-note-select').submit();
    });
  });
</script>
<?php $this->endContent(); ?>

