<?php

namespace app\models;

use app\components\db\ActiveRecord;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;

/**
 * Class User
 *
 * @package app\models
 *
 * @property integer $id
 * @property string $username
 * @property string $email
 * @property string $password
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $created_at
 * @property integer $updated_at
 */
class User extends ActiveRecord implements IdentityInterface
{
    /** @var int */
    public const STATUS_ACTIVE = 1;
    /** @var int */
    public const STATUS_INACTIVE = 0;
    /** @var string */
    public const SCENARIO_UPDATE = 'update';

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%user}}';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['id', 'status'], 'integer'],
            [['username', 'email', 'password', 'auth_key'], 'string'],
            [['email', 'password'], 'required'],
            [['password'], 'validateChangePassword', 'skipOnEmpty' => false],
            [['username', 'email', 'password'], 'filter', 'filter' => 'trim', 'skipOnArray' => true],
            [['username', 'email'], 'unique', 'on' => self::SCENARIO_DEFAULT],
            ['email', 'email'],
            [['status'], 'default', 'value' => self::STATUS_ACTIVE],
        ];
    }

    /**
     * @return array
     */
    public function scenarios(): array
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_UPDATE] = ['email', 'password'];

        return $scenarios;
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'username' => 'Логин',
            'email' => 'Email',
            'password' => 'Пароль',
            'visible' => 'Видимость',
            'status' => 'Статус',
            'created_by' => 'Автор',
            'created_at' => 'Дата публикации',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * @inheritdoc
     * @throws NotSupportedException
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * @param string $username
     * @return User
     */
    public static function findByUsername(string $username): User
    {
        $user = static::find()
            ->where(['username' => $username])
            ->one();

        if (!empty($user)) {
            return new static($user);
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @return array
     */
    public static function getListStatuses(): array
    {
        return [
            self::STATUS_INACTIVE => 'Отключен',
            self::STATUS_ACTIVE => 'Активен',
        ];
    }
}
