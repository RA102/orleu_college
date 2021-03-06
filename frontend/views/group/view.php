<?php

use common\models\person\Student;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\organization\Group */

$this->title = Yii::t('app', 'View Group');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Groups'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<h1><?= Html::encode($this->title) ?></h1>

<div class="group-view skin-white">

    <div class="card-body">

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                'caption_current',
                [
                    'format'    => 'html',
                    'attribute' => 'language',
                    'value'     => function (\common\models\organization\Group $model) {
                        return $model->getLanguage();
                    },
                ],
                [
                    'format'    => 'html',
                    'attribute' => 'speciality_id',
                    'value'     => function (\common\models\organization\Group $model) {
                        return $model->speciality->caption_current;
                    },
                ],
                'max_class',
                'class',
                [
                    'format'    => 'html',
                    'attribute' => 'education_form',
                    'value'     => function (\common\models\organization\Group $model) {
                        return $model->getEducationForm();
                    },
                ],
                [
                    'format'    => 'html',
                    'attribute' => 'education_pay_form',
                    'value'     => function (\common\models\organization\Group $model) {
                        return $model->getEducationPayForm();
                    },
                ],
            ],
        ]) ?>

        <h3><?=Yii::t('app', 'Students')?></h3>
        <?= GridView::widget([
            'dataProvider' => $studentsDataProvider,
            'filterModel' => $studentsSearch,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                'firstname',
                'lastname',
                'middlename',
                'birth_date',
                'iin',

                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view}',
                    'buttons' => [
                        'view' => function ($url, Student $model) {
                            return Html::a(
                                    '<span class="glyphicon glyphicon-eye-open"></span>',
                                ['/student/view', 'id' => $model->id]
                            );
                        }
                    ],
                ],
            ],
        ]); ?>

        <p>
            <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
        </p>
    </div>

</div>
