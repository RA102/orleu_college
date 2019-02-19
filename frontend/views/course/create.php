<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Course */
/* @var $disciplines common\models\Discipline[] */
/* @var $classes array */

$this->title = Yii::t('app', 'Create Course');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Courses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?= Html::encode($this->title) ?></h1>
<?php $this->beginBlock('content') ?>
<div class="course-create">

    <?= $this->render('_form', [
        'model' => $model,
        'disciplines' => $disciplines,
        'classes' => $classes,
    ]) ?>

</div>
<?php $this->endBlock() ?>
<?= $this->render('_layout') ?>