<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "bot_message".
 *
 * @property int $id
 * @property string $text
 *
 * @property BotMessageVariant[] $botMessageVariants
 */
class BotMessage extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bot_message';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['text'], 'required'],
            [['text'], 'string', 'max' => 1000],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'text' => 'Text',
        ];
    }

    /**
     * Gets query for [[BotMessageVariants]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBotMessageVariants()
    {
        return $this->hasMany(BotMessageVariant::className(), ['bot_message_id' => 'id']);
    }
}
