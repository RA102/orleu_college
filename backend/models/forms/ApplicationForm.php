<?php
namespace backend\models\forms;

use common\models\organization\InstitutionApplication;
use yii\base\Model;
use Yii;
use yii\web\Application;
use common\components\ActiveForm;

/**
 * Signup form
 */
class ApplicationForm extends Model
{
    public $email;
    public $educational_form_id;
    public $organizational_legal_form_id;
    public $name;
    public $city_id;
    public $type_id;
    public $phone;
    public $sex;
    public $iin;
    public $firstname;
    public $lastname;
    public $middlename;
    public $street;
    public $birth_date;
    public $house_number;

    public $country_id;
    public $city_ids = [];
    public $street_id;
    public $type_ids = [];

    public $hasCountryUnit = false;
    public $hasStreet = false;
    public $hasHouseNumber = false;

    public $hasInstitutionType = false;

    public function __construct(
        InstitutionApplication $application,
        array $config = []
    )
    {
        $this->setAttributes($application->attributes);
        $this->type_ids = $application->getInstitutionTypeIds();
        $this->country_id = $application->getCountryId();
        $this->city_id = $application->city_id;
        $this->city_ids = $application->getCityIds();
        $this->street = $application->street;
//        echo $this->street;die();

        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],

            [
                [
                    'educational_form_id', 'organizational_legal_form_id', 'name',
                    'phone', 'sex', 'iin', 'firstname', 'lastname', 'middlename',
                    'birth_date', 'house_number'
                ],
                'required'
            ],
            [['country_id', 'city_ids', 'street_id'], 'default', 'value' => null],
            [['country_id'], 'required'],
            [['country_id', 'city_ids', 'street', 'type_ids'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'iin' => Yii::t('app', 'Iin'),
            'sex' => Yii::t('app', 'Sex'),
            'email' => Yii::t('app', 'Email'),
            'phone' => Yii::t('app', 'Phone'),
            'name' => Yii::t('app', 'Title'),
            'city_id' => Yii::t('app', 'City ID'),
            'type_id' => Yii::t('app', 'Type ID'),
            'firstname' => Yii::t('app', 'Firstname'),
            'lastname' => Yii::t('app', 'Lastname'),
            'middlename' => Yii::t('app', 'Middlename'),
            'street' => Yii::t('app', 'Street'),
            'birth_date' => Yii::t('app', 'Birth Date'),
            'house_number' => Yii::t('app', 'House Number'),
            'educational_form_id' => Yii::t('app', 'Educational Form ID'),
            'organizational_legal_form_id' => Yii::t('app', 'Organizational Legal Form ID'),
            'status' => Yii::t('app', 'Status'),
            'create_ts' => Yii::t('app', 'Create Ts'),
            'update_ts' => Yii::t('app', 'Update Ts'),
            'delete_ts' => Yii::t('app', 'Delete Ts'),
            'country_id' => Yii::t('app', 'Address'),
            'street_id' => Yii::t('app', 'Street ID'),
        ];
    }

    public function validate($attributeNames = null, $clearErrors = true)
    {
        if (Yii::$app instanceof Application && (
                Yii::$app->request->post(ActiveForm::$refreshParam)
                || Yii::$app->request->get(ActiveForm::$refreshParam)
            )
        ) {
            return false;
        }

        return parent::validate($attributeNames, $clearErrors);
    }
}
