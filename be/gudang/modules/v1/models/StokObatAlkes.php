<?php

/**
*  @author yaya
*/
namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "stokobatalkes_t".
 *
 * @property int $stokobatalkes_id
 * @property int $ruangan_id
 * @property int $penerimaanobatdetail_id
 * @property int $terimamutasidetail_id
 * @property int $returresepdetail_id
 * @property int $returpenerimaanobatdetail_id
 * @property int $mutasiobatdetail_id
 * @property int $obatalkespasien_id
 * @property int $pemusnahanoadet_id
 * @property int $stokopnamedetail_id
 * @property int $obatalkes_id
 * @property string $tglkadaluarsa
 * @property string $nobatch
 * @property string $tglstok_in
 * @property string $tglstok_out
 * @property double $qtystok_in
 * @property double $qtystok_out
 * @property double $harganetto
 * @property double $persendiscount
 * @property double $jmldiscount
 * @property double $persenppn
 * @property double $persenpph
 * @property double $persenmargin
 * @property double $jmlmargin
 * @property bool $stokoa_aktif
 * @property int $stokobatalkesasal_id
 * @property int $satuankecil_id
 * @property string $tglterima
 * @property int $lokasiobat_id
 * @property int $rakobat_id
 * @property int $pemakaianobatdetail_id
 * @property int $produksiobat_id
 * @property int $produksiobatdet_id
 * @property int $stokinhand
 * @property int $storeeddetail_id
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
 *
 * @property PenerimaanobatdetailT[] $penerimaanobatdetailTs
 */
class StokObatAlkes extends \Doco\components\DocoActiveRecord
{
    public $ruangan_id;
    public $obatalkes_id;
    public $satuankecil_id;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'stokobatalkes_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
          [
            [
              'ruangan_id',
              'obatalkes_id',
              'satuankecil_id'
            ],
            'required'
          ],
          [
            [
              'ruangan_id',
              'penerimaanobatdetail_id',
              'terimamutasidetail_id',
              'returresepdetail_id',
              'returpenerimaanobatdetail_id',
              'mutasiobatdetail_id',
              'obatalkespasien_id',
              'pemusnahanobatdetail_id',
              'stokopnamedetail_id',
              'obatalkes_id',
              'stokobatalkesasal_id',
              'satuankecil_id',
              'lokasiobat_id',
              'rakobat_id',
              'pemakaianobatdetail_id',
              'produksiobatdetail_id',
              'storexpiredobatdetail_id',
              'created_by',
              'modified_count',
              'last_modified_by',
              'deleted_by'
            ],
            'default',
            'value'=>null
          ],
          [
            [
              'harga_netto_avg'
            ],
            'default',
            'value'=>0
          ],
          [
            [
              'ruangan_id',
              'penerimaanobatdetail_id',
              'terimamutasidetail_id',
              'returresepdetail_id',
              'returpenerimaanobatdetail_id',
              'mutasiobatdetail_id',
              'obatalkespasien_id',
              'pemusnahanobatdetail_id',
              'stokopnamedetail_id',
              'obatalkes_id',
              'stokobatalkesasal_id',
              'satuankecil_id',
              'lokasiobat_id',
              'rakobat_id',
              'pemakaianobatdetail_id',
              'produksiobatdetail_id',
              'storexpiredobatdetail_id',
              'created_by',
              'modified_count',
              'last_modified_by',
              'deleted_by'
            ],
            'integer'
          ],
          [
            [
              'penerimaansuppdetail_id',
              'adjusmenobatmasuk_id',
              'adjusmenobatkeluar_id',
              'pembatalanresep_id',
              'tglkadaluarsa',
              'tglstok_in',
              'tglstok_out',
              'tglterima',
              'created_date',
              'last_modified_date',
              'deleted_date',
              'stokoa_aktif',
              'qtystok_in',
              'qtystok_out',
              'persendiscount',
              'jmldiscount',
              'persenppn',
              'persenpph',
              'persenmargin',
              'jmlmargin',
              'nobatch',
              "jmlppn",
              'harga_netto_avg',
              'total_persediaan'
            ],
            'safe'
          ],
          [
            [
              'qtystok_in',
              'qtystok_out',
              'harganetto',
              'persendiscount',
              'jmldiscount',
              'persenppn',
              'persenpph',
              'persenmargin',
              'jmlmargin'
            ],
            'number'
          ],
          [
            [
              'stokoa_aktif',
              'is_deleted',
              'is_active'
            ],
            'boolean'
          ],
          [
            [
              'additional_data'
            ],
            'string'
          ],
          [
            [
              'qtystok_in',
              'qtystok_out',
              'persendiscount',
              'jmldiscount',
              'persenppn',
              'persenpph',
              'persenmargin',
              'jmlmargin'
            ],
            'default',
            'value'=>0
          ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'stokobatalkes_id' => 'Stokobatalkes ID',
            'ruangan_id' => 'Ruangan ID',
            'penerimaanobatdetail_id' => 'Penerimaanobatdetail ID',
            'terimamutasidetail_id' => 'Terimamutasidetail ID',
            'returresepdetail_id' => 'Returresepdetail ID',
            'returpenerimaanobatdetail_id' => 'Returpenerimaanobatdetail ID',
            'mutasiobatdetail_id' => 'Mutasiobatdetail ID',
            'obatalkespasien_id' => 'Obatalkespasien ID',
            'pemusnahanobatdetail_id' => 'Pemusnahanobatdetail ID',
            'stokopnamedetail_id' => 'Stokopnamedetail ID',
            'obatalkes_id' => 'Obatalkes ID',
            'tglkadaluarsa' => 'Tglkadaluarsa',
            'nobatch' => 'Nobatch',
            'tglstok_in' => 'Tglstok In',
            'tglstok_out' => 'Tglstok Out',
            'qtystok_in' => 'Qtystok In',
            'qtystok_out' => 'Qtystok Out',
            'harganetto' => 'Harganetto',
            'persendiscount' => 'Persendiscount',
            'jmldiscount' => 'Jmldiscount',
            'persenppn' => 'Persenppn',
            'persenpph' => 'Persenpph',
            'persenmargin' => 'Persenmargin',
            'jmlmargin' => 'Jmlmargin',
            'stokoa_aktif' => 'Stokoa Aktif',
            'stokobatalkesasal_id' => 'Stokobatalkesasal ID',
            'satuankecil_id' => 'Satuankecil ID',
            'tglterima' => 'Tglterima',
            'lokasiobat_id' => 'Lokasiobat ID',
            'rakobat_id' => 'Rakobat ID',
            'pemakaianobatdetail_id' => 'Pemakaianobatdetail ID',
            'produksiobatdetail_id' => 'Produksiobatdetail ID',
            'storexpiredobatdetail_id' => 'Storexpiredobatdetail ID',
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

    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getPenerimaanobatdetailTs()
    // {
    //     return $this->hasMany(PenerimaanobatdetailT::className(), ['stokobatalkes_id' => 'stokobatalkes_id']);
    // }

    public function populateData($data) {
        if (!count($data)) return true;

        $currentDate = date('Y-m-d H:i:s');
        $jwtRuangan = Yii::$app->jwt->ruangan_id;
        $ruangan_id = isset($_POST['ruangan_id']) ? $_POST['ruangan_id'] : $jwtRuangan;

        $idObatAlkes = array_column($data, 'obatalkes_id');
        $kadaluarsa = array_column($data, 'tglkadaluarsa');
        $listObatalkesId = "(" . implode(",", $idObatAlkes) . ")";
        $listKadaluarsa = "('" . implode("','", $kadaluarsa) . "')";

        $tmpInsert = [];

        $str = "
            SELECT
            st.stokobatalkes_id AS id_stok,
            st.obatalkes_id,
            st.tglkadaluarsa,
            st.nobatch,
            st.harganetto,
            st.persendiscount,
            st.jmldiscount,
            st.persenppn,
            st.persenpph,
            st.persenmargin,
            st.jmlmargin,
            st.jmlppn,
            CASE
              WHEN (tmp.stokobatalkesasal_id is null) THEN st.qtystok_in
              ELSE (st.qtystok_in - tmp.qtystok_out)
            END AS total_stok
            FROM stokobatalkes_t st
            LEFT JOIN (
              SELECT
              stokobatalkes_t.stokobatalkesasal_id,
              SUM(COALESCE(stokobatalkes_t.qtystok_out,0)) AS qtystok_out,
              stokobatalkes_t.tglkadaluarsa
              FROM stokobatalkes_t
              GROUP BY stokobatalkes_t.stokobatalkesasal_id, stokobatalkes_t.tglkadaluarsa
              ) tmp ON st.stokobatalkes_id = tmp.stokobatalkesasal_id
            WHERE st.ruangan_id = {$ruangan_id} AND st.obatalkes_id IN {$listObatalkesId} AND st.tglkadaluarsa IN {$listKadaluarsa} AND st.stokoa_aktif = TRUE
            ORDER BY st.created_date ASC
        ";

        $query = Yii::$app->db->createCommand($str)->queryAll();

        $listIdDetail = [];
        foreach ($data as $val) {
            $listIdDetail[$val['obatalkes_id']][$val['tglkadaluarsa']][] = $val;
        }

        foreach ($query as $detail) {
            $idObat = $detail['obatalkes_id'];
            $expireObat = $detail['tglkadaluarsa'];

            if (isset($listIdDetail[$idObat][$expireObat])) {
                $totalStok = $detail['total_stok'];

                $stokItem = $listIdDetail[$idObat][$expireObat];
                foreach ($stokItem as $key => $obatAlkes) {
                    $currentStok = $listIdDetail[$idObat][$expireObat][$key]['jumlah'];

                    if ($currentStok == 0 || $totalStok == 0) continue;

                    $row = [
                        'ruangan_id'             => $ruangan_id,
                        'obatalkes_id'           => $idObat,
                        'tglkadaluarsa'          => $detail['tglkadaluarsa'],
                        'nobatch'                => $detail['nobatch'],
                        'harganetto'             => isset($obatAlkes['harganetto']) ? $obatAlkes['harganetto'] : $detail['harganetto'],
                        'persendiscount'         => isset($obatAlkes['persendiscount']) ? $obatAlkes['persendiscount'] : $detail['persendiscount'],
                        'jmldiscount'            => isset($obatAlkes['jmldiscount']) ? $obatAlkes['jmldiscount'] : $detail['jmldiscount'],
                        'persenppn'              => isset($obatAlkes['persenppn']) ? $obatAlkes['persenppn'] : $detail['persenppn'],
                        'persenpph'              => isset($obatAlkes['persenpph']) ? $obatAlkes['persenpph'] : $detail['persenpph'],
                        'persenmargin'           => isset($obatAlkes['persenmargin']) ? $obatAlkes['persenmargin'] : $detail['persenmargin'],
                        'jmlmargin'              => isset($obatAlkes['jmlmargin']) ? $obatAlkes['jmlmargin'] : $detail['jmlmargin'],
                        'jmlppn'                 => isset($obatAlkes['jmlppn']) ? $obatAlkes['jmlppn'] : $detail['jmlppn'],
                        'obatalkespasien_id'     => isset($obatAlkes['obatalkespasien_id']) ? $obatAlkes['obatalkespasien_id'] : null,
                        'satuankecil_id'         => isset($obatAlkes['satuan_id']) ? $obatAlkes['satuan_id'] : null,
                        'pemakaianobatdetail_id' => isset($obatAlkes['pemakaianobatdetail_id']) ? $obatAlkes['pemakaianobatdetail_id'] : null,
                        'pemusnahanobatdetail_id'=> isset($obatAlkes['pemusnahanobatdetail_id']) ? $obatAlkes['pemusnahanobatdetail_id'] : null,
                        'mutasiobatdetail_id'    => isset($obatAlkes['mutasiobatdetail_id']) ? $obatAlkes['mutasiobatdetail_id'] : null,
                        'adjusmenobatkeluar_id'  => isset($obatAlkes['adjusmenobatkeluar_id']) ? $obatAlkes['adjusmenobatkeluar_id'] : null,
                        'additional_data'        => isset($obatAlkes['additional_data']) ? $obatAlkes['additional_data'] : null,
                        'adjusmenobatkeluar_id'  => isset($obatAlkes['adjusmenobatkeluar_id']) ? $obatAlkes['adjusmenobatkeluar_id'] : null,
                        'tglstok_out'            => $currentDate,
                        'stokobatalkesasal_id'   => $detail['id_stok'],
                        'qtystok_in'             => 0,
                        'stokoa_aktif'           => false,
                        'is_active'              => true,
                    ];

                    if ($currentStok >= $totalStok) {
                        $row['qtystok_out'] = $totalStok;
                        $listOfOutStock[] = $detail['id_stok'];
                        $listIdDetail[$idObat][$expireObat][$key]['jumlah'] = $currentStok - $totalStok;
                        $totalStok = 0;
                    } else {
                        $row['qtystok_out'] = $obatAlkes['jumlah'];
                        $listIdDetail[$idObat][$expireObat][$key]['jumlah'] = 0;
                        $totalStok -= $currentStok;
                    }

                    $tmpInsert[] = $row;
                }
            }
        }

        return $tmpInsert;
    }
}

