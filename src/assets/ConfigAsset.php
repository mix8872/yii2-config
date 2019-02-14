<?php
namespace mix8872\config\assets;

class ConfigAsset extends \yii\web\AssetBundle
{
	public $sourcePath = '@vendor/mix8872/yii2-config/src/assets';
    public $css = [
		  'css/config.css',
    ];
    public $js = [
		  'js/config.js'
    ];
    
    public $depends = [
		  'backend\assets\AppAsset',
    ];
}