<?php

use app\components\db\Migration;

/**
* Class m191010_231812_create_table_user_wallets
*/
class m191010_231812_create_table_user_wallets extends Migration
{
    /** @var string */
    private $tableName = '{{%user_wallets}}';

    /**
     * @inheritdoc
     * @throws \yii\db\Exception
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'balance' => $this->decimal(17, 10)->notNull()->defaultValue(0.00),
            'currency' => $this->smallInteger(1)->notNull(),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
            'created_at' => $this->integer(14),
            'updated_at' => $this->integer(14),
        ]);

        $this->addForeignKey(
            'fk-user_wallets-user_id',
            $this->tableName,
            'user_id',
            '{{%user}}',
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
