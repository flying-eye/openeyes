<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<?php /** @var \OEModule\OphCiExamination\models\MedicationManagement $element */ ?>
<?php $el_id =  CHtml::modelName($element) . '_element'; ?>
    <?php foreach (array(
                       "Continued" => "getContinuedEntries",
					   "Started" => "getEntriesStartedToday",
                       "Stopped" => "getStoppedEntries",
                   ) as $section => $method): ?>
        <?php if (!empty($entries = $element->$method())): ?>
            <div class="element-data full-width">

                        <table class="medications">
													<colgroup>
														<col class="cols-3">
														<col class="cols-5">
														<col class="cols-3">
														<col class="cols-icon" span="2">
														<!-- actions auto-->
													</colgroup>
													<thead>
													<tr>
														<th>Drug</th>
														<th>Dose/frequency/route/start/stop</th>
														<th>Duration/dispense/comments</th>
														<th><i class="oe-i drug-rx small no-click"></i></th>
														<th></th><!-- actions -->
													</tr>
													</thead>
                            <tbody>
                            <?php foreach ($entries as $entry): ?>
                                <?php echo $this->render('MedicationManagementEntry_event_view',
																[
																	'entry' => $entry,
																	'stopped' => $section === "Stopped"
																	]); ?>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
        <?php endif; ?>
    <?php endforeach; ?>
