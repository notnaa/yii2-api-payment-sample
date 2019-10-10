<?php

/**
 * Class m180128_195453_create_table_user
 */
class m180128_195453_create_table_user extends Migration
{
    /** @var string */
    private $tableName = '{{%user}}';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'username' => $this->string(25)->unique(),
            'email' => $this->string()->unique(),
            'password' => $this->string(),
            'auth_key' => $this->string(),
            'status' => $this->integer(1),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
            'created_at' => $this->integer(14),
            'updated_at' => $this->integer(14),
        ]);

        $this->createIndex(
            'idx-user-username',
            $this->tableName,
            'username'
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
