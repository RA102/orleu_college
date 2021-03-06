<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Course */
/* @var $institutionDisciplines common\models\organization\InstitutionDiscipline[] */
/* @var $classes array */

$this->title = Yii::t('app', 'Update Course') . ': ' . $model->institutionDiscipline->caption_current;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Courses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->institutionDiscipline->caption_current, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>

<h1><?= Html::encode($this->title) ?></h1>
<?php $this->beginBlock('content') ?>
    <div class="course-update">

        <?= $this->render('_form', [
            'model' => $model,
            'institutionDisciplines' => $institutionDisciplines,
            'classes' => $classes,
        ]) ?>

    </div>
<?php $this->endBlock() ?>
<?= $this->render('_layout') ?>
