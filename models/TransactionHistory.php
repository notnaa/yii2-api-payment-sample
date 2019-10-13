<?php

namespace app\models;

use app\components\db\ActiveRecord;
use app\models\forms\payment\ITransactionDictionary;
use yii\db\ActiveQuery;

/**
 * Class TransactionHistory
 *
 * @package app\models
 *
 * @property int $id
 * @property int $wallet_id
 * @property int $transaction_type
 * @property float $amount
 * @property float $balance
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 *
 * @property UserWallet $userWallet
 */
class TransactionHistory extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%transaction_history}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['wallet_id', 'transaction_type', 'amount', 'balance'], 'required'],
            [['wallet_id', 'transaction_type'], 'integer'],
            [['amount', 'balance'], 'number'],
            [['transaction_type'], 'in', 'range' => ITransactionDictionary::SUPPORTED_TRANSACTION],
            [['created_by', 'updated_by', 'created_at', 'updated_at'], 'safe'],
            [['wallet_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserWallet::className(), 'targetAttribute' => ['wallet_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'wallet_id' => 'Wallet ID',
            'transaction_type' => 'Transaction Type',
            'amount' => 'Amount',
            'balance' => 'Balance',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserWallet(): ActiveQuery
    {
        return $this->hasOne(UserWallet::className(), ['id' => 'wallet_id']);
    }
}
