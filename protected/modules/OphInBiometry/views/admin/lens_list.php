<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

$this->renderPartial('//admin/generic/list', array('admin' => $admin, 'displayOrder' => $displayOrder, 'buttons' => $buttons));

?>
<div class="admin box">
    <?php
        $i = 1;
        if( $i == 1){
            ?>
                    <a href="/OphInBiometry/MergeLensData">Merge operation note cataract element lens data</a>
            <?php
        }
    ?>
</div>
