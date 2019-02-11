<?php

namespace common\models;

use common\helpers\SchemeHelper;
use common\models\person\Person;
use Yii;

/**
 * This is the model class for table "teacher_course".
 *
 * @property int $id
 * @property int $course_id
 * @property int $teacher_id
 * @property string $type
 * @property string $start_ts
 * @property string $end_ts
 * @property string $create_ts
 * @property string $update_ts
 * @property string $delete_ts
 *
 * @property Course $course
 * @property Lesson[] $lessons
 */
class TeacherCourse extends \yii\db\ActiveRecord
{
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
            [['course_id', 'teacher_id', 'start_ts', 'end_ts'], 'required'],
            [['course_id', 'teacher_id'], 'default', 'value' => null],
            [['course_id', 'teacher_id'], 'integer'],
            [['start_ts', 'end_ts'], 'safe'],
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
            'type' => Yii::t('app', 'Type'),
            'start_ts' => Yii::t('app', 'Start Ts'),
            'end_ts' => Yii::t('app', 'End Ts'),
            'create_ts' => Yii::t('app', 'Create Ts'),
            'update_ts' => Yii::t('app', 'Update Ts'),
            'delete_ts' => Yii::t('app', 'Delete Ts'),
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
}