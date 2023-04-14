<?php

use kartik\datetime\DateTimePicker;
use kartik\select2\Select2;
use mihaildev\elfinder\InputFile;
use mix8872\config\models\Config;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

\mix8872\config\assets\ConfigAsset::register($this);

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
                        <?php if (!$model->protected): ?>
                            <?= Html::a(Html::tag('i', '', ['class' => 'fa fa-times']) . Yii::t('config', ' Удалить'), ['delete', 'id' => $model->id], ['class' => 'btn btn-danger']) ?>
                        <?php endif; ?>
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
            <div class="row">
                <div class="col-md-5 col-sm-12">
                    <?= $form->field($model, 'key')->textInput(['disabled' => $model->protected]) ?>
                </div>
                <div class="col-md-5 col-sm-12">
                    <?= $form->field($model, 'name')->textInput() ?>
                </div>
                <div class="col-md-2 col-sm-12">
                    <?= $form->field($model, 'position')->textInput() ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <?php Pjax::begin(['id' => 'config-options', 'timeout' => false]); ?>
                    <?= $form->field($model, 'tabId')->widget(Select2::class, [
                        'data' => $tabs,
                        'addon' => [
                            'append' => [
                                'content' => Html::button(Html::tag('i', '', ['class' => 'fa fa-plus']), [
                                    'class' => 'btn btn-success',
                                    'title' => 'Добавить вкладку',
                                    'data' => [
                                        'bs-toggle' => 'modal',
                                        'bs-target' => '.add-tab-modal',
                                    ]
                                ]),
                                'asButton' => true
                            ]
                        ],
                        'pluginOptions' => [
                            'tags' => false,
                            'tokenSeparators' => false,
                            'allowClear' => false
                        ],
                    ]) ?>
                    <?php Pjax::end(); ?>
                </div>
                <div class="col-md-3 col-sm-12">
                    <?= $form->field($model, 'group')->widget(Select2::class, [
                        'data' => $groups,
                        'options' => ['placeholder' => ''],
                        'pluginOptions' => [
                            'tags' => true,
                            'tokenSeparators' => false,
                            'allowClear' => true
                        ],
                    ]) ?>
                </div>
                <div class="col-md-3 col-sm-12">
                    <?= $form->field($model, 'type')->widget(Select2::class, [
                        'data' => Config::$types,
                        'pluginOptions' => [
                            'tags' => false,
                            'tokenSeparators' => false,
                            'allowClear' => false
                        ],
                        'options' => [
                            'class' => 'js-config-type'
                        ]
                    ]) ?>
                </div>
            </div>
            <?php Pjax::begin(['id' => 'config-value-input', 'timeout' => false]); ?>
            <div class="row">
                <div class="col-md-12">
                    <?php
                    switch ($model->type) {
                        case $model::TYPE_STRING:
                            echo $form
                                ->field($model, 'value')
                                ->textInput()
                                ->label(false);
                            break;
                        case $model::TYPE_BOOLEAN:
                            echo $form
                                ->field($model, 'value')
                                ->dropDownList([
                                    '0' => 'Нет',
                                    '1' => 'Да'
                                ])
                                ->label(false);
                            break;
                        case $model::TYPE_NUMBER:
                            echo $form
                                ->field($model, 'value')
                                ->textInput(['type' => 'number'])
                                ->label(false);
                            break;
                        case $model::TYPE_FILE:
                            echo InputFile::widget([
                                'language' => 'ru',
                                'controller' => 'elfinder',
                                'template' => '<div class="input-group">{input}<div class="input-group-btn">{button}</div></div>',
                                'options' => ['class' => 'form-control'],
                                'buttonOptions' => ['class' => 'btn btn-outline-secondary'],
                                'buttonName' => Yii::t('config', 'Выбрать файл'),
                                'name' => 'Config[value]',
                                'value' => $model->value
                            ]);
                            break;
                        case $model::TYPE_PASSWORD:
                            echo $form
                                ->field($model, 'value')
                                ->passwordInput()
                                ->label(false);
                            break;
                        case $model::TYPE_DATE:
                            echo $form
                                ->field($model, 'value')
                                ->widget(DateTimePicker::class, [
                                    'type' => DateTimePicker::TYPE_INPUT,
                                    'options' => ['placeholder' => 'Ввод даты/времени...'],
                                    'convertFormat' => false,
                                    'pluginOptions' => [
                                        'format' => 'yyyy-mm-dd hh:ii:ss',
                                        'autoclose' => true,
                                        'weekStart' => 1,
                                        'todayBtn' => true,
                                        'minView' => 2,
                                    ]
                                ])
                                ->label(false);
                            break;
                        default:
                            echo $form
                                ->field($model, 'value')
                                ->textInput()
                                ->label(false);
                    }
                    ?>
                </div>
            </div>
            <?php Pjax::end(); ?>
            <hr>
            <div class="card card-block">
                <div class="row">
                    <div class="col-md-12">
                        <p>
                            <a class="text-muted" data-bs-toggle="collapse" href="#permissions" aria-expanded="false"
                               aria-controls="collapseExample">
                                Права доступа >
                            </a>
                        </p>
                        <div class="row collapse" id="permissions">
                            <div class="col-md-12">
                                <p>
                                    <?= Yii::t('config', 'Здесь вы можете изменить права доступа к опции настройки') ?>
                                </p>
                                <p class="text-danger font-weight-bold">
                                    <?= Yii::t('config', 'Будьте осторожны! Вы можете утратить доступ к опции!') ?>
                                </p>
                                <p class="text-danger">
                                    <?= Yii::t('config', 'Если запрещено изменение, то редактирование так же невозможно!') ?>
                                </p>
                            </div>
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-lg-1 col-md-3 col-sm-6">
                                        <?= $form->field($model, 'readonly')->checkbox() ?>
                                    </div>
                                    <div class="col-md-3 col-sm-6">
                                        <?= $form->field($model, 'protected')->checkbox() ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-12">
                                <?= $form->field($model, 'canChange')->widget(Select2::class, [
                                    'data' => $authItems,
                                    'options' => ['placeholder' => ''],
                                    'pluginOptions' => [
                                        'tags' => false,
                                        'tokenSeparators' => false,
                                        'allowClear' => true
                                    ],
                                ]) ?>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-12">
                                <?= $form->field($model, 'canEdit')->widget(Select2::class, [
                                    'data' => $authItems,
                                    'options' => ['placeholder' => ''],
                                    'pluginOptions' => [
                                        'tags' => false,
                                        'tokenSeparators' => false,
                                        'allowClear' => true
                                    ],
                                ]) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
<div class="modal fade add-tab-modal" tabindex="-1" role="dialog"
     aria-hidden="true" style="display: none;">
    <?= $this->render('tab-form', [
        'model' => $tabModel,
        'action' => ['default/add-tab']
    ]) ?>
</div><!-- /.modal-dialog -->
</div>
