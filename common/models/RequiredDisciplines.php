<?php

namespace common\models;

use common\helpers\SchemeHelper;
use common\models\TeacherCourse;
use common\models\organization\InstitutionDiscipline;
use common\models\organization\Group;
use common\models\person\Employee;
use Yii;
use yii\db\ArrayExpression;

/**
 * This is the model class for table "required_disciplines".
 *
 * @property int $id
 * @property int $teacher_course_id
 * @property int $group_id
 * @property array $lections_hours
 * @property array $seminars_hours
 * @property array $course_works_hours
 * @property array $tests_hours
 * @property array $offsets_hours
 * @property array $consultations_hours
 * @property array $exams_hours
 * @property array $ktp
 *
 */
class RequiredDisciplines extends \yii\db\ActiveRecord
{
    public $exam_type;
    public $exam_week;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return SchemeHelper::PUBLIC . 'required_disciplines';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['teacher_course_id'], 'required'],
            [['lections_hours', 'seminars_hours', 'course_works_hours', 'tests_hours', 'offsets_hours', 'consultations_hours', 'exams_hours', 'ktp'], 'default', 'value' => null],
            [['lections_hours', 'seminars_hours', 'course_works_hours', 'tests_hours', 'offsets_hours', 'consultations_hours', 'exams_hours', 'ktp'], 'safe'],
            [['teacher_course_id', 'group_id'], 'integer'],
        ];
    }

    public function afterFind()
    {
        parent::afterFind();
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'teacher_course_id' => Yii::t('app', 'Дисциплина'),
            'group_id' => Yii::t('app', 'Group'),
            'discipline_id' => Yii::t('app', 'Discipline'),
            'lections_hours' => 'Кол-во часов на лекции',
            'seminars_hours' => 'Кол-во часов на семинары',
            'course_works_hours' => 'Кол-во часов на курсовые работы',
            'tests_hours' => 'Кол-во часов на контрольные работы',
            'offsets_hours' => 'Кол-во часов на зачёт',
            'consultations_hours' => 'Кол-во часов на консультации',
            'exams_hours' => 'Кол-во часов на экзамены',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeacherCourse()
    {
        return $this->hasOne(TeacherCourse::class, ['id' => 'teacher_course_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstitutionDiscipline()
    {
        return $this->hasOne(InstitutionDiscipline::class, ['id' => 'discipline_id']);
            //->via('course');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCourse()
    {
        return $this->hasOne(Course::class, ['id' => 'course_id'])
            ->viaTable('public.teacher_course', ['id' => 'teacher_course_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroup()
    {
        return $this->hasOne(Group::class, ['id' => 'group_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    /*public function getTeacher()
    {
        return $this->hasOne(Employee::class, ['id' => 'teacher_id']);
    }*/

    /*public function getDisciplineName()
    {
        return $this->institutionDiscipline->caption_current;
    }*/

    public function forYear($property)
    {
        
        return intval($this->$property[1]) + intval($this->$property[2]);
    }

    public function totalHours($semester)
    {
        if ($semester == 3) {
            $total = $this->forYear('lections_hours') + $this->forYear('seminars_hours') + $this->forYear('course_works_hours') + $this->forYear('tests_hours') + $this->forYear('offsets_hours') + $this->forYear('consultations_hours') + $this->forYear('exams_hours');
        }
        else $total = intval($this->lections_hours[$semester]) + intval($this->seminars_hours[$semester]) + intval($this->course_works_hours[$semester]) + intval($this->tests_hours[$semester]) + intval($this->offsets_hours[$semester]) + intval($this->consultations_hours[$semester]) + intval($this->exams_hours[$semester]);

        return $total;
    }

    public function types()
    {
        $types = [
            'Теоретическое обучение' => [
                '1' => 'Лекция', 
                '2' => 'Семинар (ЛПЗ)', 
                '3' => 'Курсовая работа (проект)',
                '4' => 'Консультации',
            ],
            'Практика' => [
                '5' => 'Учебная практика',
            ],
            'Профессиональная практика' => [
                '6' => 'Технологическая', 
                '7' => 'Производственная',
            ], 
            'Промежуточная и итоговая аттестация' => [
                '8' => 'Контрольная работа',
                '9' => 'Зачёт',
                '10' => 'Экзамен',
            ],
            'Дипломная работы' =>[
                '11' => 'Написание и защита дипломной работы (проекта)',
            ],
            'Дополнительно' => [
                '12' => 'Факультативные курсы',
            ],
        ];

        return $types;
    }

    public function getType($type)
    {
        $types = [
            '1' => 'Лекция', 
            '2' => 'Семинар (ЛПЗ)', 
            '3' => 'Курсовая работа (проект)',
            '4' => 'Консультации',
            '5' => 'Учебная практика',
            '6' => 'Технологическая', 
            '7' => 'Производственная',
            '8' => 'Контрольная работа',
            '9' => 'Зачёт',
            '10' => 'Экзамен',
            '11' => 'Написание и защита дипломной работы (проекта)',
            '12' => 'Факультативные курсы',
        ];

        return $types[$type];
    }

}
