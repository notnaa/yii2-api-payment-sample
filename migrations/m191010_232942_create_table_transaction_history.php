<?php

use app\components\db\Migration;

/**
* Class m191010_232942_create_table_transaction_history
*/
class m191010_232942_create_table_transaction_history extends Migration
{
    /** @var string */
    private $tableName = '{{%transaction_history}}';

    /**
     * @inheritdoc
     * @throws \yii\db\Exception
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'wallet_id' => $this->integer()->notNull(),
            'amount' => $this->decimal(17, 10)->notNull(),
            'balance' => $this->decimal(17, 10)->notNull(),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
            'created_at' => $this->integer(14),
            'updated_at' => $this->integer(14),
        ]);

        $this->addForeignKey(
            'fk-transaction_history-wallet_id',
            $this->tableName,
            'wallet_id',
            '{{%user_wallets}}',
            'id'
        );
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
