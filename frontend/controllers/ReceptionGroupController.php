<?php

namespace frontend\controllers;

use common\models\ReceptionExam;
use Yii;
use common\models\ReceptionGroup;
use frontend\search\ReceptionGroupSearch;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ReceptionGroupController implements the CRUD actions for ReceptionGroup model.
 */
class ReceptionGroupController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => [
                            'index', 'view',
                            'create', 'update',
                            'delete'
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all ReceptionGroup models.
     * @return mixed
     */
    public function actionIndex($commission_id)
    {
        $searchModel = new ReceptionGroupSearch();
        $searchModel->commission_id = $commission_id;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ReceptionGroup model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        $examDataProvider = new ActiveDataProvider([
            'query' => ReceptionExam::find()
                ->joinWith([
                    /** @see ReceptionExam::getReceptionGroups() */
                    'receptionGroups' => function (ActiveQuery $query) use ($model) {
                        return $query->andWhere([
                            ReceptionGroup::tableName() . '.id' => $model->id,
                        ]);
                    },
                ])
                ->with([
                    /** @see ReceptionExam::getInstitutionDiscipline() */
                    'institutionDiscipline'
                ]),
        ]);

        return $this->render('view', [
            'model' => $model,
            'examDataProvider' => $examDataProvider,
        ]);
    }

    /**
     * Creates a new ReceptionGroup model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id)
    {
        $model = new ReceptionGroup();
        $model->commission_id = $id;
        $specialities = Yii::$app->user->identity->institution->specialities;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'commission_id' => $id]);
        }

        return $this->render('create', [
            'model' => $model,
            'specialities' => $specialities
        ]);
    }

    /**
     * Updates an existing ReceptionGroup model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $specialities = Yii::$app->user->identity->institution->specialities;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'commission_id' => $model->commission_id]);
        }

        return $this->render('update', [
            'model' => $model,
            'specialities' => $specialities
        ]);
    }

    /**
     * Deletes an existing ReceptionGroup model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ReceptionGroup model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ReceptionGroup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ReceptionGroup::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
