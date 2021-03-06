<?php

namespace frontend\controllers;

use common\services\person\EntrantService;
use Yii;
use common\models\reception\AppealApplication;
use frontend\search\AppealApplicationSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\base\Module;

/**
 * AppealApplicationController implements the CRUD actions for AppealApplication model.
 */
class AppealApplicationController extends Controller
{
    private $entrantService;
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function __construct(
        string $id,
        Module $module,
        EntrantService $entrantService,
        array $config = [])
    {
        $this->entrantService = $entrantService;
        parent::__construct($id, $module, $config);
    }

    /**
     * Lists all AppealApplication models.
     * @return mixed
     */
    public function actionIndex($commission_id)
    {
        $searchModel = new AppealApplicationSearch();
        $searchModel->appeal_commission_id = $commission_id;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AppealApplication model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new AppealApplication model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id)
    {
        $model = new AppealApplication();
        $model->appeal_commission_id = $id;
        $model->status = AppealApplication::STATUS_NEW;
        $entrants = $this->entrantService->getCommissionEntrants(
            Yii::$app->user->identity->institution,
            $model->appealCommission->commission
        );

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'entrants' => $entrants
        ]);
    }

    /**
     * Updates an existing AppealApplication model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $entrants = $this->entrantService->getCommissionEntrants(
            Yii::$app->user->identity->institution,
            $model->appealCommission->commission
        );

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'entrants' => $entrants
        ]);
    }

    public function actionAccept($id) {
        $model = $this->findModel($id);
        $model->status = AppealApplication::STATUS_ACCEPTED;
        $model->save();

        return $this->redirect(['index', 'commission_id' => $model->appeal_commission_id]);
    }

    public function actionReject($id) {
        $model = $this->findModel($id);
        $model->status = AppealApplication::STATUS_REJECTED;
        $model->save();

        return $this->redirect(['view', 'commission_id' => $model->appeal_commission_id]);
    }

    /**
     * Deletes an existing AppealApplication model.
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
     * Finds the AppealApplication model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AppealApplication the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AppealApplication::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
