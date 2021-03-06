<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Commissions');
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?= Html::encode($this->title) ?></h1>

<div class="card">
    <div class="card-body">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'caption_current',
            'from_date',
            'to_date',
            //'order_number',
            //'order_date',
            [
                'attribute' => 'status',
                'value' => function (\common\models\reception\Commission $model) {
                    return \common\helpers\CommissionHelper::getStatusList()[$model->status];
                }
            ],
            'create_ts',
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
