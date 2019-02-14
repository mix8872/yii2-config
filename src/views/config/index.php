<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\grid\GridView;
use mix8872\admin\models\Config;
use yii\jui\AutoComplete;
use mihaildev\elfinder\InputFile;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Настройки';
$this->params['breadcrumbs'][] = $this->title;

\mix8872\admin\assets\ConfigAsset::register($this);
?>
<div class="settings-index panel panel-default">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
    <div class="panel-heading">
        <div class="form-group pull-right">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>
    <div class="panel-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'showHeader' => false,
            'summary' => '',
            'striped' => true,
            'hover' => false,
            'pjax' => true,
            'rowOptions' => [
                'class' => 'config-option_row'
            ],
            'columns' => [
//                ['class' => 'yii\grid\SerialColumn'],

//            'group',
                [
                    'attribute' => 'group',
                    'value' => function ($model, $key, $index, $widget) {
                        return $model->group;
                    },
                    'group' => true,  // enable grouping,
                    'groupedRow' => true,                    // move grouped column to a single grouped row
                    'groupEvenCssClass' => 'kv-grouped-row kv-group-odd', // configure even group cell css class
                    'groupOddCssClass' => 'kv-grouped-row kv-group-odd', // configure even group cell css class
                ],
                [
                    'attribute' => 'key',
                    'format' => 'raw',
                    'value' => function ($model) use ($form, $groups) {
                        return Html::tag('div', $model->key, ['class' => 'config-option_name'])
                            . Html::tag('div', $form->field($model, '[' . $model->id . ']group')->widget(
                                    AutoComplete::className(), [
                                    'clientOptions' => [
                                        'source' => $groups,
                                    ],
                                    'options'=>[
                                        'class'=>'form-control'
                                    ]
                                ]) . $form->field($model, '[' . $model->id . ']key')->textInput()->label(false), ['class' => 'config-option_hidden-input']);
                    }
                ],
                [
                    'attribute' => 'name',
                    'format' => 'raw',
                    'value' => function ($model) use ($form) {
                        return Html::tag('div', $model->name, ['class' => 'config-option_name'])
                            . Html::tag('div', $form->field($model, '[' . $model->id . ']name')->textInput()->label(false), ['class' => 'config-option_hidden-input']);
                    }
                ],
                [
                    'attribute' => 'value',
                    'format' => 'raw',
                    'value' => function ($model) use ($form) {
                        switch ($model->type) {
                            case $model::TYPE_STRING:
								$params = [];
                                if ($model->readonly) {
                                    $params['disabled'] = 'disabled';
                                }
                                return $form
                                    ->field($model, '[' . $model->id . ']value')
                                    ->textInput($params)
                                    ->label(false);
                            case $model::TYPE_BOOLEAN:
                                return $form
                                    ->field($model, '[' . $model->id . ']value')
                                    ->dropDownList([
                                        '0' => 'Нет',
                                        '1' => 'Да'
                                    ])
                                    ->label(false);
                            case $model::TYPE_NUMBER:
                                $params = [
                                    'type' => 'number'
                                ];
                                if ($model->readonly) {
                                    $params['disabled'] = 'disabled';
                                }
                                return $form
                                    ->field($model, '[' . $model->id . ']value')
                                    ->textInput($params)
                                    ->label(false);
                            case $model::TYPE_FILE:
								 return InputFile::widget([
									'language' => 'ru',
                                    'controller' => 'elfinder', // вставляем название контроллера, по умолчанию равен elfinder
                                    'template' => '<div class="input-group">{input}<div class="input-group-btn">{button}</div></div>',
                                    'options' => ['class' => 'form-control'],
                                    'buttonOptions' => ['class' => 'btn btn-outline-secondary'],
                                    'buttonName' => Yii::t('admin','Выбрать файл'),
                                    'name' => 'Config[' . $model->id . '][value]',
                                     'value' => $model->value
                                ]);
                            case $model::TYPE_PASSWORD:
                                return $form
                                    ->field($model, '[' . $model->id . ']value')
                                    ->passwordInput()
                                    ->label(false);
                            default:
							$params = [];
                                if ($model->readonly) {
                                    $params['disabled'] = 'disabled';
                                }
                                return $form
                                    ->field($model, '[' . $model->id . ']value')
                                    ->textInput($params)
                                    ->label(false);
                        }
                    }
                ],
                [
                    'class' => 'kartik\grid\ActionColumn',
                    'header' => '',
                    'template' => '{update} {delete}',
                    'width' => '50px',
                    'buttons' => [
                        'update' => function ($url, $model) {
                            return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, ['class' => 'config-option_update', 'data-id' => $model->id]);
                        },
                    ]
                ]
            ],
        ]); ?>
    </div>
    <?php ActiveForm::end(); ?>
    <?= Html::button('Добавить', ['class' => 'btn pull-right', 'data' => [
        'toggle' => 'modal',
        'target' => '.config-type-select'
    ]]) ?>

    <div class="modal fade config-type-select" tabindex="-1" role="dialog" aria-labelledby="modal-label" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="modal-label">Выберите тип опции</h4>
                </div>
                <div class="modal-body">
                    <div class="content-center">
                        <?= Html::a('Строка', ['create', 'type' => Config::TYPE_STRING], ['class' => 'btn btn-success']) ?>
                        <?= Html::a('Да/Нет', ['create', 'type' => Config::TYPE_BOOLEAN], ['class' => 'btn btn-success']) ?>
                        <?= Html::a('Число', ['create', 'type' => Config::TYPE_NUMBER], ['class' => 'btn btn-success']) ?>
                        <?= Html::a('Пароль', ['create', 'type' => Config::TYPE_PASSWORD], ['class' => 'btn btn-success']) ?>
                        <?= Html::a('Файл', ['create', 'type' => Config::TYPE_FILE], ['class' => 'btn btn-success']) ?>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
</div>
