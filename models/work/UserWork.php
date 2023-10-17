<?php

namespace app\models\work;

use app\models\common\AccessLevel;
use app\models\common\People;
use app\models\common\User;
use app\models\null\PeopleNull;
use Yii;
use yii\base;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;


class UserWork extends User
{


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
    public $viewForeign;
    public $editForeign;
    public $viewProgram;
    public $editProgram;
    public $viewGroup;
    public $editGroup;
    public $viewGroupBranch;
    public $editGroupBranch;
    public $addGroup;
    public $deleteGroup;
    public $report;

    public $oldPass;
    public $newPass;

    public $roles;

    public $password;


    public function rules()
    {
        return [
            [['firstname', 'secondname', 'patronymic', 'username', 'email', 'password_hash', 'newPass', 'oldPass', 'password_hash'], 'string'],
            [['aka', 'creator_id', 'last_edit_id'], 'integer'],
            ['roles', 'safe'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
            [['addUsers', 'viewRoles', 'editRoles', 'viewOut', 'editOut', 'viewIn', 'editIn', 'viewOrder', 'editOrder',
                'viewRegulation', 'editRegulation', 'viewEvent', 'editEvent', 'viewAS', 'editAS', 'viewAdd', 'editAdd',
                'viewForeign', 'editForeign', 'viewProgram', 'editProgram', 'viewGroup', 'editGroup', 'viewGroupBranch', 'editGroupBranch',
                'addGroup', 'deleteGroup', 'report'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'firstname' => 'Имя',
            'secondname' => 'Фамилия',
            'patronymic' => 'Отчество',
            'password_hash' => 'Пароль',
            'email' => 'E-mail',
            'username' => 'Логин (e-mail)',
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
            'viewForeign' => 'Разрешено просматривать внешние мероприятия',
            'editForeign' => 'Разрешено редактировать внешние мероприятия',
            'viewProgram' => 'Разрешено просматривать образовательные программы',
            'editProgram' => 'Разрешено редактировать образовательные программы',
            'viewGroup' => 'Разрешено просматривать все учебные группы',
            'editGroup' => 'Разрешено редактировать все учебные группы',
            'viewGroupBranch' => 'Разрешено просматривать все учебные группы своего отдела',
            'editGroupBranch' => 'Разрешено редактировать все учебные группы своего отдела',
            'addGroup' => 'Разрешено добавлять учебные группы',
            'deleteGroup' => 'Разрешено удалять учебные группы',
            'report' => 'Разрешено генерировать и просматривать отчеты',
            'oldPass' => 'Старый пароль',
            'newPass' => 'Новый пароль',
            'aka' => 'Также является',
            'akaName' => 'Также является',
        ];
    }

    public function getAka()
    {
        $try = $this->hasOne(PeopleWork::className(), ['id' => 'aka']);
        return $try->all() ? $try : new PeopleNull();
    }

    public function getAkaName()
    {
        return PeopleWork::find()->where(['id' => $this->aka])->one()->fullName;
    }

    public function getFullName()
    {
        return $this->secondname.' '.$this->firstname.' '.$this->patronymic;
    }

    public function getRolesString()
    {
        $roles = UserRoleWork::find()->where(['user_id' => $this->id])->all();
        $result = "";
        foreach ($roles as $role)
            $result .= $role->role->name.'<br>';
        return $result;
    }

    //-------------------------------------------

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub

        if ($this->roles !== null)
        {
            for ($i = 0; $i < count($this->roles); $i++)
            {
                $roleBind = UserRoleWork::find()->where(['user_id' => $this->id])->andWhere(['role_id' => $this->roles[$i]->role_id])->one();
                if ($roleBind == null) $roleBind = new UserRoleWork();
                $roleBind->user_id = $this->id;
                $roleBind->role_id = $this->roles[$i]->role_id;
                $roleBind->save();
            }
        }



        $arr = array($this->addUsers, $this->viewRoles, $this->editRoles, $this->viewOut, $this->editOut,
            $this->viewIn, $this->editIn, $this->viewOrder, $this->editOrder, $this->viewRegulation,
            $this->editRegulation, $this->viewEvent, $this->editEvent, $this->viewAS, $this->editAS,
            $this->viewAdd, $this->editAdd, $this->viewForeign, $this->editForeign, $this->viewProgram, $this->editProgram,
            $this->viewGroup, $this->editGroup, $this->viewGroupBranch, $this->editGroupBranch, $this->addGroup, $this->deleteGroup,
            $this->report);
        if ($changedAttributes['password_hash'] == null)
        {
            for ($i = 0; $i != count($arr); $i++)
            {
                /*
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
                */
            }

        }

    }

}