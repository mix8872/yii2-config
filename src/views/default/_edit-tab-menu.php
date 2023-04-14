<?php

use yii\helpers\Html;

$btn = Html::tag('i', '', [
    'class' => 'dropdown-toggle js-tab-more' . ($i === 0 ? '' : ' hidden'),
    'type' => '',
    'data' => [
        'bs-target' => "#{$slug}-tab-link",
        'bs-toggle' => 'dropdown',
    ],
    'aria' => [
        'haspopup' => 'true',
        'expanded' => 'false'
    ],
]);
$update = Html::a('Редактировать ' . Html::tag('i', '', ['class' => 'fa fas fa-pencil fa-pencil-alt']),
    ['default/update-tab', 'id' => $tabId],
    ['class' => 'text-muted dropdown-item js-tab-edit']);
$delete = Html::a('Удалить ' . Html::tag('i', '', ['class' => 'fa fas fa-trash']),
    ['default/delete-tab', 'id' => $tabId],
    [
        'class' => 'text-danger dropdown-item',
        'data' => [
            'method' => 'POST',
            'confirm' => 'Вы действительно хотите удалить данный элемент?',
            'pjax' => 0,
        ],
    ]);
$dropDown = "$btn <div class=\"dropdown-menu\">{$update}{$delete}</div>";


echo $dropDown;
