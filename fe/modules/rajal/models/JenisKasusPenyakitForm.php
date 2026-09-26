<?php

/**
 * @Author: afil
 * @Date:   2018-01-03 13:46:58
 * @Last Modified by:   afil
 * @Last Modified time: 2018-01-22 17:25:06
 * @Description: 
 */
namespace app\modules\rajal\models;

use Yii;

/**
 * This is the model class for table "jeniskasuspenyakit_m".
 *
 * @property integer $jeniskasuspenyakit_id
 * @property string $jeniskasuspenyakit_nama
 * @property string $jeniskasuspenyakit_namalainnya
 * @property integer $jeniskasuspenyakit_urutan
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
 *
 * @property KasuspenyakitdiagnosaMp[] $kasuspenyakitdiagnosaMps
 * @property DiagnosaM[] $diagnosas
 * @property KasuspenyakitobatMp[] $kasuspenyakitobatMps
 * @property KasuspenyakitruanganMp[] $kasuspenyakitruanganMps
 * @property RuanganM[] $ruangans
 * @property PasienmasukpenunjangT[] $pasienmasukpenunjangTs
 * @property PendaftaranT[] $pendaftaranTs
 * @property TindakanpelayananT[] $tindakanpelayananTs
 */
class JenisKasusPenyakitForm extends \yii\base\Model
{
    public $jeniskasuspenyakit_id;
    public $jeniskasuspenyakit_nama;
    public $jeniskasuspenyakit_namalainnya;
    public $jeniskasuspenyakit_urutan;
    public $additional_data;
    public $created_date;
    public $created_by;
    public $modified_count;
    public $last_modified_date;
    public $last_modified_by;
    public $is_deleted;
    public $is_active;
    public $deleted_date;
    public $deleted_by;

    /**
     *
     * @desc : variable kebutuhan lainnya
     *
     */
    
    public $select_kasus;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'jeniskasuspenyakit_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['jeniskasuspenyakit_nama'], 'required'],
            [['jeniskasuspenyakit_urutan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['jeniskasuspenyakit_nama', 'jeniskasuspenyakit_namalainnya'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'jeniskasuspenyakit_id' => Yii::t('fe', 'Jenis kasus penyakit'),
            'jeniskasuspenyakit_nama' => Yii::t('fe', 'Nama jenis kasus penyakit'),
            'jeniskasuspenyakit_namalainnya' => Yii::t('fe', 'Nama lain jenis kasus penyakit'),
            'jeniskasuspenyakit_urutan' => Yii::t('fe', 'Urutan jenis kasus penyakit'),
            'additional_data' => \Yii::t('fe','Additional data'),
            'created_date' => \Yii::t('fe','Created date'),
            'created_by' => \Yii::t('fe','Created by'),
            'modified_count' => \Yii::t('fe','Modified count'),
            'last_modified_date' => \Yii::t('fe','Last modified date'),
            'last_modified_by' => \Yii::t('fe','Last modified by'),
            'is_deleted' => \Yii::t('fe','Is deleted'),
            'is_active' => \Yii::t('fe','Status'),
            'deleted_date' => \Yii::t('fe','Deleted date'),
            'deleted_by' => \Yii::t('fe','Deleted by'),
        ];
    }
}
