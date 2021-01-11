<?php

namespace app\models\common;

use Yii;
use yii\base;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $secondname
 * @property string $firstname
 * @property string $patronymic
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    public $addUsers;
    public $viewRoles;
    public $editRoles;
    public $viewOut;
    public $editOut;
    public $viewIn;
    public $editIn;
    public $viewOrder;
    public $editOrder;
    public $viewRegulation;
    public $editRegulation;
    public $viewEvent;
    public $editEvent;
    public $viewAS;
    public $editAS;
    public $viewAdd;
    public $editAdd;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['firstname', 'secondname', 'patronymic', 'username'], 'string'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
            [['addUsers', 'viewRoles', 'editRoles', 'viewOut', 'editOut', 'viewIn', 'editIn', 'viewOrder', 'editOrder',
                'viewRegulation', 'editRegulation', 'viewEvent', 'editEvent', 'viewAS', 'editAS', 'viewAdd', 'editAdd'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'firstname' => 'Имя',
            'secondname' => 'Фамилия',
            'patronymic' => 'Отчество',
            'email' => 'E-mail',
            'username' => 'Логин',
            'addUsers' => 'Разрешено добавлять новых пользователей',
            'viewRoles' => 'Разрешено просматривать роли пользователей',
            'editRoles' => 'Разрешено редактировать роли пользователей',
            'viewOut' => 'Разрешено просматривать исходящую документацию',
            'editOut' => 'Разрешено редактировать исходящую документацию',
            'viewIn' => 'Разрешено просматривать входящую документацию',
            'editIn' => 'Разрешено редактировать входящую документацию',
            'viewOrder' => 'Разрешено просматривать приказы',
            'editOrder' => 'Разрешено редактировать приказы',
            'viewRegulation' => 'Разрешено просматривать положения',
            'editRegulation' => 'Разрешено редактировать положения',
            'viewEvent' => 'Разрешено просматривать мероприятия',
            'editEvent' => 'Разрешено редактировать мероприятия',
            'viewAS' => 'Разрешено просматривать реестр ПО',
            'editAS' => 'Разрешено редактировать реестр ПО',
            'viewAdd' => 'Разрешено просматривать дополнительную информацию',
            'editAdd' => 'Разрешено редактировать дополнительную информацию',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        //throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public function getFullName()
    {
        return $this->secondname.' '.$this->firstname.' '.$this->patronymic;
    }

    //-------------------------------------------

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
        $arr = array($this->addUsers, $this->viewRoles, $this->editRoles, $this->viewOut, $this->editOut,
            $this->viewIn, $this->editIn, $this->viewOrder, $this->editOrder, $this->viewRegulation,
            $this->editRegulation, $this->viewEvent, $this->editEvent, $this->viewAS, $this->editAS,
            $this->viewAdd, $this->editAdd);
        for ($i = 0; $i != count($arr); $i++)
        {

            $tmpAccess = AccessLevel::find()->where(['user_id' => $this->id])->andWhere(['access_id' => $i + 1])->one();

            if ($arr[$i] == 1)
            {
                if ($tmpAccess == null)
                {
                    $newAccess = new AccessLevel();
                    $newAccess->user_id = $this->id;
                    $newAccess->access_id = $i + 1;
                    $newAccess->save(false);
                }
            }
            else
                if ($tmpAccess !== null)
                    $tmpAccess->delete();
        }

    }

}