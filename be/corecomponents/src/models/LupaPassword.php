<?php

/**
 * @Author: Sigit
 * @Date:   2018-10-01 14:04:47
 */

namespace Doco\models;

use Yii;

/**
 * This is the model class for table "lupapass_k".
 *
 * @property int $lupapass_id
 * @property int $loginmobile_id
 * @property string $email
 * @property string $pass_baru
 * @property bool $is_konfirmasi
 * @property string $additional_data
 * @property string $created_date
 * @property int $created_by
 * @property int $modified_count
 * @property string $last_modified_date
 * @property int $last_modified_by
 * @property bool $is_deleted
 * @property bool $is_active
 * @property string $deleted_date
 * @property int $deleted_by
 */
class LupaPassword extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lupapass_k';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['loginmobile_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['loginmobile_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['email'], 'required'],
            [['is_konfirmasi', 'is_deleted', 'is_active'], 'boolean'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['email', 'pass_baru'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'lupapass_id' => 'Lupa Password ID',
            'loginmobile_id' => 'Login Mobile ID',
            'email' => 'Email',
            'pass_baru' => 'Password Baru',
            'is_konfirmasi' => 'Is Konfirmasi',
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By',
        ];
    }
}
