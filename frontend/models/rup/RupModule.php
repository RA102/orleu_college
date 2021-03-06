<?php

namespace frontend\models\rup;

use Yii;

/**
 * This is the model class for table "rup_sub_block".
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string $time
 * @property boolean $isTemplate
 *
 * @property RupBlock $id0
 * @property RupSubjects[] $rupSubjects
 */
class RupModule extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */

    public static function tableName()
    {
        return 'rup_module';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name','block_id'], 'required'],
            [['time','rup_id','block_id'], 'integer'],
            [['code', 'name'], 'string'],
            [['isTemplate'], 'boolean'],
            [['block', 'timemodulededucted','isTemplate'],'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => 'Индекс',
            'name' => 'Наименование',
            'rup_id'=>'id RUP',
            'time'=>'Всего часов',
            'block_id'=>'Индекс',
            'isTemplate'=>'Добавить в шаблон'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBlock()
    {
        return $this->hasOne(RupBlock::className(), ['id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRupSubjects()
    {
        return $this->hasMany(RupSubjects::className(), ['id_sub_block' => 'id']);
    }

//    public function getTimemodule(){
//        $sum = RupSubjects::find()->select(['time'])->where(['id_sub_block'=>$this->block_id])->andWhere(['rup_id'=>$this->rup_id])->sum('time');
//        return $sum;
//    }
    public function getTimemodulededucted(){

        $sum = RupSubjects::find()->select(['time'])->where(['id_block'=>$this->block_id])->andWhere(['rup_id'=>$this->rup_id])->andWhere(['id_sub_block'=>$this->id])->sum('time');
        return $this->time-$sum;
    }
}
