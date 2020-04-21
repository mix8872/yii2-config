<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>
<div class="modal-dialog">
    <?php $form = ActiveForm::begin(['id' => 'new-tab-form', 'action' => $action, 'options' => ['class' => 'has-ajax']]); ?>
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="modal-label"><?= $model->isNewRecord ? 'Добавление' : 'Редактирование' ?> вкладки</h4>
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        </div>
        <div class="modal-body">
            <?= $form->field($model, 'title')->textInput(['placeholder' => 'Название вкладки', 'autofocus' => true]) ?>
            <?= $form->field($model, 'order') ?>
        </div>
        <div class="modal-footer">
            <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить', ['class' => 'btn btn-success']) ?>
        </div>
    </div><!-- /.modal-content -->
    <?php ActiveForm::end(); ?>
</div><!-- /.modal-dialog -->
