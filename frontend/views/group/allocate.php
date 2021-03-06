<?php

use common\models\person\Student;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $allocationModel \frontend\models\forms\GroupAllocationForm */
/* @var $years [] */
/* @var $groups [] */
/* @var $fromCurrentGroupSearch \frontend\search\StudentSearch */
/* @var $studentsFromGroupDataProvider yii\data\ActiveDataProvider */
/* @var $withoutGroupSearch \frontend\search\StudentSearch */
/* @var $studentsWithoutGroupDataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Groups allocation');
$this->params['breadcrumbs'][] = $this->title;
?>

<div style="position: relative;">
    <h1><?=$this->title?></h1>
</div>

<div class="group-index skin-white">
    <div class="card-body">
        <?php $form = ActiveForm::begin([
            'id' => 'group-allocation-form',
            'action' => ['group/allocate'],
            'enableClientValidation' => false,
            'method' => 'GET',
            'options' => [
                'validateOnSubmit' => true,
                'data-pjax' => '#list-pjax'
            ],
        ]); ?>
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($allocationModel, "class")->dropDownList(
                    $years, [
                        'class' => 'form-control active-form-refresh-control',
                        'prompt' => '',
                        'id' => 'class-id'
                ]);?>
            </div>
            <div class="col-md-6">
                <?= $form->field($allocationModel, 'group_id')->widget(DepDrop::classname(), [
                    'options' => ['id' => 'group-id'],
                    'data' => $groups,
                    'pluginOptions' => [
                        'depends' => ['class-id'],
                        'placeholder' => Yii::t('app', 'Select group'),
                        'url'=>Url::to(['/group/by-year']),
                        'loadingText' => '',
                        'initialize' => true,
                    ]
                ]);?>
            </div>
        </div>
        <?= Html::submitButton(Html::tag('span', Yii::t('app', 'Search')), ['id' => 'pjax-submit', 'class' => 'hidden']) ?>
        <?php ActiveForm::end(); ?>
        <?php if($allocationModel->group_id): ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="card-header">
                        <?=Yii::t('app', 'Students without group')?>
                    </div>
                    <?= GridView::widget([
                        'dataProvider' => $studentsWithoutGroupDataProvider,
                        'filterModel' => $withoutGroupSearch,
                        'layout' => "{items}\n{pager}",
                        'tableOptions' => [
                            'class' => 'table table-striped table-condensed'
                        ],
                        'columns' => [
                            'lastname',
                            'firstname',
                            'middlename',

                            [
                                'class' => 'yii\grid\ActionColumn',
                                'template' => '{fire} {add}',
                                'buttons' => [
                                    'fire' => function ($url, \common\models\person\Student $model) use($allocationModel) {
                                        return Html::a('<span class="glyphicon glyphicon-trash"></span>',
                                            ['group/delete-student-from-institution', 'id' => $model->id, 'group_id' => $allocationModel->group_id, 'class' => $allocationModel->class], [
                                                'data-confirm' => Yii::t('app', 'Are you sure?')
                                            ]
                                        );
                                    },
                                    'add' => function ($url, \common\models\person\Student $model) use($allocationModel) {
                                        return Html::a('<span class="glyphicon glyphicon-plus"></span>',
                                            ['group/add-student', 'id' => $model->id, 'group_id' => $allocationModel->group_id, 'class' => $allocationModel->class]
                                        );
                                    },
                                ],
                            ],

                        ],
                    ]); ?>
                </div>
                <div class="col-md-6">
                    <div class="card-header">
                        <?=Yii::t('app', 'Added to group')?> (<?=$studentsFromGroupDataProvider->totalCount?>)
                    </div>
                    <?= GridView::widget([
                        'dataProvider' => $studentsFromGroupDataProvider,
                        'filterModel' => $fromCurrentGroupSearch,
                        'layout' => "{items}\n{pager}",
                        'tableOptions' => [
                            'class' => 'table table-striped table-condensed'
                        ],
                        'columns' => [
                            'lastname',
                            'firstname',
                            'middlename',

                            [
                                'class' => 'yii\grid\ActionColumn',
                                'template' => '{drop}',
                                'buttons' => [
                                    'drop' => function ($url, \common\models\person\Student $model) use($allocationModel) {
                                        return Html::a('<span class="glyphicon glyphicon-minus"></span>',
                                            ['group/delete-student', 'id' => $model->id, 'group_id' => $allocationModel->group_id, 'class' => $allocationModel->class], [
                                                'data-confirm' => Yii::t('app', 'Are you sure?')
                                            ]);
                                    },
                                ],
                            ],

                        ],
                    ]); ?>
                </div>
            </div>
        <?php endif;?>
    </div>
</div>

<?php
$this->registerJs(
/** @lang javascript */
    '
    $(document).on("change", "#group-id", function() {
        $("#pjax-submit").click()
    });
    '
);
?>