<?php

use kartik\datetime\DateTimePicker;
use mix8872\config\Module;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\helpers\Url;
use yii\jui\Sortable;
use yii\widgets\ActiveForm;
use kartik\grid\GridView;
use mix8872\config\models\Config;
use mihaildev\elfinder\InputFile;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('config', 'Настройки');
$this->params['breadcrumbs'][] = $this->title;

\mix8872\config\assets\ConfigAsset::register($this);
?>
<?php $form = ActiveForm::begin(['id' => 'config-form', 'options' => ['enctype' => 'multipart/form-data']]); ?>
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
                    <?= Html::submitButton(Html::tag('i', '', ['class' => 'fa fa-save']) . Yii::t('config', ' Сохранить'), ['class' => 'btn btn-primary']) ?>
                    <?php if (Yii::$app->controller->checkAccess(Module::ACTION_MANAGE)) : ?>
                        <?= Html::button(Html::tag('i', '', ['class' => 'fa fa-plus']) . Yii::t('config', ' Добавить'), ['class' => 'btn btn-light', 'data' => [
                            'toggle' => 'modal',
                            'target' => '.config-type-select'
                        ]]) ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card-box">
            <?php
            $i = 0;
            $items = [];
            ?>
            <?php foreach ($dp as $tabId => $data): if ($data['dp']->count): ?>
                <?php
                $slug = Inflector::slug($data['title'], '-');
                if (Yii::$app->controller->checkAccess(Module::ACTION_MANAGE)) {
                    $dropDown = $this->render('_edit-tab-menu', compact('i', 'slug', 'tabId'));
                } else {
                    $dropDown = '';
                }
                $text = $data['title'];
                $items[] = Html::tag('div', "$text $dropDown", [
                    'class' => 'nav-link' . ($i++ === 0 ? ' active' : ''),
                    'style' => 'cursor:pointer',
                    'id' => "{$slug}-tab-link",
                    'role' => 'tab',
                    'data' => [
                        'target' => "#$slug",
                        'toggle' => 'tab',
                        'id' => $tabId
                    ],
                    'aria' => [
                        'controls' => 'nav-home'
                    ],

                ]);
            endif; endforeach; ?>
            <?= Sortable::widget([
                'items' => $items,
                'id' => 'sortable-tabs',
                'options' => [
                    'tag' => 'ul',
                    'class' => 'nav nav-tabs tabs tabs-top',
                    'data-sort' => Url::to(['default/sort-tabs'])
                ],
                'itemOptions' => [
                    'tag' => 'li',
                    'class' => 'nav-item dropdown js-tabs',
                ],
                'clientOptions' => ['cursor' => 'move'],
                'clientEvents' => [
                    'stop' => 'sort'
                ],
            ]) ?>
            <div class="tab-content" id="myTabContent">
                <?php $i = 0; ?>
                <?php foreach ($dp as $tabId => $data): if ($data['dp']->count): ?>
                    <?php
                    $slug = Inflector::slug($data['title'], '-');
                    ?>
                    <div class="tab-pane <?= $i++ === 0 ? 'active' : '' ?>" id="<?= $slug ?>"
                         role="tabpanel"
                         aria-labelledby="<?= $slug ?>-tab">
                        <?= GridView::widget([
                            'dataProvider' => $data['dp'],
                            'showHeader' => false,
                            'summary' => '',
                            'striped' => true,
                            'hover' => false,
                            'pjax' => true,
                            'rowOptions' => [
                                'class' => 'config-option_row'
                            ],
                            'columns' => [
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
                                'key',
                                'name',
                                [
                                    'attribute' => 'value',
                                    'format' => 'raw',
                                    'value' => function ($model) use ($form) {
                                        if (!Yii::$app->controller->checkAccess(Module::ACTION_CHANGE, $model)) {
                                            switch ($model->type) {
                                                case $model::TYPE_BOOLEAN:
                                                    return $model->value ? 'Да' : 'Нет';
                                                case $model::TYPE_PASSWORD:
                                                    return '*********';
                                                default:
                                                    return $model->value;
                                            }
                                        }
                                        switch ($model->type) {
                                            case $model::TYPE_BOOLEAN:
                                                return $form
                                                    ->field($model, '[' . $model->id . ']value')
                                                    ->dropDownList([
                                                        '0' => 'Нет',
                                                        '1' => 'Да'
                                                    ])
                                                    ->label(false);
                                            case $model::TYPE_NUMBER:
                                                return $form
                                                    ->field($model, '[' . $model->id . ']value')
                                                    ->textInput(['type' => 'number'])
                                                    ->label(false);
                                            case $model::TYPE_FILE:
                                                return InputFile::widget([
                                                    'language' => 'ru',
                                                    'controller' => 'elfinder', // вставляем название контроллера, по умолчанию равен elfinder
                                                    'template' => '<div class="input-group">{input}<div class="input-group-btn">{button}</div></div>',
                                                    'options' => ['class' => 'form-control'],
                                                    'buttonOptions' => ['class' => 'btn btn-outline-secondary'],
                                                    'buttonName' => Yii::t('config', 'Выбрать файл'),
                                                    'name' => 'Config[' . $model->id . '][value]',
                                                    'value' => $model->value
                                                ]);
                                            case $model::TYPE_PASSWORD:
                                                return $form
                                                    ->field($model, '[' . $model->id . ']value')
                                                    ->passwordInput()
                                                    ->label(false);
                                            case $model::TYPE_DATE:
                                                return $form
                                                    ->field($model, '[' . $model->id . ']value')
                                                    ->widget(DateTimePicker::class, [
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
                                                    ])
                                                    ->label(false);
                                            default:
                                                return $form
                                                    ->field($model, '[' . $model->id . ']value')
                                                    ->textInput()
                                                    ->label(false);
                                        }
                                    }
                                ],
                                [
                                    'class' => 'kartik\grid\ActionColumn',
                                    'header' => '',
                                    'template' => '{update} {delete}',
                                    'visibleButtons' => [
                                        'update' => function ($model) {
                                            return Yii::$app->controller->checkAccess(Module::ACTION_EDIT, $model);
                                        },
                                        'delete' => function ($model) {
                                            return $model->protected ? !$model->protected : Yii::$app->controller->checkAccess(Module::ACTION_DELETE, $model);
                                        }
                                    ]
                                ]
                            ],
                        ]) ?>
                    </div>
                <?php endif; endforeach; ?>
            </div>

        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>

<div class="modal fade config-type-select" tabindex="-1" role="dialog" aria-labelledby="modal-label"
     aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modal-label">Выберите тип опции</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="content-center">
                    <?= Html::a(Yii::t('config', Config::$types[Config::TYPE_STRING]), ['create', 'type' => Config::TYPE_STRING], ['class' => 'btn btn-success']) ?>
                    <?= Html::a(Yii::t('config', Config::$types[Config::TYPE_BOOLEAN]), ['create', 'type' => Config::TYPE_BOOLEAN], ['class' => 'btn btn-success']) ?>
                    <?= Html::a(Yii::t('config', Config::$types[Config::TYPE_NUMBER]), ['create', 'type' => Config::TYPE_NUMBER], ['class' => 'btn btn-success']) ?>
                    <?= Html::a(Yii::t('config', Config::$types[Config::TYPE_PASSWORD]), ['create', 'type' => Config::TYPE_PASSWORD], ['class' => 'btn btn-success']) ?>
                    <?= Html::a(Yii::t('config', Config::$types[Config::TYPE_FILE]), ['create', 'type' => Config::TYPE_FILE], ['class' => 'btn btn-success']) ?>
                    <?= Html::a(Yii::t('config', Config::$types[Config::TYPE_DATE]), ['create', 'type' => Config::TYPE_DATE], ['class' => 'btn btn-success']) ?>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
