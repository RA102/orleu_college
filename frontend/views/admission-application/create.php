<?php

/* @var $this yii\web\View */
/* @var $admissionApplicationForm \frontend\models\forms\AdmissionApplicationForm */
/* @var $specialities common\models\handbook\Speciality[] */

$this->title = Yii::t('app', 'Добавить заявление');
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Заявления'),
    'url'   => ['index']
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="admission-application-create">

    <h1><?= $this->title ?></h1>

    <?= $this->render('_form', [
        'admissionApplicationForm' => $admissionApplicationForm,
        'specialities'             => $specialities
    ]) ?>
</div>
