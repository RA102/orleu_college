<?php

namespace common\models;

use common\helpers\SchemeHelper;
use common\models\person\Person;
use common\models\organization\Group;
use common\models\organization\Classroom;
use common\models\RequiredDisciplines;
use common\models\TeacherCourse;
use Yii;
use yii\db\ArrayExpression;

/**
 * This is the model class for table "schedule".
 *
 * @property int $id
 * @property int $group_id
 * @property int $weekday
 * @property int $teacher_course_id
 * @property int $lesson_number
 * @property int $classroom_id
 * @property int $type
 *
 */
class Schedule extends \yii\db\ActiveRecord
{
	public $double;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return SchemeHelper::PUBLIC . 'schedule';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['group_id', 'weekday', 'teacher_course_id', 'lesson_number', 'classroom_id'], 'required'],
            [['group_id', 'weekday', 'teacher_course_id', 'lesson_number', 'classroom_id', 'type'], 'integer'],
            [['double'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'group_id' => Yii::t('app', 'Group ID'),
            'weekday' => Yii::t('app', 'День недели'),
            'teacher_course_id' => Yii::t('app', 'Teacher Course ID'),
            'lesson_number' => Yii::t('app', 'Урок №'),
            'classroom_id' => Yii::t('app', 'Classroom ID'),
            'double' => Yii::t('app', 'Удвоенный'),
            'type' => Yii::t('app', 'Тип'),
        ];
    }

    /*public function afterFind()
    {
        parent::afterFind();
    }*/

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroup()
    {
        return $this->hasOne(Group::class, ['id' => 'group_id']);
    }

    public function getTeacherCourse()
    {
    	return $this->hasOne(TeacherCourse::class, ['id' => 'teacher_course_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    /*public function getTeacher()
    {
        return $this->hasOne(Person::class, ['id' => 'teacher_id']);
    }*/

    /**
     * @return \yii\db\ActiveQuery
     */
    /*public function getRequiredDiscipline()
    {
        return $this->hasOne(RequiredDisciplines::class, ['id' => 'discipline_id']);
    }*/

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClassroom()
    {
        return $this->hasOne(Classroom::class, ['id' => 'classroom_id']);
    }

    public function getWeekdays()
    {
    	$weekdays = [
    		'1' => 'Понедельник',
    		'2' => 'Вторник',
    		'3' => 'Среда',
    		'4' => 'Четверг',
    		'5' => 'Пятница',
    		'6' => 'Суббота',
    	];

    	return $weekdays;
    }

    public function getWeekday($weekday)
    {
    	$weekdays = $this->getWeekdays();

    	return $weekdays[$weekday];
    }

    public function getTypes()
    {
        $types = [
            'Теоретическое обучение' => [
                '1' => 'Лекция', 
                '2' => 'Семинар (ЛПЗ)', 
                '3' => 'Курсовая работа (проект)',
                '4' => 'Консультации',
            ],
            /*'Практика' => [
                '5' => 'Учебная практика',
            ],
            'Профессиональная практика' => [
                '6' => 'Технологическая', 
                '7' => 'Производственная',
            ],*/ 
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

    public function getType($key)
    {
        $types = $this->getTypes();

        if (array_key_exists($key, $types)) {
            return $types[$key];
        } else {
            foreach ($types as $type) {
                if (is_array($type)) {
                    if (array_key_exists($key, $type)) {
                        return $type[$key];
                    }
                }
            }
        }
    }

}
