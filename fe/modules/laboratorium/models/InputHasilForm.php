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

class InputHasilForm extends \yii\db\ActiveRecord
{
    public $tanggal;
    public $no_hasil;
    public $penanggungjawab;
    public $upload;
    public $pegawai_id;
    public $is_verifikasi;
    public $petugas;

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
            [['tanggal', 'penanggungjawab','petugas'], 'required'],
            [['tanggal', 'no_hasil', 'penanggungjawab', 'is_verifikasi'], 'safe']
        ];
    }


    /**
     * @todo for attribute label form
     */
    public function attributeLabels()
    {
        return [
            'tanggal' => \Yii::t('fe', 'Tanggal hasil laboratorium'),
            'no_hasil' => \Yii::t('fe', 'No hasil'),
            'penanggungjawab' => \Yii::t('fe', 'Penanggung jawab laboratorium'),
            'upload' => \Yii::t('fe', 'Upload file'),
        ];
    }

}
