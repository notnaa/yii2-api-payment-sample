<?php

namespace app\models;

use app\components\db\ActiveRecord;
use app\models\forms\payment\ICurrencyDictionary;
use yii\db\ActiveQuery;

/**
 * Class UserWallet
 *
 * @package app\models
 *
 * @property int $id
 * @property int $user_id
 * @property float $balance
 * @property int $currency
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 *
 * @property User $user
 */
class UserWallet extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%user_wallets}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['user_id', 'currency'], 'required'],
            [['user_id', 'currency'], 'integer'],
            [['balance'], 'number'],
            [['currency'], 'in', 'range' => ICurrencyDictionary::SUPPORTED_CURRENCY],
            [['created_by', 'updated_by', 'created_at', 'updated_at'], 'safe'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'balance' => 'Balance',
            'currency' => 'Currency',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
