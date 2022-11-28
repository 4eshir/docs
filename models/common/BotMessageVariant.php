<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "bot_message_variant".
 *
 * @property int $id
 * @property int $bot_message_id
 * @property string $text
 * @property string $picture
 * @property int|null $next_bot_message_id
 *
 * @property BotMessage $botMessage
 */
class BotMessageVariant extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bot_message_variant';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bot_message_id', 'text', 'picture'], 'required'],
            [['bot_message_id', 'next_bot_message_id'], 'integer'],
            [['text', 'picture'], 'string', 'max' => 1000],
            [['bot_message_id'], 'exist', 'skipOnError' => true, 'targetClass' => BotMessage::className(), 'targetAttribute' => ['bot_message_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'bot_message_id' => 'Bot Message ID',
            'text' => 'Text',
            'picture' => 'Picture',
            'next_bot_message_id' => 'Next Bot Message ID',
        ];
    }

    /**
     * Gets query for [[BotMessage]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBotMessage()
    {
        return $this->hasOne(BotMessage::className(), ['id' => 'bot_message_id']);
    }
}
