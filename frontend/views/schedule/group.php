<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model common\models\organization\Group */
/* @var $teacherCourses common\models\TeacherCourse[] */
/* @var $teachers common\models\person\Employee[] */
/* @var $classrooms common\models\organization\Classroom[] */
/* @var $searchModel frontend\search\LessonSearch */

$this->title = Yii::t('app', 'Group Lessons') . ': ' . $model->caption_current;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Lessons'), 'url' => ['groups']];
$this->params['breadcrumbs'][] = $this->title;
?>
    <div style="position: relative;">
        <h1><?= $this->title ?></h1>
		<?= Html::a('Полное расписание', ['/lesson/schedule','group_id' => $model->id,], ['class' => 'btn btn-primary']) ?>
    </div>


    <div class="lesson-index skin-white">
        <div class="card-body">
        	<table class="table table-condensed table-bordered table-striped">
        		<tr class="info">
        			<th>#</th>
        			<?php foreach ($weekdays as $key => $value):?>
        				<th class="col-md-2"><?=$value?></th>
        			<?php endforeach;?>
	        		<?php while ($number <= 10):?>
	        			<tr>
	        				<th><?=$number?></th>
		        			<?php foreach ($weekdays as $key => $value):?>
		        				<?php if ($lessons):?>
		        						<td>
				        					<a href="add-lesson?group_id=<?=$model->id?>&weekday=<?=$key?>&number=<?=$number?>" style="display:block; height: 80px;">
							        				<?php foreach ($lessons as $lesson):?>
							        					<?php if ($lesson->weekday == $key && $lesson->lesson_number == $number):?>
							        							<?=$lesson->teacherCourse->disciplineName?> <br>
							        							<?=$lesson->teacherCourse->person->fullName?> <br>
							        							(<?=$lesson->classroom->number?>)
							        							<?php if ($lesson->teacherCourse->status == 2):?>
							        								<br><small>(по выбору)</small>
							        							<?php endif;?>
							        							<?= Html::a(Yii::t('app', ''), ['delete', 'id' => $lesson->id], [
													                'class' => 'glyphicon glyphicon-trash',
													                'data'  => [
													                    'confirm' => 'Удалить занятие?',
													                    'method'  => 'post',
													                ],
												                	'style' => 'float:right;'
													            ]) ?>
							        					<?php endif;?>
								        			<?php endforeach;?>
								        	</a>
		        						</td>
	    						<?php else:?>
			        				<td>
			        					<a href="add-lesson?group_id=<?=$model->id?>&weekday=<?=$key?>&number=<?=$number?>">
			        						<div style="height: 100%;width: 100%;">&nbsp</div>
			        					</a>
			        				</td>
	        					<?php endif;?>
		        			<?php endforeach;?>
	        			</tr>
		    			<?php $number++;?>
	        		<?php endwhile;?>
        	</table>
        </div>
    </div>
