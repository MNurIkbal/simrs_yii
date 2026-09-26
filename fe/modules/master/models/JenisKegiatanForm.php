<?php

/**
 * @Author: Sigit
 * @Date:   2019-03-22 15:59:26
 */

namespace app\modules\master\models;

use Yii;

/**
 * @property string $jeniskegiatantindakan_kode
 * @property string $jeniskegiatantindakan_nama
 * @property bool $is_active
 */

class JenisKegiatanForm extends \yii\base\Model
{
    /**
     * @inheritdoc
     */
    public $jeniskegiatantindakan_kode;
    public $jeniskegiatantindakan_nama;
    public $jeniskegiatan_namalainnya;
    public $jeniskegiatan_keterangan;
    public $is_active;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['jeniskegiatantindakan_kode', 'jeniskegiatantindakan_nama'], 'required'],
            [['is_active'], 'boolean'],
            [['jeniskegiatantindakan_kode'], 'string', 'max' => 25],
            [['jeniskegiatantindakan_nama'], 'string', 'max' => 100],
            [['jeniskegiatan_namalainnya'], 'string', 'max' => 100],
            [['jeniskegiatan_keterangan'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'jeniskegiatantindakan_id' => Yii::t('fe', 'ID'),
            'jeniskegiatantindakan_kode' => Yii::t('fe', 'Kode Kegiatan'),
            'jeniskegiatantindakan_nama' => Yii::t('fe', 'Nama Kegiatan'),
            'jeniskegiatan_namalainnya' => Yii::t('fe', 'Nama Lainnya'),
            'jeniskegiatan_keterangan' => Yii::t('fe', 'Keterangan'),
            'is_active' => Yii::t('fe', 'Status'),
        ];
    }
}