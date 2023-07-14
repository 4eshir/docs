<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "bot_message_variant_test".
 *
 * @property int $id
 * @property int $bot_message_variant_id
 *
 * @property BotMessageVariantTest $botMessageVariant
 * @property BotMessageVariantTest[] $botMessageVariantTests
 */
class BotMessageVariantTest extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bot_message_variant_test';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bot_message_variant_id'], 'required'],
            [['bot_message_variant_id'], 'integer'],
            [['bot_message_variant_id'], 'exist', 'skipOnError' => true, 'targetClass' => BotMessageVariantTest::className(), 'targetAttribute' => ['bot_message_variant_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'bot_message_variant_id' => 'Bot Message Variant ID',
        ];
    }

    /**
     * Gets query for [[BotMessageVariant]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBotMessageVariant()
    {
        return $this->hasOne(BotMessageVariantTest::className(), ['id' => 'bot_message_variant_id']);
    }

    /**
     * Gets query for [[BotMessageVariantTests]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBotMessageVariantTests()
    {
        return $this->hasMany(BotMessageVariantTest::className(), ['bot_message_variant_id' => 'id']);
    }
}
