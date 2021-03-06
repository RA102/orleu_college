<?php

namespace common\models;

use common\helpers\SchemeHelper;
use common\models\link\TeacherCourseGroupLink;
use common\models\organization\Group;
use common\models\person\Person;
use Yii;

/**
 * This is the model class for table "teacher_course".
 *
 * @property int $id
 * @property int $course_id
 * @property int $teacher_id
 * @property string $type
 * @property string $create_ts
 * @property string $update_ts
 * @property string $delete_ts
 * @property int $status
 *
 * @property Course $course
 * @property Lesson[] $lessons
 * @property Person $person // TODO should it be Employee?
 * @property Group[] $groups
 */
class TeacherCourse extends \yii\db\ActiveRecord
{
    const REQUIRED = 1; // обязательный предмет
    const OPTIONAL = 2; // по выбору
    const FACULTATIVE = 3; // факультатив
    const PRACTICAL = 4; // практика

    const TYPE_LECTURE = 1;
    const TYPE_SEMINAR = 2;
    const TYPE_COURSE_WORK = 3;
    const TYPE_CONSULTATION = 4;
    const TYPE_PRACTICE = 5;
    const TYPE_PROFESSIONAL_PRACTICE_TECH = 6;
    const TYPE_PROFESSIONAL_PRACTICE_PROD = 7;
    const TYPE_TEST = 8;
    const TYPE_OFFSET = 9;
    const TYPE_EXAM = 10;
    const TYPE_DIPLOMA = 11;
    const TYPE_FACULTATIVE = 12;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return SchemeHelper::PUBLIC . 'teacher_course';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['course_id', 'teacher_id'], 'required'],
            [['course_id', 'teacher_id'], 'default', 'value' => null],
            [['course_id', 'teacher_id', 'status'], 'integer'],
            [['type'], 'string', 'max' => 255],
            [['teacher_id'], 'exist', 'skipOnError' => true, 'targetClass' => Person::class, 'targetAttribute' => ['teacher_id' => 'id']],
            [['course_id'], 'exist', 'skipOnError' => true, 'targetClass' => Course::class, 'targetAttribute' => ['course_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'course_id' => Yii::t('app', 'Course ID'),
            'teacher_id' => Yii::t('app', 'Teacher ID'),
            'type' => 'Способ',
            'groups' => 'Группы',
            'start_ts' => Yii::t('app', 'Teacher Course Start TS'),
            'end_ts' => Yii::t('app', 'Teacher Course End TS'),
            'create_ts' => Yii::t('app', 'Create Ts'),
            'update_ts' => Yii::t('app', 'Update Ts'),
            'delete_ts' => Yii::t('app', 'Delete Ts'),
            'status' => 'Тип',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCourse()
    {
        return $this->hasOne(Course::class, ['id' => 'course_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLessons()
    {
        return $this->hasMany(Lesson::class, ['teacher_course_id' => 'id'])->inverseOf('teacherCourse');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPerson() // TODO should it be Employee?
    {
        return $this->hasOne(Person::class, ['id' => 'teacher_id']);
    }

    public function getPersonName() // TODO should it be Employee?
    {
        return $this->person->fullName;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroupsVia()
    {
        return $this->hasMany(Group::class, ['id' => 'group_id'])
            ->viaTable('link.teacher_course_group_link', ['teacher_course_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroups()
    {
        return $this->hasMany(Group::class, ['id' => 'group_id'])->via('teacherCourseGroupLinks');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeacherCourseGroupLinks()
    {
        return $this->hasMany(TeacherCourseGroupLink::class, ['teacher_course_id' => 'id'])
            ->andWhere([TeacherCourseGroupLink::tableName() . '.delete_ts' => null]);
    }

    public function getFullname()
    {
        return $this->course->caption_current . ' (' . $this->type . ')';
    }

    public function getDiscipline()
    {
        return $this->course->institutionDiscipline;
    }

    public function getDisciplineName()
    {
        return $this->course->institutionDiscipline->caption_current;
    }

    public function statusList()
    {
        $list = [
            '1' => 'Обязательный',
            '2' => 'По выбору',
            '3' => 'Факультатив',
        ];

        return $list;
    }
    public function statusName($status)
    {
        $list = $this->statusList();

        return $list[$status];
    }

    public function getStatus($status)
    {
        if ($status !== null) {
            $list = $this->statusList();
            return $list[$status];
        }
    }

    public function getStatusName()
    {
        $list = $this->statusList();
        if (array_key_exists($this->status, $list)) {
            return $list[$this->status];
        }
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
