<?php

namespace app\modules\dcms\models;

use Yii;

/**
 * This is the model class for table "carabayar_m".
 *
 * @property integer $carabayar_id
 * @property string $carabayar_nama
 * @property string $carabayar_namalainnya
 * @property string $metode_pembayaran
 * @property string $carabayar_loket
 * @property string $carabayar_singkatan
 * @property integer $carabayar_urutan
 * @property boolean $is_subsidiasuransi
 * @property boolean $is_subsidipemerintah
 * @property boolean $is_subsidirs
 * @property string $additional_data
 * @property string $created_date
 * @property integer $created_by
 * @property integer $modified_count
 * @property string $last_modified_date
 * @property integer $last_modified_by
 * @property boolean $is_deleted
 * @property boolean $is_active
 * @property string $deleted_date
 * @property integer $deleted_by
 */
class ProfileForm extends \yii\base\Model
{
    public $password_old;
    public $password_new;
    public $password_confirm;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'carabayar_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['password_old', 'password_new', 'password_confirm'], 'required']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'password_old' => Yii::t('app', 'Kata sandi lama'),
            'password_new' => Yii::t('app', 'Kata sandi baru'),
            'password_confirm' => Yii::t('app', 'Konfirmasi kata sandi'),
        ];
    }

    
}
