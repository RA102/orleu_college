<?php

use common\models\PracticePlan;
use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Практика';

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Планирование учебного процесса'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div style="position: relative;">
    <h1><?=$this->title?></h1>
    <?= Html::a('Добавить', ['create-practice'], ['class' => 'title-action btn btn-primary']) ?>
</div>

<div class="optional-index skin-white">
	<div class="card-body">
		<?= GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                [
                    'attribute' => 'caption',
                    'value' => function (PracticePlan $model) {
                        return $model->practice->caption_current;
                    },
                    'label' => 'Название'
                ],
                [
                    'attribute' => 'group_id',
                    'value' => function (PracticePlan $model) {
                        return $model->group->caption_current;
                    },
                ],

                [
                	'class' => 'yii\grid\ActionColumn',
                    'template' => '{view}',
                	'urlCreator' => function ($action, $model, $key, $index) {
		            if ($action === 'view') {
		                $url ='view-practice?id='.$model->id;
		                return $url;
		            }
		            if ($action === 'update') {
		                $url ='update-practice?id='.$model->id;
		                return $url;
		            }
		            if ($action === 'delete') {
		                $url ='delete-practice?id='.$model->id;
		                return $url;
		            }
		          }
                ],
            ],
        ]); ?>
	</div>
</div>