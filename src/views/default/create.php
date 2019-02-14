<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\AutoComplete;

$type = strip_tags(trim(Yii::$app->request->get('type')));

$form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
<div class="panel panel-default">
    <div class="panel-heading">
		<div class="form-group pull-right">
			<?= Html::a(Yii::t('admin','Назад'), ['/admin/config'], ['class' => 'btn btn-warning']) ?>
            <?= Html::submitButton(Yii::t('admin','Добавить'), ['class' => 'btn btn-success']) ?>
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
			case $model::TYPE_STRING: ?>
			<div class="input-group">
				<?= $form->field($model, 'value')->textInput() ?>
				<div class="input-group-addon">
					<?= $form->field($model, 'readonly')->checkbox() ?>
				</div>
			</div>
				<?php break;
			case $model::TYPE_NUMBER:
				echo $form
					->field($model, 'value')
					->textInput(['type' => 'number']);
					
				echo $form->field($model, 'readonly')->checkbox();
				break;
			default:
				echo $form
					->field($model, 'value')
					->textInput();

				echo $form->field($model, 'readonly')->checkbox();
				break;
		}
		?>
    </div>
</div>
<?php ActiveForm::end(); ?>
