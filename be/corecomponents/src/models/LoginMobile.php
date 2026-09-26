<?php

/**
 * @Author: Sigit
 * @Date:   2018-09-19 17:21:58
 */

namespace Doco\models;

use Yii;

/**
 * This is the model class for table "loginmobile_k".
 *
 * @property int $loginmobile_id
 * @property string $nama_pemakai
 * @property string $katakunci_pemakai
 * @property bool $statuslogin
 * @property bool $no_handphone
 * @property bool $email
 * @property string $link_aktifitas
 * @property string $akses_token
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

class LoginMobile extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'loginmobile_k';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nama_pemakai', 'katakunci_pemakai', 'email'], 'required', 'on' => 'brimob'],
            [['statuslogin', 'is_deleted', 'is_active'], 'boolean'],
            [['no_handphone'], 'default', 'value' => null],
            [['no_handphone', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['link_aktifitas', 'additional_data', 'player_id'], 'string'],
            [['loginmobile_id', 'created_date', 'last_modified_date', 'deleted_date', 'player_id'], 'safe'],
            [['nama_pemakai'], 'string', 'max' => 100],
            [['katakunci_pemakai', 'akses_token'], 'string', 'max' => 200],
            [['email'], 'string', 'max' => 255],
            [['nama_pemakai', 'email'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'loginmobile_id' => 'Login Mobile ID',
            'nama_pemakai' => 'Nama Pemakai',
            'katakunci_pemakai' => 'Kata Kunci Pemakai',
            'statuslogin' => 'Status Login',
            'no_handphone' => 'No Handphone',
            'email' => 'Email',
            'link_aktifitas' => 'Link Aktifitas',
            'akses_token' => 'Akses Token',
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