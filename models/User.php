<?php

namespace app\models;

class User extends \yii\base\BaseObject implements \yii\web\IdentityInterface {

    public $id;
    public $username;
    public $password;
    public $authKey;
    public $accessToken;
    private static $users = [
        '100' => [
            'id' => '100',
            'username' => '王欣欣',
            'password' => 'wxx',
            'authKey' => 'test100key',
            'accessToken' => '100-token',
        ],
        '101' => [
            'id' => '101',
            'username' => '郭文峰',
            'password' => 'gwf',
            'authKey' => 'test101key',
            'accessToken' => '101-token',
        ],
        '102' => [
            'id' => '102',
            'username' => '刘雅琳',
            'password' => 'lyl',
            'authKey' => 'test102key',
            'accessToken' => '102-token',
        ],
        '103' => [
            'id' => '103',
            'username' => '龚萱',
            'password' => 'gx',
            'authKey' => 'test103key',
            'accessToken' => '103-token',
        ],
        '104' => [
            'id' => '104',
            'username' => '李洁林',
            'password' => 'ljl',
            'authKey' => 'test104key',
            'accessToken' => '104-token',
        ],
        '105' => [
            'id' => '105',
            'username' => '金潇',
            'password' => 'jx',
            'authKey' => 'test105key',
            'accessToken' => '105-token',
        ],
        '106' => [
            'id' => '106',
            'username' => '吴疆',
            'password' => 'wj',
            'authKey' => 'test106key',
            'accessToken' => '106-token',
        ],
        '107' => [
            'id' => '107',
            'username' => '王宁',
            'password' => 'wn',
            'authKey' => 'test107key',
            'accessToken' => '107-token',
        ],
        '108' => [
            'id' => '108',
            'username' => '吴毓泽',
            'password' => 'wyz',
            'authKey' => 'test108key',
            'accessToken' => '108-token',
        ],
        '109' => [
            'id' => '109',
            'username' => '孙志伟',
            'password' => 'szw',
            'authKey' => 'test109key',
            'accessToken' => '109-token',
        ],
        '110' => [
            'id' => '110',
            'username' => '王嵇璇',
            'password' => 'wjx',
            'authKey' => 'test110key',
            'accessToken' => '110-token',
        ],
        '111' => [
            'id' => '111',
            'username' => '杨旭静',
            'password' => '123',
            'authKey' => 'test111key',
            'accessToken' => '111-token',
        ],
        '112' => [
            'id' => '112',
            'username' => '周秋月',
            'password' => 'zqy',
            'authKey' => 'test112key',
            'accessToken' => '112-token',
        ],
        '113' => [
            'id' => '113',
            'username' => '金潇',
            'password' => 'jx',
            'authKey' => 'test113key',
            'accessToken' => '113-token',
        ],
        '114' => [
            'id' => '114',
            'username' => '王宁',
            'password' => 'wn',
            'authKey' => 'test114key',
            'accessToken' => '114-token',
        ],
        '115' => [
            'id' => '115',
            'username' => '李洁林',
            'password' => 'ljl',
            'authKey' => 'test115key',
            'accessToken' => '115-token',
        ],
        '116' => [
            'id' => '116',
            'username' => 'qibuer',
            'password' => '123456',
            'authKey' => 'test116key',
            'accessToken' => '116-token',
        ],
    ];

    /**
     * @inheritdoc
     */
    public static function findIdentity($id) {
        return isset(self::$users[$id]) ? new static(self::$users[$id]) : null;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null) {
        foreach (self::$users as $user) {
            if ($user['accessToken'] === $token) {
                return new static($user);
            }
        }

        return null;
    }

    /**
     * Finds user by username
     *
     * @param  string      $username
     * @return static|null
     */
    public static function findByUsername($username) {
        foreach (self::$users as $user) {
            if (strcasecmp($user['username'], $username) === 0) {
                return new static($user);
            }
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey() {
        return $this->authKey;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey) {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param  string  $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password) {
        return $this->password === $password;
    }

}
