<?php
/* @var $this CaseSearchController */
/* @var $id int */
/* @var $model CaseSearchParameter */
/* @var $form CActiveForm */
?>
<tr class="parameter" id="<?= $id ?>">
    <td>
        <?php //$this->renderPartial($model->getViewPath(), array(
            //'model' => $model,
            //'id' => $id
        //)); ?>
        <?= $model->getDisplayTitle() ?>
    </td>
    <td>
        <?= $model->operation ?>
    </td>
    <td>
        <?= ''//$model->value ?>
    </td>
    <td>
        <i id="<?= $id ?>-remove" class="oe-i remove-circle medium"></i>
    </td>
</tr>



