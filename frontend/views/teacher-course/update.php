<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\TeacherCourse */

$this->title = Yii::t('app', 'Update Teacher Course') . ': ' . $model->course->caption;
$this->params['breadcrumbs'][] = ['label' => $model->course->caption, 'url' => ['view', 'id' => $model->id, 'course_id' => $model->course_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>

<h1><?= Html::encode($this->title) ?></h1>
<?php $this->beginBlock('content') ?>
<div class="teacher-course-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
<?php $this->endBlock() ?>
<?= $this->render('_layout') ?>