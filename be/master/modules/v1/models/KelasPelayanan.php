<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "kelaspelayanan_m".
 *
 * @property integer $kelaspelayanan_id
 * @property integer $jeniskelas_id
 * @property string $kelaspelayanan_nama
 * @property string $kelaspelayanan_namalainnya
 * @property double $persentasirujin
 * @property integer $urutankelas
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
 * @property AsuransipasienM[] $asuransipasienMs
 * @property BookingkamarT[] $bookingkamarTs
 * @property KamarruanganM[] $kamarruanganMs
 * @property JeniskelasM $jeniskelas
 * @property JenisKelas $jeniskelas
 * @property MasukkamarT[] $masukkamarTs
 * @property PasienadmisiT[] $pasienadmisiTs
 * @property PasienkirimkeunitlainT[] $pasienkirimkeunitlainTs
 * @property PasienmasukpenunjangT[] $pasienmasukpenunjangTs
 * @property PendaftaranT[] $pendaftaranTs
 * @property PenjualanresepT[] $penjualanresepTs
 * @property PindahkamarT[] $pindahkamarTs
 * @property TanggunganpenjaminM[] $tanggunganpenjaminMs
 * @property TariftindakanM[] $tariftindakanMs
 * @property TindakanpelayananT[] $tindakanpelayananTs
 * @property TindakanpelayananT[] $tindakanpelayananTs0
 * @property TipepaketM[] $tipepaketMs
 */
class KelasPelayanan extends \Doco\components\DocoActiveRecord
{
    protected $xssProtected = [
        'kelaspelayanan_nama',
        'kelaspelayanan_namalainnya'
    ];
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'kelaspelayanan_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['jeniskelas_id', 'kelaspelayanan_nama'], 'required'],
            [['kelaspelayanan_nama'], 'checkUnique'],
            [['jeniskelas_id', 'urutankelas', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['persentasirujin'], 'number'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date','persentasirujin', 'kelaspelayanan_kode'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['kelaspelayanan_nama', 'kelaspelayanan_namalainnya', 'kelaspelayanan_kode'], 'string', 'max' => 50]
        ];
    }

    public function checkUnique($attribute, $params)
    {
        $request = Yii::$app->request;
        $kelaspelayanan_nama = $this->kelaspelayanan_nama;
        $jeniskelas_id = $request->post('jeniskelas_id');
        $query = KelasPelayanan::find()->where([
            'kelaspelayanan_nama' => $kelaspelayanan_nama,
            'jeniskelas_id' => $jeniskelas_id
        ])->one();

        if (!empty($query)) {
            if ($this->kelaspelayanan_id != $query->kelaspelayanan_id) {
                $this->addError('kelaspelayanan_nama','"'.$kelaspelayanan_nama.'" Nama Sudah Terpakai');
                return false;
            }
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'jeniskelas_id' => 'Jeniskelas ID',
            'kelaspelayanan_nama' => 'Kelaspelayanan Nama',
            'kelaspelayanan_namalainnya' => 'Kelaspelayanan Namalainnya',
            'persentasirujin' => 'Persentasirujin',
            'urutankelas' => 'Urutankelas',
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
            'kelaspelayanan_kode' => 'Kelaspelayanan Kode',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAsuransipasienMs()
    {
        return $this->hasMany(AsuransipasienM::className(), ['kelastanggunganasuransi_id' => 'kelaspelayanan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBookingkamarTs()
    {
        return $this->hasMany(Bookingkamar::className(), ['kelaspelayanan_id' => 'kelaspelayanan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKamarruanganMs()
    {
        return $this->hasMany(Kamarruangan::className(), ['kelaspelayanan_id' => 'kelaspelayanan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJenisKelas()
    {
        return $this->hasOne(JenisKelas::className(), ['jeniskelas_id' => 'jeniskelas_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMasukkamarTs()
    {
        return $this->hasMany(Masukkamar::className(), ['kelaspelayanan_id' => 'kelaspelayanan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasienadmisiTs()
    {
        return $this->hasMany(Pasienadmisi::className(), ['kelaspelayanan_id' => 'kelaspelayanan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasienkirimkeunitlainTs()
    {
        return $this->hasMany(Pasienkirimkeunitlain::className(), ['kelaspelayanan_id' => 'kelaspelayanan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasienmasukpenunjangTs()
    {
        return $this->hasMany(Pasienmasukpenunjang::className(), ['kelaspelayanan_id' => 'kelaspelayanan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPendaftaranTs()
    {
        return $this->hasMany(Pendaftaran::className(), ['kelaspelayanan_id' => 'kelaspelayanan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPenjualanresepTs()
    {
        return $this->hasMany(Penjualanresep::className(), ['kelaspelayanan_id' => 'kelaspelayanan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPindahkamarTs()
    {
        return $this->hasMany(Pindahkamar::className(), ['kelaspelayanan_id' => 'kelaspelayanan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTanggunganpenjaminMs()
    {
        return $this->hasMany(Tanggunganpenjamin::className(), ['kelaspelayanan_id' => 'kelaspelayanan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTariftindakanMs()
    {
        return $this->hasMany(Tariftindakan::className(), ['kelaspelayanan_id' => 'kelaspelayanan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTindakanpelayananTs()
    {
        return $this->hasMany(Tindakanpelayanan::className(), ['kelaspelayanan_id' => 'kelaspelayanan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTindakanpelayananTs0()
    {
        return $this->hasMany(Tindakanpelayanan::className(), ['kelastanggungan_id' => 'kelaspelayanan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipepaketMs()
    {
        return $this->hasMany(Tipepaket::className(), ['kelaspelayanan_id' => 'kelaspelayanan_id']);
    }
}
