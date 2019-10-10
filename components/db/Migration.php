<?php

namespace app\components\db;

use Yii;

/**
 * Class Migration
 *
 * @package app\components\db
 */
class Migration extends \yii\db\Migration
{
    /** @var string */
    public const CASCADE = 'CASCADE';
    /** @var string */
    public const RESTRICT = 'RESTRICT';
    /** @var string */
    public const SET_NULL = 'SET NULL';
    /** @var string */
    public const NO_ACTION = 'NO ACTION';

    /** @var null|string */
    protected $tableOptions = null;
    /** @var \yii\rbac\DbManager */
    protected $authManager = null;
    /** @var string */
    protected $pkPrefix = 'PK';
    /** @var string */
    protected $idxPrefix = 'IDX';
    /** @var string */
    protected $uniPrefix = 'UNI';
    /** @var string */
    protected $fkPrefix = 'FK';
    /** @var string */
    protected $timestamp = 'CURRENT_TIMESTAMP';

    /**
     * @param array $config
     */
    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->db->enableSchemaCache = false;

        $this->authManager = Yii::$app->getAuthManager();
    }

    /**
     * @param string|null $name
     * @param string $table
     * @param string|array $columns
     */
    public function addPrimaryKey($name, $table, $columns)
    {
        if (is_null($name)) {
            $name = $this->getPkName($table, $columns);
        }

        parent::addPrimaryKey($name, $table, $columns);
    }

    /**
     * Disable checked foreign key
     */
    public function disableFK()
    {
        Yii::$app->db->createCommand("SET FOREIGN_KEY_CHECKS=0;")->execute();
    }

    /**
     * Enable checked foreign key
     */
    public function enableFK()
    {
        Yii::$app->db->createCommand("SET FOREIGN_KEY_CHECKS=0;")->execute();
    }

    /**
     * @param $table
     * @param $columns
     * @return string
     */
    public function getPkName($table, $columns)
    {
        $table = $this->db->schema->getRawTableName($table);

        return strtolower(sprintf(
            '%s_%s',
            $this->pkPrefix,
            $table . '_' . implode('', (array)$columns)
        ));
    }

    /**
     * @param $columns
     * @return string
     */
    public function includePrimaryKey($columns)
    {
        $columns = is_array($columns) ? implode(',', $columns) : $columns;

        return ('PRIMARY KEY (' . $columns . ')');
    }

    /**
     * @param string|null $name
     * @param string $table
     * @param array|string $columns
     * @param boolean|false $unique
     */
    public function createIndex($name, $table, $columns, $unique = false)
    {
        if (is_null($name)) {
            if ($unique) {
                $name = $this->getUniName($table, $columns);
            } else {
                $name = $this->getIdxName($table, $columns);
            }
        }

        parent::createIndex($name, $table, $columns, $unique);
    }

    /**
     * @param string $table
     * @param string|array $columns
     * @return string
     */
    public function getUniName($table, $columns)
    {
        $table = $this->db->schema->getRawTableName($table);

        return strtolower(sprintf(
            '%s_%s',
            $this->uniPrefix,
            $table . '_' . implode('', (array)$columns)
        ));
    }

    /**
     * @param string $table
     * @param string|array $columns
     * @return string
     */
    public function getIdxName($table, $columns)
    {
        $table = $this->db->schema->getRawTableName($table);

        return strtolower(sprintf(
            '%s_%s',
            $this->idxPrefix,
            $table . '_' . implode('', (array)$columns)
        ));
    }

    /**
     * @param string|null $name
     * @param string $table
     * @param string $columns
     * @param array|string $refTable
     * @param string $refColumns
     * @param null $delete
     * @param null $update
     * @throws \yii\db\Exception
     */
    public function addForeignKey($name, $table, $columns, $refTable, $refColumns, $delete = null, $update = null)
    {
        if (is_null($name)) {
            $name = $this->getFkName($table, $columns);
        }

        if (!$this->existsFk($name)) {
            parent::addForeignKey($name, $table, $columns, $refTable, $refColumns, $delete, $update);
        }
    }

    /**
     * @param string $name
     * @param string $table
     * @throws \yii\db\Exception
     */
    public function dropForeignKey($name, $table)
    {
        if ($this->existsFk($name)) {
            parent::dropForeignKey($name, $table);
        }
    }

    /**
     * @param string $table
     * @param string|array $columns
     * @return string
     */
    public function getFkName($table, $columns)
    {
        $table = $this->db->schema->getRawTableName($table);

        return strtolower(substr(sprintf(
            '%s_%s_%s',
            $this->fkPrefix,
            $table,
            $columns
        ), 0, 64));
    }

    /**
     * Включение/отключение проверки внешних ключей
     *
     * @param bool $value
     * @throws \yii\base\NotSupportedException
     * @throws \yii\db\Exception
     */
    public function checkFk($value = true)
    {
        Yii::$app->db
            ->createCommand()
            ->checkIntegrity($value)
            ->execute();
    }

    /**
     * Проверка существования внешнего ключа
     *
     * @param $fkName
     * @return false|null|string
     * @throws \yii\db\Exception
     */
    public function existsFk($fkName)
    {
        return Yii::$app->db
            ->createCommand(
                "SELECT EXISTS(SELECT * FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS "
                . " WHERE CONSTRAINT_SCHEMA = DATABASE() AND CONSTRAINT_TYPE = 'FOREIGN KEY' AND CONSTRAINT_NAME = :fk)",
                [':fk' => $fkName]
            )
            ->queryScalar();
    }

    /**
     * Проверка существования первичного ключа
     *
     * @param $pkName
     * @return false|null|string
     * @throws \yii\db\Exception
     */
    protected function existsPk($pkName)
    {
        return Yii::$app->db
            ->createCommand(
                "SELECT EXISTS(SELECT * FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS "
                . " WHERE CONSTRAINT_SCHEMA = DATABASE() AND CONSTRAINT_TYPE = 'PRIMARY KEY' AND CONSTRAINT_NAME = :pk)",
                [':pk' => $pkName]
            )
            ->queryScalar();
    }
}
