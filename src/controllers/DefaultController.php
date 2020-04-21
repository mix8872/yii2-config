<?php

namespace mix8872\config\controllers;

use mix8872\config\models\ConfigTab;
use Yii;
use mix8872\config\models\Config;
use yii\data\ActiveDataProvider;
use yii\base\Model;
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
                $this->redirect(['index']);
            }
        }

        return $this->render('index', [
            'dp' => $dp
        ]);
    }

    public function actionCreate()
    {
        $model = new Config();
        $model->type = strip_tags(trim(Yii::$app->request->get('type')));
        $tabs = ArrayHelper::map(self::getTabs('id DESC'), 'id', 'title');
        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                Yii::$app->getSession()->setFlash('success', 'Настройка добавлена');
                return $this->redirect(['index']);
            } else {
                Yii::$app->getSession()->setFlash('warning', 'Ошибка добавления: ' . print_r($model->getErrors()));
            }
        }

        if (Yii::$app->request->isAjax) {
            $model->tabId = array_key_first($tabs);
            return $this->renderAjax('create', [
                'model' => $model,
                'groups' => self::getGroups(),
                'tabs' => $tabs,
                'tabModel' => new ConfigTab()
            ]);
        }

        return $this->render('create', [
            'model' => $model,
            'groups' => self::getGroups(),
            'tabs' => $tabs,
            'tabModel' => new ConfigTab()
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $tabs = ArrayHelper::map(self::getTabs('id DESC'), 'id', 'title');
        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                Yii::$app->getSession()->setFlash('success', 'Настройка сохранена');
                return $this->redirect(['index']);
            } else {
                Yii::$app->getSession()->setFlash('warning', 'Ошибка сохранения: ' . print_r($model->getErrors()));
            }
        }

        if (Yii::$app->request->isAjax) {
            $model->tabId = array_key_first($tabs);
            $model->type = strip_tags(trim(Yii::$app->request->get('type'))) ?: $model->type;
            return $this->renderAjax('update', [
                'model' => $model,
                'groups' => self::getGroups(),
                'tabs' => $tabs,
                'tabModel' => new ConfigTab()
            ]);
        }

        return $this->render('update', [
            'model' => $model,
            'groups' => self::getGroups(),
            'tabs' => $tabs,
            'tabModel' => new ConfigTab()
        ]);
    }

    public function actionAddTab()
    {
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
        $model = ConfigTab::findOne((int)$id);
        if (Yii::$app->request->isAjax) {
            $action = ['default/update-tab', 'id' => $model->id];
            return $this->renderAjax('tab-form', compact('model', 'action'));
        } else {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                Yii::$app->session->setFlash('success', 'Вкладка сохранена');
            } else {
                Yii::$app->session->setFlash('error', 'Ошибка сохранения вкладки');
            }
            return $this->redirect(['index']);
        }
    }

    public function actionSortTabs()
    {
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
        if (ConfigTab::findOne((int)$id)->delete() !== false) {
            Yii::$app->session->setFlash('success', 'Вкладка удалена');
        } else {
            Yii::$app->session->setFlash('error', 'Ошибка удаления вкладки');
        }
        $this->redirect(['index']);
    }

    public function actionDelete($id)
    {
        if ($this->findModel($id)->delete()) {
            Yii::$app->getSession()->setFlash('success', 'Настройка удалена');
        } else {
            Yii::$app->getSession()->setFlash('error', 'Ошибка удаления');
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
}
