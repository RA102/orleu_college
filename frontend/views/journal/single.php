<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

$this->title = 'Журнал ' . $group->caption_current;
?>

<p>Группа: <?=$group->caption_current?> <br>
    Предмет: <?=$teacherCourse->course->institutionDiscipline->caption_current?> <br>
    Преподаватель: <?=$teacherCourse->person->getFullname()?> <br>
    Начало курса: <?=date('d-m-Y', strtotime($teacherCourse->start_ts))?> <br>
    Окончание курса: <?=date('d-m-Y', strtotime($teacherCourse->end_ts))?>
</p>

<div class="card">
	<div class="card-header">
        <ul class="nav nav-tabs">
            <li role="presentation">
                <?= Html::a('Теоретическое обучение', ['pluralist'], []) ?>
            </li>
            <li role="presentation">
                <?= Html::a('Курсовые проекты, лабораторно-практические и графические работы', ['pluralist'], []) ?>
            </li>
            <li role="presentation">
                <?= Html::a('Контрольные работы', ['pluralist'], []) ?>
            </li>
        </ul>
    </div>
	<div class="card-body" style="overflow-x: scroll;">
		<table class="table table-bordered table-striped table-responsive">
			<tr>
				<th>№</th>
				<th>ФИО</th>
				<?php foreach ($lessons as $lesson):?>
					<th><?=date('d.m.y', strtotime($lesson->date_ts))?></th>
				<?php endforeach;?>
			</tr>
			<?php foreach ($group->students as $key=>$student):?>
				<tr>
					<td><?=$key+1?></td>
					<td><?=$student->getFullname()?></td>
					<?php foreach ($lessons as $lesson):?>
						<td></td>
					<?php endforeach;?>
				</tr>
			<?php endforeach;?>
		</table>
	</div>
</div>