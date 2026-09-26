<?php

/**
 * @author Randy Vianda Putra
 * @todo Transaksi Resep Form
 * @copyright 15 January 2018 aweutist
 */

namespace Doco\laboratorium\models;

use Yii;
use yii\web\UploadedFile;
use yii\validators\UniqueValidator;
use yii\db\Query;
class SpecimentForm extends \yii\db\ActiveRecord
{
    public $tanggal;
    public $samplelab_id;
    public $jumlah;
    public $satuanlab_id;
    public $keterangan;
    public $pemeriksaan;
    public $daftartindakan_id;
    public $tindakanpelayanan_id;
    public $nama_pemeriksaan;
    public $pasienmasukpenunjang_id;
    public $list_paket;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ambilsample_t';
    }

    /**
     * @todo Rule for Form Obat Alkes Jenis Kasus Penyakit
     * @return void
     */
    public function rules()
    {
        return [
            [['tanggal', 'samplelab_id', 'jumlah', 'satuanlab_id', 'list_paket'], 'required', 'on' => 'paket'],
            [['tanggal', 'samplelab_id', 'jumlah', 'satuanlab_id', 'pemeriksaan'], 'required', 'on' => 'lab'],
            [['tanggal', 'samplelab_id', 'jumlah', 'satuanlab_id', 'pemeriksaan', 'list_paket'], 'safe']
        ];
    }


    /**
     * @todo for attribute label form
     */
    public function attributeLabels()
    {
        return [
            'samplelab_id' => \Yii::t('fe', 'Nama speciment'),
            'satuanlab_id' => \Yii::t('fe', 'Satuan'),
            'pemeriksaan' => \Yii::t('fe', 'List Tindakan'),
        ];
    }

    public function checkUnique() {
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $pegawai_id = Yii::$app->docoVars->user('id_pegawai');
        $cacheTrans = Yii::$app->cache->get('addSampleLab' . $ruangan_id . '-' . $pegawai_id);
        $transLab = json_decode($cacheTrans, true);
    }
}
