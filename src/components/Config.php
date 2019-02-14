<?php

namespace mix8872\config\components;

use Yii;
use mix8872\config\models\Config as ConfigModel;

class Config extends \yii\base\Component
{
    public function init()
    {
        parent::init();

        $mailer = Yii::$app->mailer;
        if ($this->g('use_smtp')) {
            if ($this->g('smtp_secure')) {
                $port = $this->g('smtp_port') ?: '465';
                $encryption = 'ssl';
            } else {
                $port = '25';
                $encryption = false;
            }
            $mailer->transport = [
                'class' => 'Swift_SmtpTransport',
                'host' => $this->g('smtp_server'),
                'username' => $this->g('smtp_login'),
                'password' => $this->g('smtp_pass'),
                'port' => $port,
                'encryption' => $encryption,
            ];
        }
    }

    /**
     * @param string $group
     * @return mixed
     */
    public function getGroup(string $group)
    {
        return ConfigModel::find()->where('group = :group', [':group' => $group])->all();
    }

    /**
     * Get config parameter value
     * @param $param
     * @return false|null|string
     */
    public function g(string $param)
    {
        return $this->__get($param);
    }

    /**
     * @param string $param
     * @return false|mixed|null|string
     */
    public function __get($param)
    {
        if (is_string($param)) {
            return ConfigModel::find()->select('value')->where(['key' => $param])->scalar();
        }
        return false;
    }

    /**
     * Set config parameter value
     * If argument is string, then this string will explodes by comma and first argument will given as parameter key
     * If argument is array than array can be in two variants:
     * * first - indexed array, contains 2 items key and value
     * * second - associative array from one item where key of item - as param key, and value - as param value
     * @param mixed $param
     * @param mixed $val
     * @return bool
     */
    public function s(mixed $param, $val = false): bool
    {
        switch (true) {
            case $val:
                $params = array();
                $params['key'] = $param;
                $params['value'] = $val;
                break;
            case is_string($param):
                $exp = explode(',', trim($param));
                $params = array();
                if (count($exp) === 2) {
                    $params['key'] = $exp[0];
                    $params['value'] = $exp[1];
                }
                break;
            default:
                $params = $param;
        }
        if (is_array($params)) {
            $key = null;
            $value = null;
            if (count($params) === 2 && isset($params['key'], $params['value'])) {
                $key = $params['key'];
                $value = $params['value'];
            } elseif (count($params) === 1) {
                $key = array_key_first($params);
                $value = array_shift($params);
            }
            if (is_string($key)) {
                return $this->__set($key, $value);
            }
        }
        return false;
    }

    /**
     * @param string $key
     * @param string $value
     * @return bool|void
     */
    public function __set($key, $value)
    {
        if (is_string($key)) {
            $model = ConfigModel::find()->where('key = :key', [':key' => $key])->one();
            if ($model) {
                $model->value = trim($value);
                return $model->save();
            }
        }
        return false;
    }

    /**
     * @param $param
     * @return bool
     */
    public function __isset($param)
    {
        if (is_string($param)) {
            return ConfigModel::find()->where('key = :param', [':param' => $param])->exists();
        }
        return false;
    }
}
