<?php

use common\helpers\DisciplineHelper;
use common\models\Discipline;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Discipline */

$this->title = $model->caption_current;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Disciplines'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>

<?php $this->beginBlock('content') ?>
    <div class="discipline-view">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
        </p>

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                'caption_current',
                'slug',
                'status',
                [
                    'attribute' => 'types',
                    'value' => function(Discipline $model) {
                        return implode(', ', array_map(function ($item) {
                            return DisciplineHelper::getTypeList()[$item];
                        }, $model->types));
                    }
                ],
                'create_ts',
                'update_ts',
                'delete_ts',
            ],
        ]) ?>

    </div>
<?php $this->endBlock() ?>
<?= $this->render('_layout') ?>

