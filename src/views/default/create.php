<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\AutoComplete;

$type = strip_tags(trim(Yii::$app->request->get('type')));

$form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="form-group pull-right">
            <?= Html::a(Yii::t('config', 'Назад'), ['/config'], ['class' => 'btn btn-warning']) ?>
            <?= Html::submitButton(Yii::t('config', 'Добавить'), ['class' => 'btn btn-success']) ?>
        </div>
    </div>
    <div class="panel-body">
        <?= $form->field($model, 'group')->widget(
            AutoComplete::className(), [
            'clientOptions' => [
                'source' => $groups,
            ],
            'options' => [
                'class' => 'form-control',
                'value' => 'default'
            ]
        ]) ?>
        <?= $form->field($model, 'key')->textInput() ?>
        <?= $form->field($model, 'name')->textInput() ?>
        <!--        --><? //= $form->field($model, 'position')->textInput() ?>
        <?= $form->field($model, 'type')->hiddenInput(['value' => $type])->label(false) ?>
        <?php
        switch ($type) {
            case $model::TYPE_STRING:
                echo $form->field($model, 'value')->textInput();
                break;
            case $model::TYPE_NUMBER:
                echo $form
                    ->field($model, 'value')
                    ->textInput(['type' => 'number']);
                break;
            default:
                echo $form
                    ->field($model, 'value')
                    ->textInput();
                break;
        }
        echo $form->field($model, 'readonly')->checkbox();
        echo $form->field($model, 'protected')->checkbox();
        ?>
    </div>
</div>
<?php ActiveForm::end(); ?>
