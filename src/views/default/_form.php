<?php

use kartik\datetime\DateTimePicker;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$type = strip_tags(trim(Yii::$app->request->get('type')));

$form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]);
?>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="row">
                    <div class="col-12">
                        <h4 class="page-title"><?= Html::encode($this->title) ?></h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <?php if ($model->isNewRecord) : ?>
                            <?= Html::a(Html::tag('i', '', ['class' => 'fa fa-chevron-left']) . Yii::t('config', ' Назад'), ['/config'], ['class' => 'btn btn-warning']) ?>
                            <?= Html::submitButton(Html::tag('i', '', ['class' => 'fa fa-plus']) . Yii::t('config', ' Добавить'), ['class' => 'btn btn-success']) ?>
                        <?php else: ?>
                            <?= Html::a(Html::tag('i', '', ['class' => 'fa fa-chevron-left']) . Yii::t('config', ' Назад'), ['/config'], ['class' => 'btn btn-warning']) ?>
                            <?= Html::a(Html::tag('i', '', ['class' => 'fa fa-times']) . Yii::t('config', ' Удалить'), ['delete', 'id' => $model->id], ['class' => 'btn btn-danger']) ?>
                            <?= Html::submitButton(Html::tag('i', '', ['class' => 'fa fa-save']) . Yii::t('config', ' Сохранить'), ['class' => 'btn btn-success']) ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <?= $form->field($model, 'group')->widget(Select2::class, [
                    'data' => $groups,
                    'options' => ['placeholder' => ''],
                    'pluginOptions' => [
                        'tags' => true,
                        'tokenSeparators' => false,
                        'allowClear' => true
                    ],
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
                    case $model::TYPE_DATE:
                        echo $form->field($model, 'value')->widget(DateTimePicker::className(), [
                            'type' => DateTimePicker::TYPE_INPUT,
                            'options' => ['placeholder' => 'Ввод даты/времени...'],
                            'convertFormat' => false,
                            'pluginOptions' => [
                                'format' => 'yyyy-mm-dd hh:ii:ss',
                                'autoclose' => true,
                                'weekStart' => 1,
                                'startDate' => '01.05.2015 00:00',
                                'todayBtn' => true,
                                'minView' => 2,
                            ]
                        ]);
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
    </div>
<?php ActiveForm::end(); ?>