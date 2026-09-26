<?php

/**
 * @Author  : M.ilhamsyah.P
 * @Date    : 2020-08-10 13:23:53
 * @Last Modified by    :  
 * @Last Modified time  :  
 * @Description : membuat laporan pasien konsul
 */
namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pasien_v".
 *
 * @property int $pasien_id
 * @property string $no_rekam_medik
 * @property string $tgl_rekam_medik
 * @property string $nama_pasien
 * @property string $tanggal_lahir
 */
class PasienKonsul extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'laporankonsul_v';
    }

}
