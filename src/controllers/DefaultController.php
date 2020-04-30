<?php

namespace mix8872\config\controllers;

use mix8872\config\components\Event;
use mix8872\config\models\ConfigTab;
use mix8872\config\Module;
use Yii;
use mix8872\config\models\Config;
use yii\data\ActiveDataProvider;
use yii\base\Model;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\HtmlPurifier;
use yii\web\Response;

/**
 * SettingsController implements the CRUD actions for Settings model.
 */
class DefaultController extends \yii\web\Controller
{
    /**
     * Lists all Settings models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dp = [];
        $tabs = self::getTabs();

        foreach ($tabs as $tab) {
            $dp[$tab->id]['title'] = HtmlPurifier::process($tab->title);
            $dp[$tab->id]['dp'] = new ActiveDataProvider([
                'query' => $tab->getConfigs(),
                'pagination' => [
                    'pageSize' => 100,
                ]
            ]);
        }

        if (Yii::$app->request->isPost) {
            $settings = Config::find()->indexBy('id')->all();

            if (Model::loadMultiple($settings, Yii::$app->request->post()) && Model::validateMultiple($settings)) {
                foreach ($settings as $item) {
                    $item->save();
                }
                $event = new Event(['model' => $settings]);
                $this->module->trigger(Module::EVENT_AFTER_UPDATE, $event);
                $this->redirect(['index']);
            }
        }

        return $this->render('index', [
            'dp' => $dp
        ]);
    }

    public function actionCreate()
    {
        if (!$this->checkAccess(Module::ACTION_MANAGE)) {
            Yii::$app->session->setFlash('error', Yii::t('config', 'Недостаточно прав!'));
            return $this->redirect(['index']);
        }
        $model = new Config();
        $model->type = strip_tags(trim(Yii::$app->request->get('type')));
        $tabs = ArrayHelper::map(self::getTabs('id DESC'), 'id', 'title');
        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                $event = new Event(['model' => $model]);
                $this->module->trigger(Module::EVENT_AFTER_CREATE, $event);
                Yii::$app->getSession()->setFlash('success', Yii::t('config', 'Настройка добавлена'));
                return $this->redirect(['index']);
            } else {
                Yii::$app->getSession()->setFlash('warning', Yii::t('config', 'Ошибка добавления: ') . print_r($model->getErrors()));
            }
        }

        if (Yii::$app->request->isAjax) {
            $model->tabId = array_key_first($tabs);
            return $this->renderAjax('create', [
                'model' => $model,
                'groups' => self::getGroups(),
                'tabs' => $tabs,
                'tabModel' => new ConfigTab(),
                'authItems' => self::getAuthItems()
            ]);
        }

        return $this->render('create', [
            'model' => $model,
            'groups' => self::getGroups(),
            'tabs' => $tabs,
            'tabModel' => new ConfigTab(),
            'authItems' => self::getAuthItems()
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if (!$this->checkAccess(Module::ACTION_EDIT, $model)) {
            Yii::$app->session->setFlash('error', Yii::t('config', 'Недостаточно прав!'));
            return $this->redirect(['index']);
        }
        $tabs = ArrayHelper::map(self::getTabs('id DESC'), 'id', 'title');
        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                $event = new Event(['model' => $model]);
                $this->module->trigger(Module::EVENT_AFTER_UPDATE, $event);
                Yii::$app->getSession()->setFlash('success', Yii::t('config', 'Настройка сохранена'));
                return $this->redirect(['index']);
            } else {
                Yii::$app->getSession()->setFlash('warning', Yii::t('config', 'Ошибка сохранения: ') . print_r($model->getErrors()));
            }
        }

        if (Yii::$app->request->isAjax) {
            $model->tabId = array_key_first($tabs);
            $model->type = strip_tags(trim(Yii::$app->request->get('type'))) ?: $model->type;
            return $this->renderAjax('update', [
                'model' => $model,
                'groups' => self::getGroups(),
                'tabs' => $tabs,
                'tabModel' => new ConfigTab(),
                'authItems' => self::getAuthItems()
            ]);
        }

        return $this->render('update', [
            'model' => $model,
            'groups' => self::getGroups(),
            'tabs' => $tabs,
            'tabModel' => new ConfigTab(),
            'authItems' => self::getAuthItems()
        ]);
    }

    public function actionAddTab()
    {
        if ($this->module->adminRole && !Yii::$app->user->can($this->module->adminRole)) {
            Yii::$app->session->setFlash('error', Yii::t('config', 'Недостаточно прав!'));
            return [
                'success' => false,
                'errors' => Yii::t('config', 'Недостаточно прав')
            ];
        }
        Yii::$app->request->isAjax || die();
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new ConfigTab();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return [
                'success' => true
            ];
        } else {
            return [
                'success' => false,
                'errors' => $model->getErrors()
            ];
        }
    }

    public function actionUpdateTab($id)
    {
        if ($this->module->adminRole && !Yii::$app->user->can($this->module->adminRole)) {
            Yii::$app->session->setFlash('error', Yii::t('config', 'Недостаточно прав!'));
            return $this->redirect(['index']);
        }
        $model = ConfigTab::findOne((int)$id);
        if (Yii::$app->request->isAjax) {
            $action = ['default/update-tab', 'id' => $model->id];
            return $this->renderAjax('tab-form', compact('model', 'action'));
        } else {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                Yii::$app->session->setFlash('success', Yii::t('config', 'Вкладка сохранена'));
            } else {
                Yii::$app->session->setFlash('error', Yii::t('config', 'Ошибка сохранения вкладки'));
            }
            return $this->redirect(['index']);
        }
    }

    public function actionSortTabs()
    {
        if ($this->module->adminRole && !Yii::$app->user->can($this->module->adminRole)) {
            return [
                'success' => false,
                'errors' => Yii::t('config', 'Недостаточно прав!')
            ];
        }
        Yii::$app->request->isAjax || die();
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($items = Yii::$app->request->post('items')) {
            $result = ConfigTab::sort(json_decode($items, true));
            return [
                'success' => true
            ];
        }
    }

    public function actionDeleteTab($id)
    {
        if ($this->module->adminRole && !Yii::$app->user->can($this->module->adminRole)) {
            Yii::$app->session->setFlash('error', Yii::t('config', 'Недостаточно прав!'));
            return $this->redirect(['index']);
        }
        if (ConfigTab::findOne((int)$id)->delete() !== false) {
            Yii::$app->session->setFlash('success', Yii::t('config', 'Вкладка удалена'));
        } else {
            Yii::$app->session->setFlash('error', Yii::t('config', 'Ошибка удаления вкладки'));
        }
        $this->redirect(['index']);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if (!$this->checkAccess(Module::ACTION_DELETE, $model)) {
            Yii::$app->session->setFlash('error', Yii::t('config', 'Недостаточно прав!'));
            return $this->redirect(['index']);
        }
        if ($model->delete()) {
            $event = new Event(['model' => $model]);
            $this->module->trigger(Module::EVENT_AFTER_DELETE, $event);
            Yii::$app->getSession()->setFlash('success', Yii::t('config', 'Настройка удалена'));
        } else {
            Yii::$app->getSession()->setFlash('error', Yii::t('config', 'Ошибка удаления'));
        }
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = Config::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected static function getGroups()
    {
        $groups = Config::find()->select(['group as value', 'group as label'])->asArray()->distinct()->all();
        return ArrayHelper::map($groups, 'value', 'label');
    }

    protected static function getTabs($order = 'order ASC')
    {
        return ConfigTab::find()->orderBy($order)->all();
    }

    protected static function getAuthItems()
    {
        if (\Yii::$app->db->getTableSchema('{{%auth_item}}', true) !== null) {
            $items = (new Query())->select('name')->from('auth_item')->orderBy('type')->all();
            return ArrayHelper::map($items, 'name', 'name');
        }
        return [];
    }

    public function checkAccess($action, $model = null)
    {
        $user = Yii::$app->user;
        $factors = [];
        if ($this->module->adminRole) {
            $factors['adminAccess'] = $user->can($this->module->adminRole);
        }
        if (!$model && $action !== Module::ACTION_MANAGE) {
            return false;
        }

        switch ($action) {
            case Module::ACTION_MANAGE:
                return $factors['adminAccess'] ?? true;
            case Module::ACTION_CHANGE:
                $matrix = [
                    'or',
                    'readonly' => 0,
                    'canChange' => 0,
                    'canEdit' => 1
                ];
                break;
            case Module::ACTION_EDIT:
                $matrix = [
                    'or',
                    'protected' => 0,
                    'readonly' => 0,
                    'canChange' => 1,
                    'canEdit' => 0
                ];
                break;
            case Module::ACTION_DELETE:
                $matrix = [
                    'and',
                    'protected' => 0,
                    'readonly' => 0,
                ];
                break;
            default:
                return true;
        }

        $logick = array_shift($matrix);
        foreach ($matrix as $key => $item) {
            if ($item) {
                if ($model->{$key} && !$user->can($model->{$key})) {
                    $factors[$key] = false;
                }
            } else {
                if ($model->{$key}) {
                    $factors[$key] = $user->can($model->{$key});
                }
            }
        }

        if (!$count = count($factors)) {
            return true;
        }

        $res = $logick === 'or' ? array_sum($factors) >= 1 : array_sum($factors) === $count;

        return $res;
    }
}
