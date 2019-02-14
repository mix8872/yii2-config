<?php

namespace mix8872\config\controllers;

use Yii;
use mix8872\admin\models\Config;
use yii\data\ActiveDataProvider;
use yii\base\Model;

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
        $dataProvider = new ActiveDataProvider([
            'query' => Config::find()->orderBy('group'),
        ]);
        $groups = Config::find()->select(['group as value','group as label'])->asArray()->distinct()->all();

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
            'dataProvider' => $dataProvider,
            'groups' => $groups
        ]);
    }

    public function actionCreate()
    {
        $model = new Config();
        $groups = Config::find()->select(['group as value','group as label'])->asArray()->distinct()->all();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', 'Настройка добавлена');
            return $this->redirect(['index']);
        } else {
//            Yii::$app->getSession()->setFlash('warning', 'Ошибка добавления: ' . print_r($model->getErrors()));
        }

        return $this->render('create', [
            'model' => $model,
            'groups' => $groups
        ]);
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
}
