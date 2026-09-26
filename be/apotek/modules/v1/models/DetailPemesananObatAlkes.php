<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "detailpemesananobatalkes_v".
 *
 * @property int $pesanobatalkes_id
 * @property string $tglpemesanan
 * @property int $ruangan_id
 * @property string $ruangan_tujuan
 * @property int $instalasi_id
 * @property string $instalasi_tujuan
 * @property string $nopemesanan
 * @property int $ruanganpemesan_id
 * @property int $ruangan_pemesan_id
 * @property string $ruangan_pemesan
 * @property int $instalasi_pemesan_id
 * @property string $instalasi_pemesan
 * @property int $obatalkes_id
 * @property string $obatalkes_namalain
 * @property double $jumlah_pesan
 * @property string $tglmintadikirim
 */
class DetailPemesananObatAlkes extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'detailpemesananobatalkes_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pesanobatalkes_id', 'ruangan_id', 'instalasi_id', 'ruanganpemesan_id', 'ruangan_pemesan_id', 'instalasi_pemesan_id', 'obatalkes_id'], 'default', 'value' => null],
            [['pesanobatalkes_id', 'ruangan_id', 'instalasi_id', 'ruanganpemesan_id', 'ruangan_pemesan_id', 'instalasi_pemesan_id', 'obatalkes_id'], 'integer'],
            [['tglpemesanan', 'tglmintadikirim'], 'safe'],
            [['nopemesanan', 'obatalkes_namalain'], 'string'],
            [['jumlah_pesan'], 'number'],
            [['ruangan_tujuan', 'instalasi_tujuan', 'ruangan_pemesan', 'instalasi_pemesan'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pesanobatalkes_id' => 'Pesanobatalkes ID',
            'tglpemesanan' => 'Tglpemesanan',
            'ruangan_id' => 'Ruangan ID',
            'ruangan_tujuan' => 'Ruangan Tujuan',
            'instalasi_id' => 'Instalasi ID',
            'instalasi_tujuan' => 'Instalasi Tujuan',
            'nopemesanan' => 'Nopemesanan',
            'ruanganpemesan_id' => 'Ruanganpemesan ID',
            'ruangan_pemesan_id' => 'Ruangan Pemesan ID',
            'ruangan_pemesan' => 'Ruangan Pemesan',
            'instalasi_pemesan_id' => 'Instalasi Pemesan ID',
            'instalasi_pemesan' => 'Instalasi Pemesan',
            'obatalkes_id' => 'Obatalkes ID',
            'obatalkes_namalain' => 'Obatalkes Namalain',
            'jumlah_pesan' => 'Jumlah Pesan',
            'tglmintadikirim' => 'Tglmintadikirim',
        ];
    }

    public function getObatalkes()
    {
        return $this->hasOne(ObatAlkes::className(), ['obatalkes_id' => 'obatalkes_id']);
    }

    public function getList($params, $nopemesanan)
    {
        $query = self::find()->where(['nopemesanan' => $nopemesanan]);

        // if(isset($params['obatalkes_namalain'])) {
        //     $query->andFilterWhere(['ILIKE', 'obatalkes_namalain', $params['obatalkes_namalain']]);
        // }

        return $query;
    }

    public static function primaryKey()
    {
        return ["pesanobatdetail_id"];
    }
}
