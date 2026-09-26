<?php

/**
 * @Author: Sigit
 * @Date:   2018-04-23 14:16:13
 * @Last Modified by:   iqbal@docotel
 * @Last Modified time: 2018-07-17 10:30:27
 */

namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "kelompokpemeriksaanlab_m".
 *
 * @property int $kelompokpemeriksaanlab_id
 * @property string $kode_kelompok
 * @property string $nama_kelompok
 * @property string $keterangan_kelompok
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
class KelompokPemeriksaanLabForm extends \yii\base\Model
{
    // Public property
    public $kelompokpemeriksaanlab_id;
    public $kode_kelompok;
    public $nama_kelompok;
    // public $keterangan_kelompok;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // [['kode_kelompok', 'nama_kelompok'], 'checkUnique', 'on' => 'insert'],
            [['kode_kelompok','nama_kelompok'], 'required', 'message'=>'{attribute} Tidak boleh kosong'],
            [['nama_kelompok'], 'checkUnique'],
            [['kode_kelompok'], 'string', 'max' => 25],
            [['nama_kelompok'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'kelompokpemeriksaanlab_id' => 'Kelompokpemeriksaanlab ID',
            'kode_kelompok' => 'Kode Kelompok',
            'nama_kelompok' => 'Kelompok Pemeriksaan',
            'keterangan_kelompok' => 'Kelompok Pemeriksaan',
        ];
    }

    public function checkUnique() {
        $nama_kelompok = $this->nama_kelompok;
        if (strpos(substr($nama_kelompok, 0, 1), ' ') !== FALSE) {
            $this->addError('nama_kelompok', 'Kelompok pemeriksaan mengandung spasi di awal kata');
            return false;
        }
        return true;
    }
}
?>
