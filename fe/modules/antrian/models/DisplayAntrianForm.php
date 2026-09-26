<?php

/**
 * @author Randy Vianda Putra
 * @todo Transaksi Resep Form
 * @copyright 15 January 2018 aweutist
 */

namespace Doco\antrian\models;

use Yii;
use yii\web\UploadedFile;
use yii\validators\UniqueValidator;
use yii\db\Query;
class DisplayAntrianForm extends \yii\db\ActiveRecord
{
    public $jenisantrian_id;
    public $layarantrian_nama;
    public $pegawai_id;
    public $layarantrian_latarbelakang;
    public $is_active;
    public $satuan;
    public $stok;
    public $instalasi_id;
    public $ruangan_id;
    public $loket_id;
    public $file;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'layarantrian_m';
    }

    /**
     * @todo Rule for Form Obat Alkes Jenis Kasus Penyakit
     * @return void
     */
    public function rules()
    {
        return [
            // [['layarantrian_nama'], 'checkUnique'],
            [['jenisantrian_id', 'layarantrian_nama', 'pegawai_id'], 'required', 'on' => 'poli'],
            [['jenisantrian_id', 'layarantrian_nama', 'loket_id'], 'required', 'on' => 'loket'],
            [['layarantrian_latarbelakang'], 'file', 'extensions' => 'jpg, jpeg, png, gif'],
            [['stok', 'instalasi_tujuan', 'ruangan_tujuan', 'layarantrian_latarbelakang', 'loket_id'], 'safe']
        ];
    }


    /**
     * @todo for attribute label form
     */
    public function attributeLabels()
    {
        return [
            'jenisantrian_id' => \Yii::t('fe', 'Jenis Layar Antrian'),
            'layarantrian_nama' => \Yii::t('fe', 'Nama Layar Antrian'),
            'is_active' => \Yii::t('fe', 'Status Aktif'),
            'ruangan_id' => \Yii::t('fe', 'Ruangan'),
            'pegawai_id' => \Yii::t('fe', 'Pegawai'),
            'loket_id' => \Yii::t('fe', 'Loket'),
            'layarantrian_latarbelakang' => \Yii::t('fe', 'Latar belakang'),
            'qty' => 'Qty',
        ];
    }

    public function upload()
    {
        if ($this->validate()) {
            $path = \Yii::getAlias('@webroot');
            $this->layarantrian_latarbelakang->saveAs($path. '/media/img/display-antrian/' . $this->layarantrian_latarbelakang->baseName . '.' . $this->layarantrian_latarbelakang->extension);
            return true;
        } else {
            return false;
        }
    }

    public function checkUnique() {
        $instalasi = Yii::$app->docoVars->workspace("instalasi_id");
        $layarantrian_nama = $this->layarantrian_nama;
        $data_display = Yii::$app->cache->get("data-display-{$instalasi}");
        if ($data_display !== false) {
            foreach ($data_display as $value) {
                if (preg_match("/\b({$layarantrian_nama})\b/i", @$value['layarantrian_nama'])) {
                    $this->addError('layarantrian_nama','Nama Layar Antrian Sudah Terpakai');
                    return false;
                }
            }
        }
        return true;
    }
}
