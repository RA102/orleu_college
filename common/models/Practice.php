<?php

namespace common\models;

use common\helpers\SchemeHelper;
use common\models\TeacherCourse;
use common\models\organization\Group;
use common\models\person\Employee;
use Yii;
use yii\db\ArrayExpression;

/**
 * This is the model class for table "practice".
 *
 * @property int $id
 * @property array $caption
 *
 */
class Practice extends \yii\db\ActiveRecord
{
    public $caption_current;

    public $caption_ru;
    public $caption_kk;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return SchemeHelper::PUBLIC . 'practice';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['caption_ru', 'caption_kk'], 'required'],
            [['caption'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'caption_ru' => Yii::t('app', 'Caption Ru'),
            'caption_kz' => Yii::t('app', 'Caption Kk'),
        ];
    }

    public function beforeSave($insert)
    {
        // set json caption from two non json fields
        $this->caption = [
            'ru' => $this->caption_ru,
            'kk' => $this->caption_kk,
        ];

        return parent::beforeSave($insert);
    }

    public function afterFind()
    {
        $currentLanguage = \Yii::$app->language == 'kz-KZ' ? 'kk' : 'ru';
        // set current caption, can be used as default caption variant
        $this->caption_current = $this->caption[$currentLanguage] ?? $this->caption['ru'] ?? $this->caption['kk'] ?? null;
        // set caption in russian and kazakh
        $this->caption_ru = $this->caption['ru'] ?? null;
        $this->caption_kk = $this->caption['kk'] ?? null;

        parent::afterFind();
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
    public function getGroup()
    {
        return $this->hasOne(Group::class, ['id' => 'group_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeacher($teacher_id)
    {
        return Employee::findOne(['id' => $teacher_id]);
    }

    public function forYear()
    {
        return intval($this->hours[1]) + intval($this->hours[2]);
    }

}
