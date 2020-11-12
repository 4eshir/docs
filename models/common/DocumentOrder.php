<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "document_order".
 *
 * @property int $id
 * @property string $order_number
 * @property string $order_name
 * @property string $order_date
 * @property int $signed_id
 * @property int $bring_id
 * @property int $executor_id
 * @property int $scan
 * @property int $register_id
 *
 * @property People $bring
 * @property People $executor
 * @property People $register
 * @property People $signed
 * @property Responsible[] $responsibles
 */
class DocumentOrder extends \yii\db\ActiveRecord
{
    public $scanFile;
    public $responsibles;
    public $people_arr;

    public $signedString;
    public $executorString;
    public $bringString;
    public $registerString;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'document_order';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['scanFile'], 'file', 'extensions' => 'jpg, png, pdf, doc, docx', 'skipOnEmpty' => true],
            [['signedString', 'executorString', 'bringString', 'registerString'], 'string'],
            [['order_number', 'order_name', 'order_date', 'signed_id', 'bring_id', 'executor_id', 'register_id',
              'signedString', 'executorString', 'bringString'], 'required'],
            [['order_number', 'signed_id', 'bring_id', 'executor_id', 'register_id'], 'integer'],
            [['order_date'], 'safe'],
            [['order_name', 'scan'], 'string', 'max' => 1000],
            [['order_number'], 'string', 'max' => 100],
            [['order_number'], 'unique'],
            [['bring_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['bring_id' => 'id']],
            [['executor_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['executor_id' => 'id']],
            [['register_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['register_id' => 'id']],
            [['signed_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['signed_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_number' => 'Order Number',
            'order_name' => 'Order Name',
            'order_date' => 'Order Date',
            'signed_id' => 'Signed ID',
            'bring_id' => 'Bring ID',
            'executor_id' => 'Executor ID',
            'scan' => 'Scan',
            'register_id' => 'Register ID',
        ];
    }

    /**
     * Gets query for [[Bring]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBring()
    {
        return $this->hasOne(People::className(), ['id' => 'bring_id']);
    }

    /**
     * Gets query for [[Executor]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExecutor()
    {
        return $this->hasOne(People::className(), ['id' => 'executor_id']);
    }

    /**
     * Gets query for [[Register]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRegister()
    {
        return $this->hasOne(User::className(), ['id' => 'register_id']);
    }

    /**
     * Gets query for [[Signed]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSigned()
    {
        return $this->hasOne(People::className(), ['id' => 'signed_id']);
    }

    /**
     * Gets query for [[Responsibles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getResponsibles()
    {
        return $this->hasMany(Responsible::className(), ['document_order_id' => 'id']);
    }

    public function  beforeSave($insert)
    {

        $fioSigned = explode(" ", $this->signedString);
        $fioExecutor = explode(" ", $this->executorString);
        $fioRegister = explode(" ", $this->registerString);
        $fioBring = explode(" ", $this->bringString);

        $fioSignedDb = People::find()->where(['secondname' => $fioSigned[0]])
            ->andWhere(['firstname' => $fioSigned[1]])
            ->andWhere(['patronymic' => $fioSigned[2]])->one();
        $fioExecutorDb = People::find()->where(['secondname' => $fioExecutor[0]])
            ->andWhere(['firstname' => $fioExecutor[1]])
            ->andWhere(['patronymic' => $fioExecutor[2]])->one();
        $fioRegisterDb = People::find()->where(['secondname' => $fioRegister[0]])
            ->andWhere(['firstname' => $fioRegister[1]])
            ->andWhere(['patronymic' => $fioRegister[2]])->one();
        $fioBringDb = People::find()->where(['secondname' => $fioBring[0]])
            ->andWhere(['firstname' => $fioBring[1]])
            ->andWhere(['patronymic' => $fioBring[2]])->one();

        if ($fioSignedDb !== null)
            $this->signed_id = $fioSignedDb->id;

        if ($fioExecutorDb !== null)
            $this->executor_id = $fioExecutorDb->id;

        if ($fioRegisterDb !== null)
            $this->register_id = $fioRegisterDb->id;

        if ($fioRegisterDb !== null)
            $this->bring_id = $fioBringDb->id;

        $this->register_id = Yii::$app->user->identity->getId();

        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    public function uploadScanFile()
    {
        $path = '@app/upload/files/order/';
        $date = $this->order_date;
        $new_date = '';
        for ($i = 0; $i < strlen($date); ++$i)
            if ($date[$i] != '-')
                $new_date = $new_date.$date[$i];
        $filename = 'П.'.$new_date.'_'.$this->order_number.'-'.$this->id.'_'.$this->order_name;
        $this->scan = $filename . '.' . $this->scanFile->extension;
        $this->scanFile->saveAs( $path . $filename . '.' . $this->scanFile->extension);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub

        if (true)
        {
            $resp = [new Responsible];
            $resp = $this->responsibles;
            for ($i = 0; $i < count($resp); $i++)
            {
                $split = explode(" ", $resp[$i]->fio);
                $p_id = People::find()->where(['firstname' => $split[1]])->andWhere(['secondname' => $split[0]])
                    ->andWhere(['patronymic' => $split[2]])->one()->id;
                if (!$this->IsResponsibleDuplicate($p_id)) {
                    $resp[$i]->people_id = $p_id;
                    $resp[$i]->document_order_id = $this->id;
                    $resp[$i]->save();
                }
            }
        }
    }

    public function beforeDelete()
    {
        $resp = Responsible::find()->where(['document_order_id' => $this->id])->all();
        foreach ($resp as $respOne)
            $respOne->delete();
        return parent::beforeDelete(); // TODO: Change the autogenerated stub
    }

    private function IsResponsibleDuplicate($people_id)
    {
        if (count(Responsible::find()->where(['document_order_id' => $this->id])->andWhere(['people_id' => $people_id])->all()) > 0)
        {
            $fio = People::find()->where(['id' => $people_id])->one();
            Yii::$app->session->addFlash('error', 'Повторное добавление ответственного: '.
                $fio->secondname.' '.$fio->firstname.' '.$fio->patronymic);
            return true;
        }
        return false;
    }
}
