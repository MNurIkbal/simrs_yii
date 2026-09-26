<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "satuanunit_m".
 *
 * @property int $satuanunit_id
 * @property string $satuanunit_nama
 * @property string $satuanunit_singkatan
 * @property string $satuan_jenis 0=kecil , 1=sedang, 2=besar
 *
 * @property FakturpenerimaanobatdetailT[] $fakturpenerimaanobatdetailTs
 * @property FakturpenerimaanobatdetailT[] $fakturpenerimaanobatdetailTs0
 * @property ObatalkesM[] $obatalkesMs
 * @property ObatalkesM[] $obatalkesMs0
 * @property ObatsupplierM[] $obatsupplierMs
 * @property ObatsupplierM[] $obatsupplierMs0
 * @property PaketbmhpM[] $paketbmhpMs
 * @property PembelianobatdetailT[] $pembelianobatdetailTs
 * @property PembelianobatdetailT[] $pembelianobatdetailTs0
 * @property PenerimaanbarangdetailT[] $penerimaanbarangdetailTs
 * @property PenerimaanbarangdetailT[] $penerimaanbarangdetailTs0
 * @property PenerimaanobatdetailT[] $penerimaanobatdetailTs
 * @property PenerimaanobatdetailT[] $penerimaanobatdetailTs0
 */
class SatuanUnit extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'satuanunit_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['satuanunit_nama', 'satuanunit_namalain'], 'required','message'=>'{attribute} Tidak boleh kosong'],
            [['satuanunit_id'], 'integer'],
            [['satuanunit_nama', 'satuanunit_singkatan'], 'string'],
            [['satuan_jenis'], 'string', 'max' => 10],
            [['satuanunit_nama'], 'chkNama'],
            [['satuanunit_namalain'], 'chkNamaLainnya'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'satuanunit_id' => 'Satuanunit ID',
            'satuanunit_nama' => 'Nama Lainnya',
            'satuanunit_namalain' => 'Nama Lainnya',
            'satuanunit_singkatan' => 'Satuanunit Singkatan',
            'satuan_jenis' => 'Satuan Jenis',
        ];
    }

    public function chkNama($params, $attributes)
    {
        $satuanunit_nama = $this->satuanunit_nama;
        $rest = substr($this->satuanunit_nama, 0, 1);
        if ($rest == " ") {
            $this->addError("satuanunit_nama", "Nama Satuan mengandung spasi di awal kata");
            return false;
        }
        else {
            $model = self::find()->where([
                'TRIM(LOWER (satuanunit_nama))' => strtolower($this->satuanunit_nama), 
                'is_deleted' => false
            ])->one();
            if(!empty($model) && $model->satuanunit_id != $this->satuanunit_id ){
                $this->addError("satuanunit_nama","Nama Satuan Sudah Dipakai");
                return false;
            }
        }
    
        return true;
    }

    public function chkNamaLainnya($params, $attributes)
    {
        $rest = substr($this->satuanunit_namalain, 0, 1);
        if ($rest == " ") {
            $this->addError("satuanunit_namalain", "Nama Lainnya mengandung spasi di awal kata");
            return false;
        }
    
        return true;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFakturpenerimaanobatdetailTs()
    {
        return $this->hasMany(FakturpenerimaanobatdetailT::className(), ['satuankecil_id' => 'satuanunit_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFakturpenerimaanobatdetailTs0()
    {
        return $this->hasMany(FakturpenerimaanobatdetailT::className(), ['satuanbesar_id' => 'satuanunit_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getObatalkesMs()
    {
        return $this->hasMany(ObatalkesM::className(), ['satuankecil_id' => 'satuanunit_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getObatalkesMs0()
    {
        return $this->hasMany(ObatalkesM::className(), ['satuanbesar_id' => 'satuanunit_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getObatsupplierMs()
    {
        return $this->hasMany(ObatsupplierM::className(), ['satuankecil_id' => 'satuanunit_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getObatsupplierMs0()
    {
        return $this->hasMany(ObatsupplierM::className(), ['satuanbesar_id' => 'satuanunit_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaketbmhpMs()
    {
        return $this->hasMany(PaketbmhpM::className(), ['satuankecil_id' => 'satuanunit_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPembelianobatdetailTs()
    {
        return $this->hasMany(PembelianobatdetailT::className(), ['satuanbesar_id' => 'satuanunit_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPembelianobatdetailTs0()
    {
        return $this->hasMany(PembelianobatdetailT::className(), ['satuankecil_id' => 'satuanunit_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPenerimaanbarangdetailTs()
    {
        return $this->hasMany(PenerimaanbarangdetailT::className(), ['satuankecil_id' => 'satuanunit_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPenerimaanbarangdetailTs0()
    {
        return $this->hasMany(PenerimaanbarangdetailT::className(), ['satuanbesar_id' => 'satuanunit_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPenerimaanobatdetailTs()
    {
        return $this->hasMany(PenerimaanobatdetailT::className(), ['satuankecil_id' => 'satuanunit_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPenerimaanobatdetailTs0()
    {
        return $this->hasMany(PenerimaanobatdetailT::className(), ['satuanbesar_id' => 'satuanunit_id']);
    }
}
