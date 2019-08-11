<?php

$this->title = Yii::t('config', 'Редактирование элемента: ') . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Настройки', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo $this->render('_form', compact('model', 'groups'));
