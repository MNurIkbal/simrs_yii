<?php

namespace Doco\models;

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
            [['created_date', 'last_modified_date', 'deleted_date','persentasirujin'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['kelaspelayanan_nama', 'kelaspelayanan_namalainnya'], 'string', 'max' => 50]
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
        ];
    }
}
