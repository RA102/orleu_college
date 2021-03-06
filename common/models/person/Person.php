<?php

namespace common\models\person;

use common\helpers\LanguageHelper;
use common\helpers\PersonHelper;
use common\helpers\SchemeHelper;
use common\helpers\PersonTypeHelper;
use common\models\link\PersonInstitutionLink;
use common\models\Nationality;
use common\models\organization\Institution;
use common\models\PersonRelative;
use Yii;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "person.person".
 *
 * @property int $id
 * @property int $status
 * @property string $nickname
 * @property string $firstname
 * @property string $lastname
 * @property string $middlename
 * @property string $birth_date
 * @property int $sex
 * @property int $nationality_id
 * @property string $iin
 * @property int $is_pluralist
 * @property int $birth_country_id
 * @property int $birth_city_id
 * @property string $birth_place
 * @property string $language
 * @property int $oid
 * @property int $alledu_id
 * @property int $alledu_server_id
 * @property int $pupil_id
 * @property int $owner_id
 * @property int $server_id
 * @property bool $is_subscribed
 * @property int $portal_uid
 * @property string $photo
 * @property int $type
 * @property string $create_ts
 * @property string $delete_ts
 * @property string $import_ts
 * @property string $person_type
 * @property array $lang
 *
 * @property AccessToken[] $accessTokens
 * @property AccessToken $activeAccessToken
 * @property Nationality $nationality
 * @property PersonInfo[] $personInfos
 * @property PersonContact[] $personContacts
 * @property PersonCredential[] $personCredentials
 * @property PersonLocation[] $personLocations
 * @property Institution[] $institutions
 * @property Institution $institution
 * @property PersonInstitutionLink[] $personInstitutionLinks
 * @property PersonRelative[] $relatives
 */
class Person extends \yii\db\ActiveRecord implements IdentityInterface
{
    const TYPE_UNDEFINED = 0;
    const TYPE_STUDENT = 1;
    const TYPE_EMPLOYEE = 2;
    const TYPE_ENTRANT = 3;
    //const TYPE_HR = 4;
    const GLOBAL_ADMIN = 5;

    const SEX_NONE = 0;
    const SEX_MALE = 1;
    const SEX_FEMALE = 2;

    const STATUS_ACTIVE = 1;
    const STATUS_FIRED = 2;
    const STATUS_DELETED = 0;
    const STATUS_PLURALIST = 4;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return SchemeHelper::PERSON . 'person';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'status',
                    'sex',
                    'nationality_id',
                    'is_pluralist',
                    'birth_country_id',
                    'birth_city_id',
                    'oid',
                    'alledu_id',
                    'alledu_server_id',
                    'pupil_id',
                    'owner_id',
                    'server_id',
                    'portal_uid',
                    'type',
                    'person_type'
                ],
                'default',
                'value' => null
            ],
            [['status', 'sex', 'nationality_id', 'is_pluralist', 'birth_country_id', 'birth_city_id', 'oid', 'alledu_id', 'alledu_server_id', 'pupil_id', 'owner_id', 'server_id', 'portal_uid', 'type'], 'integer'],
            [['birth_date', 'create_ts', 'delete_ts', 'import_ts', 'lang'], 'safe'],
            [['is_subscribed'], 'boolean'],
            [['nickname', 'firstname', 'lastname', 'middlename', 'iin', 'person_type'], 'string', 'max' => 100],
            [['birth_place', 'photo'], 'string', 'max' => 255],
            [['language'], 'string', 'max' => 2],
            //['iin', 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'status' => Yii::t('app', 'Status'),
            'nickname' => Yii::t('app', 'Nickname'),
            'firstname' => Yii::t('app', 'Firstname'),
            'lastname' => Yii::t('app', 'Lastname'),
            'middlename' => Yii::t('app', 'Middlename'),
            'birth_date' => Yii::t('app', 'Birth Date'),
            'sex' => Yii::t('app', 'Sex'),
            'nationality_id' => Yii::t('app', 'Nationality ID'),
            'iin' => Yii::t('app', 'Iin'),
            'is_pluralist' => Yii::t('app', 'Is Pluralist'),
            'birth_country_id' => Yii::t('app', 'Birth Country ID'),
            'birth_city_id' => Yii::t('app', 'Birth City ID'),
            'birth_place' => Yii::t('app', 'Birth Place'),
            'language' => Yii::t('app', 'Language of education'),
            'oid' => Yii::t('app', 'Oid'),
            'alledu_id' => Yii::t('app', 'Alledu ID'),
            'alledu_server_id' => Yii::t('app', 'Alledu Server ID'),
            'pupil_id' => Yii::t('app', 'Pupil ID'),
            'owner_id' => Yii::t('app', 'Owner ID'),
            'server_id' => Yii::t('app', 'Server ID'),
            'is_subscribed' => Yii::t('app', 'Is Subscribed'),
            'portal_uid' => Yii::t('app', 'Portal Uid'),
            'photo' => Yii::t('app', 'Photo'),
            'type' => Yii::t('app', 'Type'),
            'create_ts' => Yii::t('app', 'Create Ts'),
            'delete_ts' => Yii::t('app', 'Delete Ts'),
            'import_ts' => Yii::t('app', 'Import Ts'),
        ];
    }

    public function getNationality()
    {
        return $this->hasOne(Nationality::className(), ['id' => 'nationality_id']);
    }

    public function getPersonInfos()
    {
        return $this->hasMany(PersonInfo::class, ['person_id' => 'id']);
    }

    public function getPersonContacts()
    {
        return $this->hasMany(PersonContact::class, ['person_id' => 'id']);
    }

    public function getPersonCredentials()
    {
        return $this->hasMany(PersonCredential::class, ['person_id' => 'id']);
    }

    public function getPersonLocations()
    {
        return $this->hasMany(PersonLocation::class, ['person_id' => 'id']);
    }

    public function getPersonType()
    {
        return $this->hasOne(PersonType::class, ['name' => 'person_type']);
    }

    public function getPersonTypeCaption()
    {
        /*$types = [
            'superadmin' => 'Супер администратор',
            'admin' => 'Администратор',
            'teacher' => 'Преподаватель',
            'hr' => 'HR',
            'director' =>
            'entrant' => 'Абитуриент',
            'student' => 'Студент',            
        ];*/

        $types = PersonTypeHelper::getList();

        return $types[$this->person_type];
    }

    public function isActive()
    {
        return $this->status == self::STATUS_ACTIVE && !$this->isDeleted();
    }

    public function isPuralist()
    {
        return $this->status == self::STATUS_PLURALIST && !$this->isDeleted();
    }

    public function isFired()
    {
        return $this->status == self::STATUS_FIRED && !$this->isDeleted();
    }

    public function isDeleted()
    {
        return $this->delete_ts != null;
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::find()->with('personType')->where(['id' => $id])->one();
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Returns Person by portal_uid
     * @param int $uid
     * @return Person|null
     */
    public static function findIdentityByUID(int $uid)
    {
        return static::findOne(['portal_uid' => $uid]);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        throw new NotSupportedException('"getAuthKey" is not implemented.');
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    public function getAccessTokens()
    {
        return $this->hasMany(AccessToken::class, ['id' => 'person_id'])
            ->andWhere(['delete_ts' => null])
            ->andWhere(['>', 'expire_ts', time()]);
    }

    public function getActiveAccessToken()
    {
        return $this->hasOne(AccessToken::class, ['person_id' => 'id'])
            ->orderBy(['expire_ts' => SORT_DESC])
            ->andWhere(['delete_ts' => null])
            ->andWhere('expire_ts > NOW()');
    }

    /**
     * @TODO Fill all attributes
     */
    public static function add($portal_uid, $firstname, $lastname, $middlename, $iin): Person
    {
        $model = new static;
        $model->portal_uid = $portal_uid;
        $model->status = static::STATUS_ACTIVE;
        $model->firstname = $firstname;
        $model->lastname = $lastname;
        $model->middlename = $middlename;
        $model->iin = $iin;

        return $model;
    }

    public function getInstitutions()
    {
        return $this->hasMany(Institution::className(), ['id' => 'institution_id'])
            ->viaTable('link.person_institution_link', ['person_id' => 'id'], function($query) {
                $query->andWhere(['link.person_institution_link.is_deleted' => false]);
            });
    }

    public function getInstitution()
    {
        if ($this->current_institution !== null) {
            return Institution::findOne($this->current_institution);
        } else {
            return $this->getInstitutions()->one();
        }
    }

    public function getFullName()
    {
        return trim("{$this->lastname} {$this->firstname} {$this->middlename}");
    }

    /**
         * @return \yii\db\ActiveQuery
     */
    public function getPersonInstitutionLinks()
    {
        return $this->hasMany(PersonInstitutionLink::class, ['person_id' => 'id']);
    }

    public function getLanguage()
    {
        $language = $this->language;
        if ($this->language == 'kz') {
            $language = 'kk';
        }
        
        return LanguageHelper::getLanguageList()[$language] ?? '';
    }

    public function getRelatives()
    {
        return $this->hasMany(PersonRelative::class, ['person_id' => 'id']);
    }

    public function getSex()
    {
        return PersonHelper::getSexList()[$this->sex] ?? null;
    }

    public function setDeleteStatus()
    {
        $this->delete_ts = intval($this->status) === Person::STATUS_ACTIVE ? null : date('Y-m-d H:i:s');
    }

    public function isSuperadmin()
    {
        if ($this->personType->name == 'college_superadmin') {
            return true;
        } else return false;
    }

    public function isAdmin()
    {
        if ($this->personType->name == 'college_admin' || $this->personType->name == 'college_superadmin') {
            return true;
        } else return false;
    }

    public function isStudent()
    {
        return in_array($this->type, [Person::TYPE_STUDENT, Person::TYPE_ENTRANT]);
    }

    public function isHr()
    {
        if ($this->personType->name == 'hr') {
            return true;
        } else return false;
    }

    public function isTeacher()
    {
        if ($this->personType->name == 'teacher') {
            return true;
        } else return false;
    }

    public function isDirector()
    {
        if ($this->personType->name == 'director') {
            return true;
        } else return false;
    }

    public function isChairman()
    {
        if ($this->personType->name == 'chairman') {
            return true;
        } else return false;
    }

    public function isDirectorDeputyAcademic()
    {        
        if ($this->personType->name == 'director_deputy_academic') {
            return true;
        } else return false;
    }

    public function isDirectorDeputyEducation()
    {        
        if ($this->personType->name == 'director_deputy_education') {
            return true;
        } else return false;
    }

    public function isDirectorDeputyIndustrial()
    {        
        if ($this->personType->name == 'director_deputy_industrial') {
            return true;
        } else return false;
    }

    public function isDirectorDeputyMethodist()
    {        
        if ($this->personType->name == 'director_deputy_methodist') {
            return true;
        } else return false;
    }

    public function isDirectorDeputyEconomic()
    {        
        if ($this->personType->name == 'director_deputy_economic') {
            return true;
        } else return false;
    }

    public function isAdmissionSpecialist()
    {        
        if ($this->personType->name == 'admission_specialist') {
            return true;
        } else return false;
    }

    public function isManager()
    {        
        if ($this->personType->name == 'manager') {
            return true;
        } else return false;
    }

    public function isPsychologist()
    {        
        if ($this->personType->name == 'psychologist') {
            return true;
        } else return false;
    }

    public function isSocialTeacher()
    {        
        if ($this->personType->name == 'social_teacher') {
            return true;
        } else return false;
    }

    public function isStaff()
    {        
        if ($this->personType->name == 'staff') {
            return true;
        } else return false;
    }

    /*TODO make normal rbac control*/
    public function isAble($action)
    {
        $college_admin = ['employee-index'];
        $teacher = [];
        $student = [];
        $entrant = [];
        $ht = [];
        $director = [];
        $chairman = [];
        $director_deputy_academic = [];
        $director_deputy_education = [];
        $director_deputy_industrial = [];
        $director_deputy_methodist = [];
        $director_deputy_economic = [];
        $admission_specialist = [];
        $manager = [];
        $psychologist = [];
        $social_teacher = [];
        $staff = [];

        $personType = $this->personType->name;

        if ($this->isSuperadmin()) {
            return true;
        }
        elseif (!$this->isSuperadmin()) {
            if (in_array($action, $$personType)) {
                return true;
            }
        }
        else return false;
    }

    public function institutionAdvanced()
    {
        return $this->institution->advanced;
    }

}
