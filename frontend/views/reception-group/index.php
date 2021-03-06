<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel frontend\search\ReceptionGroupSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Groups');
$this->params['breadcrumbs'][] = $this->title;
?>

<div style="position: relative;">
    <h1><?=$this->title?></h1>
    <?= Html::a('Добавить', ['create', 'id' => $searchModel->commission_id], ['class' => 'title-action btn btn-primary']) ?>
</div>

<div class="reception-group-index skin-white">
    <div class="card-body">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

//            'id',
            [
                'format' => 'html',
                'attribute' => 'caption_current',
                'value' => function (\common\models\ReceptionGroup $model) {
                    if ($model->caption_current !== null) {
                        return Html::a($model->caption_current, ['view', 'id' => $model->id]);
                    }
                }
            ],
            [
                'format'    => 'html',
                'attribute' => 'language',
                'value'     => function (\common\models\ReceptionGroup $model) {
                    if ($model->language !== null) {
                        return $model->getLanguage();
                    }
                },
            ],
            [
                'format'    => 'html',
                'attribute' => 'speciality_id',
                'value'     => function (\common\models\ReceptionGroup $model) {
                    if ($model->speciality !== null) {
                        return $model->speciality->getCaptionWithCode() ?? null;
                    }
                },
            ],
            [
                'format'    => 'html',
                'attribute' => 'education_form',
                'value'     => function (\common\models\ReceptionGroup $model) {
                    if ($model->education_form !== null) {
                        return $model->getEducationPayForm() ?? null;
                    }
                },
            ],
//            'education_form',
            //'institution_id',
            //'budget_places',
            //'commercial_places',
            //'create_ts',
            //'update_ts',
            //'delete_ts',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
            ],
        ],
    ]); ?>
    </div>
</div>
