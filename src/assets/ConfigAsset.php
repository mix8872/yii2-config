<?php
namespace mix8872\config\assets;

class ConfigAsset extends \yii\web\AssetBundle
{
	public $sourcePath = '@vendor/mix8872/admin/src/assets';
    public $css = [
		'css/config.css',
    ];
    public $js = [
		'js/config.js'
    ];
    
    public $depends = [
		'mix8872\admin\assets\MainAsset',
    ];
}