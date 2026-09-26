<?php
namespace SirsCore\businessLogic;

/**
** @author yaya
**/

use Yii;
use SirsCore\models\PemakaianObat;
use SirsCore\models\PemakaianObatDetail;
use SirsCore\models\KartuStokObatFn;
use SirsCore\models\StokObatAlkes as ModelStok;
use yii\helpers\ArrayHelper;

class StokObatAlkes
{
    const FEFO = 'FEFO';
    const FIFO = 'FIFO';
    public static $distribusi = true;


    public static function methodeFEFO($data = [],$tanggalPemakaian, $mutasi = false, $is_penatajasa = false, $is_stockminus = false)
    {
        if (!count($data)) return true;

        $request = Yii::$app->request;
        $jwtRuangan = Yii::$app->jwt->ruangan_id;
        $ruanganId = Yii::$app->request->post('ruangan_id', null);
        $ruangan_id = isset($_POST['ruangan_id']) ? $_POST['ruangan_id'] : (isset($ruanganId) ? $ruanganId : $jwtRuangan);
        $listIdDetail = $idObatAlkes = $trackPenggunaanObat = [];

        foreach ($data as $val) {
            /**
             * Update Kondisi untuk obat yang sama
             * 20-Maret-2019
             */
            $idObat = $val['obatalkes_id'];
            $listIdDetail[$idObat][] = $val;
            $idObatAlkes[] = $idObat;

            /**
             * Track jumlah obat yang akan dipakai
             * 26-Februari-2020
             * Anggoro (tri.anggoro@docotel.com)
            */
            $trackPenggunaanObat[$idObat] = isset($trackPenggunaanObat[$idObat]) ?
                $trackPenggunaanObat[$idObat] : 0;
            $trackPenggunaanObat[$idObat] += isset($val['qty_satuanpakai']) ?
                $val['qty_satuanpakai'] : 0;
        }

        $inCondition = "(" . implode(",", $idObatAlkes) . ")";

        // validate stock above 0
        $vsaz = "
            AND (CASE
              WHEN (tmp.stokobatalkesasal_id is null) THEN st.qtystok_in
              ELSE (st.qtystok_in - tmp.qtystok_out)
            END) > 0";

        $orderItem = "st.tglkadaluarsa ASC";

        if($is_stockminus) {
            $vsaz = "";
            $orderItem = "st.tglkadaluarsa ASC, total_stok DESC";
        } 

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
              WHERE stokobatalkes_t.qtystok_out > 0
              AND stokobatalkes_t.obatalkes_id IN {$inCondition}
              GROUP BY stokobatalkes_t.stokobatalkesasal_id, stokobatalkes_t.tglkadaluarsa
              ) tmp ON st.stokobatalkes_id = tmp.stokobatalkesasal_id
            WHERE st.obatalkes_id IN {$inCondition} AND st.ruangan_id = {$ruangan_id} AND st.is_deleted = false
            " . $vsaz . "
            ORDER BY " . $orderItem . "
        ";


        $query = Yii::$app->db->createCommand($str)->queryAll();

        /**
        * Find harga netto average from master obat alkes
        * 1 Juli 2020
        */
        $queryMasterObatAlkes = "SELECT obatalkes_id,hargaratarata FROM obatalkes_m WHERE obatalkes_id IN {$inCondition}";
        $dataMasterObatAlkes = Yii::$app->db->createCommand($queryMasterObatAlkes)->queryAll();
        if(is_array($dataMasterObatAlkes) && count($dataMasterObatAlkes)>0) $dataMasterObatAlkes = array_column($dataMasterObatAlkes, 'hargaratarata','obatalkes_id');

        $tmpInsert = $listOfOutStock = $listObatTidakCukup = [];

        /**
         * Track jumlah semua obat yang ada di ruangan
         * 26-Februari-2020
         * Anggoro (tri.anggoro@docotel.com)
        */
        $jumlah_obat_tersedia = [];
        foreach ($query as $stok_obat_tersedia) {
            $obatalkes_id__stok = $stok_obat_tersedia['obatalkes_id'];
            $jumlah_obat_tersedia[$obatalkes_id__stok] = isset($jumlah_obat_tersedia[$obatalkes_id__stok]) ?
                 $jumlah_obat_tersedia[$obatalkes_id__stok] + $stok_obat_tersedia['total_stok'] :
                 $stok_obat_tersedia['total_stok'];
        }

        foreach ($query as $detail) {
            $idObat = $detail['obatalkes_id'];

            if (isset($listIdDetail[$idObat])) {
                $totalStok = $detail['total_stok'];

                $stokItem = $listIdDetail[$idObat];
                foreach ($stokItem as $key => $obatAlkes) {
                    $currentStok = $listIdDetail[$idObat][$key]['qty_satuanpakai'];

                    if ($currentStok == 0) continue; // transaction 0 qty

                    if(!$is_stockminus) {
                        if ($totalStok == 0) continue; // stockoa 0 qty
                    }

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
                        'satuankecil_id'         => isset($obatAlkes['satuankecil_id']) ? $obatAlkes['satuankecil_id'] : null,
                        'pemakaianobatdetail_id' => isset($obatAlkes['pemakaianobatdetail_id']) ? $obatAlkes['pemakaianobatdetail_id'] : null,
                        'mutasiobatdetail_id'    => isset($obatAlkes['mutasiobatdetail_id']) ? $obatAlkes['mutasiobatdetail_id'] : null,
                        'pemusnahanobatdetail_id'=> isset($obatAlkes['pemusnahanobatdetail_id']) ? $obatAlkes['pemusnahanobatdetail_id'] : null,
                        'adjusmenobatkeluar_id'  => isset($obatAlkes['adjusmenobatkeluar_id']) ? $obatAlkes['adjusmenobatkeluar_id'] : null,
                        'additional_data'        => isset($obatAlkes['additional_data']) ? $obatAlkes['additional_data'] : null,
                        'adjusmenobatkeluar_id'  => isset($obatAlkes['adjusmenobatkeluar_id']) ? $obatAlkes['adjusmenobatkeluar_id'] : null,
                        'tglstok_out'            => date('Y-m-d H:i:s',strtotime($tanggalPemakaian)),
                        'stokobatalkesasal_id'   => $detail['id_stok'],
                        'qtystok_in'             => 0,
                        'stokoa_aktif'           => false,
                        'is_active'              => true,
                        'produksiobatalkesbahanbaku_id' => isset($obatAlkes['produksiobatalkesbahanbaku_id']) ? $obatAlkes['produksiobatalkesbahanbaku_id'] : null,
                    ];

                    /*
                    * Habiskan Stok sebelumnya terlebih dahulu,
                    * sebelum berpindah ke stok selanjutnya
                    */

                    if ($currentStok >= $totalStok && !$is_stockminus) {
                        $row['qtystok_out'] = $totalStok;
                        $listOfOutStock[] = $detail['id_stok'];
                        $listIdDetail[$idObat][$key]['qty_satuanpakai'] = $currentStok - $totalStok;
                        $totalStok = 0;
                    } else {
                        $row['qtystok_out'] = $obatAlkes['qty_satuanpakai'];
                        $listIdDetail[$idObat][$key]['qty_satuanpakai'] = 0;
                        $totalStok -= $currentStok;
                    }

                    $tmpInsert[] = $row;
                    if ($mutasi) {
                        $idTerima = isset($obatAlkes['terimamutasidetail_id']) ? $obatAlkes['terimamutasidetail_id'] : null;
                        $tmpInsert[] = self::generateMutasi($row, $idTerima);
                    }
                }
            }
        }

        if ($tmpInsert) {
            foreach ($tmpInsert as $k => $v) {
                $tmpInsert[$k]['harga_netto_avg'] = isset($dataMasterObatAlkes[$v['obatalkes_id']]) ? $dataMasterObatAlkes[$v['obatalkes_id']] : 0;
            }
            ModelStok::batchInsert($tmpInsert,false);
        }

        $listObatTidakCukup = self::cekQtySisa($listIdDetail);

        /**
         * Kirim data obat yang tidak cukup ke FE
         * 26-Februari-2020
         * Anggoro (tri.anggoro@docotel.com)
        */
        if (count($listObatTidakCukup) > 0) {
            return $listObatTidakCukup;
        }

        if ($is_penatajasa) {
            $lostStok = self::lostStok($listIdDetail, $ruangan_id, $tanggalPemakaian);
        }

        if (!empty($lostStok)) {
            $tmpInsert = array_merge($tmpInsert, $lostStok);
        }

        /** Update Stok jadiin false **/
        if ($listOfOutStock) {
            self::updateOutOfStock($listOfOutStock);
        }
        return true;
    }

    public static function methodeFIFO($data = [],$tanggalPemakaian, $mutasi = false, $is_penatajasa =false, $is_stockminus = false)
    {
        /** Skip ketika tidak di temukan datanya **/
        if (!count($data)) return true;
        $request = Yii::$app->request;
        $jwtRuangan = Yii::$app->jwt->ruangan_id;
        $ruangan_id = isset($_POST['ruangan_id']) ? $_POST['ruangan_id'] : $jwtRuangan;
        $listIdDetail = $idObatAlkes = [];
        foreach ($data as $val) {
            /**
             * Update Kondisi untuk obat yang sama
             * 20-Maret-2019
             */
            $idObat = $val['obatalkes_id'];
            $listIdDetail[$idObat][] = $val;
            $idObatAlkes[] = $idObat;
        }
        $inCondition = "(" . implode(",", $idObatAlkes) . ")";
        $str ="
            SELECT
                (CASE WHEN t.stokobatalkesasal_id IS NULL THEN t.stokobatalkes_id ELSE t.stokobatalkesasal_id END) as id_stok,
                t.obatalkes_id,
                t.qtystok_in,
                (CASE WHEN t.tglstok_in IS NULL THEN child.tglstok_in ELSE t.tglstok_in END) as tglstok_in,
                t.qtystok_out,
                t.nobatch,
                (CASE
                        WHEN t.stokobatalkesasal_id IS NULL
                            THEN t.harganetto
                        ELSE child.harganetto END) as harganetto,
                (CASE
                        WHEN t.stokobatalkesasal_id IS NULL
                            THEN t.persendiscount
                        ELSE child.persendiscount END) as persendiscount,
                (CASE
                        WHEN t.stokobatalkesasal_id IS NULL
                            THEN t.jmldiscount
                        ELSE child.jmldiscount END) as jmldiscount,
                (CASE
                        WHEN t.stokobatalkesasal_id IS NULL
                            THEN t.persenppn
                        ELSE child.persenppn END) as persenppn,
                (CASE
                        WHEN t.stokobatalkesasal_id IS NULL
                            THEN t.persenpph
                        ELSE child.persenpph END) as persenpph,
                (CASE
                        WHEN t.stokobatalkesasal_id IS NULL
                            THEN t.persenmargin
                        ELSE child.persenmargin END) as persenmargin,
                (CASE
                        WHEN t.stokobatalkesasal_id IS NULL
                            THEN t.jmlmargin
                        ELSE child.jmlmargin END) as jmlmargin,
                (CASE
                        WHEN t.stokobatalkesasal_id IS NULL
                            THEN t.jmlppn
                        ELSE child.jmlppn END) as jmlppn,
                t.tglkadaluarsa
            FROM stokobatalkes_t t
            LEFT JOIN stokobatalkes_t child ON t.stokobatalkesasal_id = child.stokobatalkes_id
            WHERE t.obatalkes_id IN {$inCondition} AND t.ruangan_id = {$ruangan_id}
            AND t.stokoa_aktif = true";

        if(self::$distribusi == false){
            $str ="
            SELECT
                (CASE WHEN t.stokobatalkesasal_id IS NULL THEN t.stokobatalkes_id ELSE t.stokobatalkesasal_id END) as id_stok,
                t.obatalkes_id,
                t.qtystok_in,
                (CASE WHEN t.tglstok_in IS NULL THEN child.tglstok_in ELSE t.tglstok_in END) as tglstok_in,
                t.qtystok_out,
                t.nobatch,
                null::TEXT as harganetto,
                null::TEXT as persendiscount,
                null::TEXT as jmldiscount,
                null::TEXT as persenppn,
                null::TEXT as persenpph,
                null::TEXT as persenmargin,
                null::TEXT as jmlmargin,
                null::TEXT as jmlppn,
                t.tglkadaluarsa
            FROM stokobatalkes_t t
            LEFT JOIN stokobatalkes_t child ON t.stokobatalkesasal_id = child.stokobatalkes_id
            WHERE t.obatalkes_id IN {$inCondition} AND t.ruangan_id = {$ruangan_id}
            AND t.stokoa_aktif = true";
        }
        $query = Yii::$app->db->createCommand("
            SELECT
                obatalkes_id,
                id_stok,
                SUM(dadang.qtystok_in - dadang.qtystok_out) as total_stok,
                dadang.tglkadaluarsa,
                nobatch,
                harganetto,
                persendiscount,
                jmldiscount,
                persenppn,
                persenpph,
                persenmargin,
                jmlmargin,
                tglstok_in,
                jmlppn
            FROM (
                {$str}
            ) as dadang
            GROUP BY
                dadang.id_stok,
                dadang.tglkadaluarsa,
                obatalkes_id,
                nobatch,
                harganetto,
                persendiscount,
                jmldiscount,
                persenppn,
                persenpph,
                persenmargin,
                jmlmargin,
                tglstok_in,
                jmlppn
            ORDER BY dadang.tglstok_in ASC
        ")->queryAll();

        /**
        * Find harga netto average from master obat alkes
        * 1 Juli 2020
        */
        $queryMasterObatAlkes = "SELECT obatalkes_id,hargaratarata FROM obatalkes_m WHERE obatalkes_id IN {$inCondition}";
        $dataMasterObatAlkes = Yii::$app->db->createCommand($queryMasterObatAlkes)->queryAll();
        if(is_array($dataMasterObatAlkes) && count($dataMasterObatAlkes)>0) $dataMasterObatAlkes = array_column($dataMasterObatAlkes, 'hargaratarata','obatalkes_id');

        $tmpInsert = $listOfOutStock = [];
        /** Kuncinya qty_satuanpakai selalu berkurang **/
        foreach ($query as $detail) {
            $idObat = $detail['obatalkes_id'];
            if (isset($listIdDetail[$idObat])) {
                $totalStok = $detail['total_stok'];

                /**
                 * ini stok permintaan yang di inputkan
                 * contoh permintaan 250 sedangkan stok detail nya 100
                 * list array obat alkes
                 * Perubahan major
                 */
                $stokItem = $listIdDetail[$idObat];
                foreach ($stokItem as $key => $obatAlkes) {
                    $currentStok = $listIdDetail[$idObat][$key]['qty_satuanpakai'];
                    /** Lewati ketika stok sudah 0 **/
                    if ($currentStok == 0 || $totalStok == 0) continue;
                    $idTerima = isset($obatAlkes['terimamutasidetail_id']) ? $obatAlkes['terimamutasidetail_id'] : null;
                    if ($currentStok > $totalStok) {
                        $listOfOutStock[] = $detail['id_stok'];
                        /** ini akan membuat row baru dengan catatan stok harus di update false **/
                        $row = [
                            'ruangan_id' => $ruangan_id,
                            'obatalkes_id' => $idObat,
                            'tglkadaluarsa' => $detail['tglkadaluarsa'],
                            'nobatch' => $detail['nobatch'],
                            'harganetto' => isset($obatAlkes['harganetto']) ? $obatAlkes['harganetto'] : $detail['harganetto'],
                            'persendiscount' => isset($obatAlkes['persendiscount']) ? $obatAlkes['persendiscount'] : $detail['persendiscount'],
                            'jmldiscount' => isset($obatAlkes['jmldiscount']) ? $obatAlkes['jmldiscount'] : $detail['jmldiscount'],
                            'persenppn' => isset($obatAlkes['persenppn']) ? $obatAlkes['persenppn'] : $detail['persenppn'],
                            'persenpph' => isset($obatAlkes['persenpph']) ? $obatAlkes['persenpph'] : $detail['persenpph'],
                            'persenmargin' => isset($obatAlkes['persenmargin']) ? $obatAlkes['persenmargin'] : $detail['persenmargin'],
                            'jmlmargin' => isset($obatAlkes['jmlmargin']) ? $obatAlkes['jmlmargin'] : $detail['jmlmargin'],
                            'jmlppn' => isset($obatAlkes['jmlppn']) ? $obatAlkes['jmlppn'] : $detail['jmlppn'],
                            'obatalkespasien_id' => isset($obatAlkes['obatalkespasien_id']) ? $obatAlkes['obatalkespasien_id'] : null,
                            'pemusnahanobatdetail_id'=> isset($obatAlkes['pemusnahanobatdetail_id']) ? $obatAlkes['pemusnahanobatdetail_id'] : null,
                            'adjusmenobatkeluar_id'  => isset($obatAlkes['adjusmenobatkeluar_id']) ? $obatAlkes['adjusmenobatkeluar_id'] : null,
                            'qtystok_in' => 0,
                            'tglstok_out' => date('Y-m-d H:i:s',strtotime($tanggalPemakaian)),
                            'qtystok_out' => $totalStok,
                            'stokobatalkesasal_id' => $detail['id_stok'],
                            'satuankecil_id' => isset($obatAlkes['satuankecil_id'])
                                                    ? $obatAlkes['satuankecil_id'] : null,
                            'pemakaianobatdetail_id' => isset($obatAlkes['pemakaianobatdetail_id'])
                                                    ? $obatAlkes['pemakaianobatdetail_id'] : null,
                            'mutasiobatdetail_id' => isset($obatAlkes['mutasiobatdetail_id'])
                                                    ? $obatAlkes['mutasiobatdetail_id'] : null,
                            'stokoa_aktif' => true,
                            'is_active' => true,
                            'additional_data' => isset($obatAlkes['additional_data']) ? $obatAlkes['additional_data'] : null,
                            'produksiobatalkesbahanbaku_id' => isset($obatAlkes['produksiobatalkesbahanbaku_id']) ? $obatAlkes['produksiobatalkesbahanbaku_id'] : null,
                        ];
                        $tmpInsert[] = $row;
                        if ($mutasi) {
                            $tmpInsert[] = self::generateMutasi($row, $idTerima);
                        }
                        $listIdDetail[$idObat][$key]['qty_satuanpakai'] = $currentStok - $totalStok;
                        $totalStok = 0;
                    } else {
                        if ($listIdDetail[$idObat][$key]['qty_satuanpakai'] == $totalStok) {
                            $listOfOutStock[] = $detail['id_stok'];
                        }
                        $row = [
                            'ruangan_id' => $ruangan_id,
                            'obatalkes_id' => $idObat,
                            'tglkadaluarsa' => $detail['tglkadaluarsa'],
                            'nobatch' => $detail['nobatch'],
                            'harganetto' => isset($obatAlkes['harganetto']) ? $obatAlkes['harganetto'] : $detail['harganetto'],
                            'persendiscount' => isset($obatAlkes['persendiscount']) ? $obatAlkes['persendiscount'] : $detail['persendiscount'],
                            'jmldiscount' => isset($obatAlkes['jmldiscount']) ? $obatAlkes['jmldiscount'] : $detail['jmldiscount'],
                            'persenppn' => isset($obatAlkes['persenppn']) ? $obatAlkes['persenppn'] : $detail['persenppn'],
                            'persenpph' => isset($obatAlkes['persenpph']) ? $obatAlkes['persenpph'] : $detail['persenpph'],
                            'persenmargin' => isset($obatAlkes['persenmargin']) ? $obatAlkes['persenmargin'] : $detail['persenmargin'],
                            'jmlmargin' => isset($obatAlkes['jmlmargin']) ? $obatAlkes['jmlmargin'] : $detail['jmlmargin'],
                            'jmlppn' => isset($obatAlkes['jmlppn']) ? $obatAlkes['jmlppn'] : $detail['jmlppn'],
                            'obatalkespasien_id' => isset($obatAlkes['obatalkespasien_id']) ? $obatAlkes['obatalkespasien_id'] : null,
                            'adjusmenobatkeluar_id'  => isset($obatAlkes['adjusmenobatkeluar_id']) ? $obatAlkes['adjusmenobatkeluar_id'] : null,
                            'qtystok_in' => 0,
                            'tglstok_out' => date('Y-m-d H:i:s',strtotime($tanggalPemakaian)),
                            'qtystok_out' => $obatAlkes['qty_satuanpakai'],
                            'stokobatalkesasal_id' => $detail['id_stok'],
                            'satuankecil_id' => isset($obatAlkes['satuankecil_id'])
                                                    ? $obatAlkes['satuankecil_id'] : null,
                            'pemakaianobatdetail_id' => isset($obatAlkes['pemakaianobatdetail_id'])
                                                    ? $obatAlkes['pemakaianobatdetail_id'] : null,
                            'mutasiobatdetail_id' => isset($obatAlkes['mutasiobatdetail_id'])
                                                    ? $obatAlkes['mutasiobatdetail_id'] : null,
                            'stokoa_aktif' => true,
                            'is_active' => true,
                            'additional_data' => isset($obatAlkes['additional_data']) ? $obatAlkes['additional_data'] : null,
                            'produksiobatalkesbahanbaku_id' => isset($obatAlkes['produksiobatalkesbahanbaku_id']) ? $obatAlkes['produksiobatalkesbahanbaku_id'] : null,
                        ];
                        $tmpInsert[] = $row;
                        if ($mutasi) {
                            $tmpInsert[] = self::generateMutasi($row, $idTerima);
                        }
                        $listIdDetail[$idObat][$key]['qty_satuanpakai'] = 0;
                        $totalStok -= $currentStok;
                    }
                }
            }
        }
        if ($is_penatajasa) {
            $lostStok = self::lostStok($listIdDetail, $ruangan_id, $tanggalPemakaian);
        }
        if (!empty($lostStok)) {
            $tmpInsert = array_merge($tmpInsert, $lostStok);
        }

        if ($tmpInsert) {
            foreach ($tmpInsert as $k => $v) {
                $tmpInsert[$k]['harga_netto_avg'] = isset($dataMasterObatAlkes[$v['obatalkes_id']]) ? $dataMasterObatAlkes[$v['obatalkes_id']] : 0;
            }
            ModelStok::batchInsert($tmpInsert,false);
        }

        /** Update Stok jadiin false **/
        if ($listOfOutStock) {
            $inCondition = "(" . implode(",", $listOfOutStock) . ")";
            Yii::$app->db->createCommand("
                UPDATE stokobatalkes_t SET stokoa_aktif = false, is_active = false
                WHERE (stokobatalkesasal_id IN {$inCondition} OR stokobatalkes_id IN {$inCondition})
                AND stokoa_aktif = true
            ")->execute();
        }
        return true;
    }

    protected static function lostStok(array $listIdDetail, $ruangan_id, $tanggalPemakaian)
    {
        $tmpInsert = [];
        foreach ($listIdDetail as $idObat => $rows) {
            if (is_array($rows)) {
                foreach ($rows as $obatAlkes) {
                    if (!empty($obatAlkes['qty_satuanpakai'])) {
                        $row = [
                            'ruangan_id' => $ruangan_id,
                            'obatalkes_id' => $idObat,
                            'tglkadaluarsa' => null,
                            'nobatch' => null,
                            'harganetto' => isset($obatAlkes['harganetto']) ? $obatAlkes['harganetto'] : 0,
                            'persendiscount' => isset($obatAlkes['persendiscount']) ? $obatAlkes['persendiscount'] : 0,
                            'jmldiscount' => isset($obatAlkes['jmldiscount']) ? $obatAlkes['jmldiscount'] : 0,
                            'persenppn' => isset($obatAlkes['persenppn']) ? $obatAlkes['persenppn'] : 0,
                            'persenpph' => isset($obatAlkes['persenpph']) ? $obatAlkes['persenpph'] : 0,
                            'persenmargin' => isset($obatAlkes['persenmargin']) ? $obatAlkes['persenmargin'] : 0,
                            'jmlmargin' => isset($obatAlkes['jmlmargin']) ? $obatAlkes['jmlmargin'] : 0,
                            'jmlppn' => isset($obatAlkes['jmlppn']) ? $obatAlkes['jmlppn'] : 0,
                            'obatalkespasien_id' => isset($obatAlkes['obatalkespasien_id']) ? $obatAlkes['obatalkespasien_id'] : null,
                            'qtystok_in' => 0,
                            'tglstok_out' => date('Y-m-d H:i:s',strtotime($tanggalPemakaian)),
                            'qtystok_out' => $obatAlkes['qty_satuanpakai'],
                            'stokobatalkesasal_id' => null,
                            'satuankecil_id' => isset($obatAlkes['satuankecil_id'])
                                                    ? $obatAlkes['satuankecil_id'] : null,
                            'pemakaianobatdetail_id' => isset($obatAlkes['pemakaianobatdetail_id'])
                                                    ? $obatAlkes['pemakaianobatdetail_id'] : null,
                            'mutasiobatdetail_id' => isset($obatAlkes['mutasiobatdetail_id'])
                                                    ? $obatAlkes['mutasiobatdetail_id'] : null,
                            'stokoa_aktif' => false,
                            'is_active' => false,
                            'additional_data' => isset($obatAlkes['additional_data']) ? $obatAlkes['additional_data'] : null
                        ];

                        $tmpInsert[] = $row;
                    }
                }
            }
        }
        return $tmpInsert;
    }

    protected static function cekQtySisa(array $list_stok_obat)
    {
        $list_out_of_stock = [];
        foreach ($list_stok_obat as $obatalkes_id => $stok_obat) {

            if (is_array($stok_obat)) {
                foreach ($stok_obat as $obat_alkes) {
                    if (!empty($obat_alkes['qty_satuanpakai'])) {
                       $list_out_of_stock[] = $obatalkes_id;
                    }
                }
            }
        }

        return $list_out_of_stock;
    }

    protected static function updateOutOfStock(array $list_out_of_stock)
    {
        $inCondition = "(" . implode(",", $list_out_of_stock) . ")";
        Yii::$app->db->createCommand("
            UPDATE stokobatalkes_t SET stokoa_aktif = false, is_active = false
            WHERE (stokobatalkesasal_id IN {$inCondition} OR stokobatalkes_id IN {$inCondition})
            AND stokoa_aktif = true
        ")->execute();
    }

    /**
    * ket kegunaan untuk parsing row stokobatalkes_t
    * @var $row Array
    * @var $idTerima Integer
    * @return array
    **/
    public static function generateMutasi($row, $idTerima)
    {
        $row['qtystok_in'] = $row['qtystok_out'];
        $row['ruangan_id'] = $_POST['ruangan_penerima_id'];
        $row['tglstok_in'] = $row['tglstok_out'];
        $row['qtystok_out'] = 0;
        $row['terimamutasidetail_id'] = $idTerima;
        $row['stokoa_aktif'] = true;
        unset($row['tglstok_out']);
        unset($row['stokobatalkesasal_id']);
        unset($row['mutasiobatdetail_id']);
        return $row;
    }

    /**
     *
     * @author : metafiliana
     * fungsi update stokobatalkes_t saat SO
     *
     */
    public static function updateStokObatAlkes($stokopname_id, $ruangan_id, $is_stokawal)
    {
        if (!count($stokopname_id)) return true;
        $request = Yii::$app->request;
        $data_sodetail = Yii::$app->db->createCommand("
            SELECT
                stokopnamedetail_id,
                obatalkes_id,
                volume_fisik,
                volume_sistem,
                harganetto,
                tglkadaluarsa,
                stokobatalkes_id
            FROM
                stokopnamedetail_t
            WHERE
                stokopname_id = {$stokopname_id} AND
                is_deleted = FALSE
        ")->queryAll();

        $listOfOutStock = $idObatAlkes = [];
        $idObatAlkes = count($data_sodetail) > 0 ? array_column($data_sodetail, 'obatalkes_id') : [];
        $inCondition = "(" . implode(",", $idObatAlkes) . ")";
        $query = Yii::$app->db->createCommand("
            SELECT
                id_stok,
                obatalkes_id,
                satuankecil_id,
                tglkadaluarsa,
                SUM(dadang.qtystok_in - dadang.qtystok_out) as total_stok,
                nobatch
            FROM
                (
                    SELECT
                        (
                            CASE
                            WHEN stokobatalkesasal_id IS NULL THEN
                                stokobatalkes_id
                            ELSE
                                stokobatalkesasal_id
                            END
                        ) AS id_stok,
                        obatalkes_id,
                        satuankecil_id,
                        tglkadaluarsa,
                        qtystok_in,
                        qtystok_out,
                        nobatch
                    FROM
                        stokobatalkes_t
                    WHERE
                        ruangan_id = {$ruangan_id}
                ) AS dadang
            GROUP BY
                dadang.id_stok,
                tglkadaluarsa,
                obatalkes_id,
                satuankecil_id,
                nobatch
            ORDER BY
                obatalkes_id ASC
        ")->queryAll();

        /**
        * Find harga netto average from master obat alkes
        * 1 Juli 2020
        */
        $dataMasterObatAlkes = $dataSatuanKecilObat = [];
        if(count($idObatAlkes)>0){
            $queryMasterObatAlkes = "SELECT obatalkes_id,hargaratarata,satuankecil_id FROM obatalkes_m WHERE obatalkes_id IN {$inCondition}";
            $rawDataMasterObatAlkes = Yii::$app->db->createCommand($queryMasterObatAlkes)->queryAll();
            if(is_array($rawDataMasterObatAlkes) && count($rawDataMasterObatAlkes)>0){
                $dataMasterObatAlkes = array_column($rawDataMasterObatAlkes, 'hargaratarata','obatalkes_id');
                $dataSatuanKecilObat = array_column($rawDataMasterObatAlkes, 'satuankecil_id','obatalkes_id');
            }

        }
        // case stok awal
        if ($is_stokawal){
            $tmpInsert = [];
            $tmpObat = [];
            $listObat = [];
            // prepare insert stok awal
            foreach ($query as $v_q) {
                $listOfOutStock[] = $v_q['id_stok'];
                $tmpObat[$v_q['obatalkes_id']] = $v_q;
            }
            foreach ($data_sodetail as $key => $val) {
                $listObat[$val['obatalkes_id']][] = $val['volume_fisik'];
            }
            $exceptionList = [];
            foreach ($data_sodetail as $k => $v_dso) {
                $tmpInsert_temp = [];
                $stok_now = $v_q['total_stok'];
                $stok_fisik = $v_dso['volume_fisik'];
                $tmpInsert_temp['ruangan_id'] = $ruangan_id;
                $tmpInsert_temp['stokopnamedetail_id'] = $v_dso['stokopnamedetail_id'];
                $tmpInsert_temp['obatalkes_id'] = $v_dso['obatalkes_id'];
                $tmpInsert_temp['tglkadaluarsa'] = $v_dso['tglkadaluarsa'];
                $tmpInsert_temp['nobatch'] = $tmpObat[$v_dso['obatalkes_id']]['nobatch'];
                $tmpInsert_temp['satuankecil_id'] = $tmpObat[$v_dso['obatalkes_id']]['satuankecil_id'];
                $tmpInsert_temp['tglstok_in'] = date('Y-m-d');
                $tmpInsert_temp['qtystok_in'] = $stok_fisik;
                $tmpInsert_temp['qtystok_out'] = 0;
                $tmpInsert_temp['harganetto'] = @$v_dso['harganetto'];
                $tmpInsert_temp['persendiscount'] = 0;
                $tmpInsert_temp['jmldiscount'] = 0;
                $tmpInsert_temp['persenmargin'] = 0;
                $tmpInsert_temp['jmlmargin'] = 0;
                $tmpInsert_temp['persenppn'] = 0;
                $tmpInsert_temp['jmlppn'] = 0;
                $tmpInsert_temp['stokoa_aktif'] = TRUE;
                $tmpInsert_temp['additional_data'] = null;
                if(!in_array($v_dso['obatalkes_id'], $exceptionList)){
                    $additional = [
                        'is_stokawal' => true,
                        'total_stok'=>array_sum( $listObat[$v_dso['obatalkes_id']] )
                    ];
                    $tmpInsert_temp['additional_data'] = json_encode($additional);
                    array_push($exceptionList, $v_dso['obatalkes_id']);
                }
                $tmpInsert[] = $tmpInsert_temp;
            }
            // return $tmpInsert;
            // Update Stok jadiin false
            if ($listOfOutStock) {
                $inCondition = "(" . implode(",", $listOfOutStock) . ")";
                Yii::$app->db->createCommand("
                    UPDATE stokobatalkes_t SET stokoa_aktif = false, is_active = false
                    WHERE (stokobatalkesasal_id IN {$inCondition} OR stokobatalkes_id IN {$inCondition})
                    AND stokoa_aktif = true
                ")->execute();
            }

            foreach ($tmpInsert as $k => $v) {
                $tmpInsert[$k]['harga_netto_avg'] = isset($dataMasterObatAlkes[$v['obatalkes_id']]) ? $dataMasterObatAlkes[$v['obatalkes_id']] : 0;
            }
            // insert new stok awal
            ModelStok::batchInsert($tmpInsert, false);

        // case penyesuaian
        }else{
            $tmpInsert = [];
            $dataObat = [];
            // prepare insert stok awal
            foreach ($query as $detail) {
                if(isset($dataSatuanKecilObat[$detail['obatalkes_id']]) && empty($detail['satuankecil_id'])){
                    $detail['satuankecil_id'] = $dataSatuanKecilObat[$detail['obatalkes_id']];
                }
                $dataObat[$detail['obatalkes_id'].'-'.$detail['tglkadaluarsa']][] = $detail;
            }

            $_sumOfGroupObatKadaluarsa = $_groupObatKadaluarsa = [];
            foreach ($data_sodetail as $_data_sodetail) {
                $primary = $_data_sodetail['obatalkes_id'].'-'.$_data_sodetail['tglkadaluarsa'];
                $_sumOfGroupObatKadaluarsa[$primary] = isset($_sumOfGroupObatKadaluarsa[$primary]) ? $_sumOfGroupObatKadaluarsa[$primary]+1 : 0;
                $_groupObatKadaluarsa[$primary][] = $_data_sodetail;
            }

            $_keyOfGroup = array_filter($_sumOfGroupObatKadaluarsa,function($var){
                return $var > 0;
            });
            $_val_keyOfGroup = array_keys($_keyOfGroup);
            $_skipGroupObatExpire = [];
            foreach ($_val_keyOfGroup as $_k_obatExpire) {
                if(isset($_groupObatKadaluarsa[$_k_obatExpire]) && count($_groupObatKadaluarsa[$_k_obatExpire]>0)){
                    $vf = array_sum(array_column($_groupObatKadaluarsa[$_k_obatExpire], 'volume_fisik'));
                    $vs = array_sum(array_column($_groupObatKadaluarsa[$_k_obatExpire], 'volume_sistem'));
                    $calcSelisih = $vf - $vs;
                    if($calcSelisih==0){
                        $_skipGroupObatExpire[] =  $_k_obatExpire;
                    }
                }
            }

            foreach ($data_sodetail as $v_dso) {
                $primary = $v_dso['obatalkes_id'].'-'.$v_dso['tglkadaluarsa'];
                if(isset($_sumOfGroupObatKadaluarsa[$primary]) && $_sumOfGroupObatKadaluarsa[$primary] > 0 && in_array($primary, $_skipGroupObatExpire)){
                    continue;
                }
                $calculate = $v_dso['volume_fisik'] - $v_dso['volume_sistem'];
                // isi 9
                $qty_fisik = $calculate;
                if($calculate != 0){
                    if($qty_fisik > 0){
                        $tmpInsert_temp['ruangan_id'] = $ruangan_id;
                        $tmpInsert_temp['stokopnamedetail_id'] = $v_dso['stokopnamedetail_id'];
                        $tmpInsert_temp['obatalkes_id'] = $v_dso['obatalkes_id'];
                        $tmpInsert_temp['tglkadaluarsa'] = $v_dso['tglkadaluarsa'];
                        $tmpInsert_temp['nobatch'] = @$dataObat[$primary][0]['nobatch'];
                        $tmpInsert_temp['satuankecil_id'] = @$dataSatuanKecilObat[$v_dso['obatalkes_id']];
                        $tmpInsert_temp['qtystok_in'] = abs($calculate);
                        $tmpInsert_temp['qtystok_out'] = 0;
                        $tmpInsert_temp['tglstok_in'] = date('Y-m-d H:i:s');
                        $tmpInsert_temp['tglstok_out'] = null;
                        $tmpInsert_temp['persendiscount'] = 0;
                        $tmpInsert_temp['jmldiscount'] = 0;
                        $tmpInsert_temp['persenmargin'] = 0;
                        $tmpInsert_temp['jmlmargin'] = 0;
                        $tmpInsert_temp['persenppn'] = 0;
                        $tmpInsert_temp['jmlppn'] = 0;
                        $tmpInsert_temp['harganetto'] = @$v_dso['harganetto'];
                        $tmpInsert_temp['stokoa_aktif'] = TRUE;
                        $tmpInsert_temp['stokobatalkesasal_id'] = null;
                        $tmpInsert[] = $tmpInsert_temp;
                    }else{
                        $qty_fisik = abs($qty_fisik);
                        if(!isset($dataObat[$v_dso['obatalkes_id'].'-'.$v_dso['tglkadaluarsa']])){
                            //tidak ada stokoa aktif
                            continue;
                        }
                        foreach($dataObat[$v_dso['obatalkes_id'].'-'.$v_dso['tglkadaluarsa']] as $k => $v):
                            $stoknow = $v['total_stok'];
                            
                            if($qty_fisik > $stoknow){
                                $isi = $stoknow; // 6
                                $qty_fisik -= $stoknow; //fisik = 9 - 6
                            }else{
                                $isi = $qty_fisik;
                                $qty_fisik = 0;
                            }

                            $tmpInsert_temp['ruangan_id'] = $ruangan_id;
                            $tmpInsert_temp['stokopnamedetail_id'] = $v_dso['stokopnamedetail_id'];
                            $tmpInsert_temp['obatalkes_id'] = $v_dso['obatalkes_id'];
                            $tmpInsert_temp['tglkadaluarsa'] = $v_dso['tglkadaluarsa'];
                            $tmpInsert_temp['nobatch'] = @$v['nobatch'];
                            $tmpInsert_temp['satuankecil_id'] = @$v['satuankecil_id'];
                            $tmpInsert_temp['qtystok_in'] = 0;
                            $tmpInsert_temp['qtystok_out'] = abs($isi);
                            $tmpInsert_temp['tglstok_in'] = null;
                            $tmpInsert_temp['tglstok_out'] = date('Y-m-d H:i:s');
                            $tmpInsert_temp['persendiscount'] = 0;
                            $tmpInsert_temp['jmldiscount'] = 0;
                            $tmpInsert_temp['persenmargin'] = 0;
                            $tmpInsert_temp['jmlmargin'] = 0;
                            $tmpInsert_temp['persenppn'] = 0;
                            $tmpInsert_temp['jmlppn'] = 0;
                            $tmpInsert_temp['harganetto'] = @$v_dso['harganetto'];
                            $tmpInsert_temp['stokoa_aktif'] = TRUE;
                            $tmpInsert_temp['stokobatalkesasal_id'] = $v['id_stok'];
                            $tmpInsert[] = $tmpInsert_temp;
                            if($qty_fisik <= 0) break;
                        endforeach;
                    }
                }
            }

            foreach ($tmpInsert as $k => $v) {
                $tmpInsert[$k]['harga_netto_avg'] = isset($dataMasterObatAlkes[$v['obatalkes_id']]) ? $dataMasterObatAlkes[$v['obatalkes_id']] : 0;
            }
            // var_dump($tmpInsert);exit;
            // insert new stok awal
            ModelStok::batchInsert($tmpInsert, false);
        }

        return true;
    }

    private function getStokOpnameDetail($stokopname_id) {
        return Yii::$app->db->createCommand("
            SELECT
                sot.stokopnamedetail_id,
                sot.obatalkes_id,
                sot.obatalkes_namalain,
                sot.volume_fisik,
                sot.volume_sistem,
                sot.harganetto,
                sot.stokobatalkes_id,
                sot.stok_sistem,
                sot.stok_selisih,
                (
                select coalesce(max(st.tglkadaluarsa),'2025-12-31')
                from stokobatalkes_t st where st.obatalkes_id = sot.obatalkes_id and st.ruangan_id = sot.ruangan_id
                ) as  max_kadaluarsa
            FROM
                infostokopnamedetail_v sot
            WHERE
                sot.stokopname_id = {$stokopname_id}
        ")->queryAll();
    }

    private function getStokKadaluarsa($ruangan_id, $inCondition, $tgl_implementasi = null) {
        $tglCondition = '';
        if (!empty($tgl_implementasi)) {
            $tglCondition = "and COALESCE(tglstok_in, tglstok_out) <= '". date('Y-m-d H:i:s', strtotime($tgl_implementasi))."'";
        }
        
        return Yii::$app->db->createCommand("
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
                WHERE obatalkes_id IN {$inCondition} AND ruangan_id = {$ruangan_id}
                {$tglCondition}
                GROUP BY stokobatalkes_t.stokobatalkesasal_id, stokobatalkes_t.tglkadaluarsa
            ) tmp ON st.stokobatalkes_id = tmp.stokobatalkesasal_id
            WHERE st.obatalkes_id IN {$inCondition} AND st.ruangan_id = {$ruangan_id}
            AND (CASE
                WHEN (tmp.stokobatalkesasal_id is null) THEN st.qtystok_in
                ELSE (st.qtystok_in - tmp.qtystok_out)
            end > 0)
            {$tglCondition}
            ORDER BY st.tglkadaluarsa ASC
        ")->queryAll();
    }

    public static function updateStokOpname($stokopname_id, $ruangan_id, $is_stokawal = false, $tgl_implementasi = null)
    {
        if (empty($stokopname_id)) return true;
        $datenow = date('Y-m-d H:i:s');
        // get data stok opname detail
        $data_sodetail = self::getStokOpnameDetail($stokopname_id);

        // get data stok obat berdasarkan obatalkes_id detail SO
        $listOfOutStock = $idObatAlkes = [];
        $idObatAlkes = count($data_sodetail) > 0 ? array_column($data_sodetail, 'obatalkes_id') : [];
        $inCondition = "(" . implode(",", $idObatAlkes) . ")";
        $query = self::getStokKadaluarsa($ruangan_id, $inCondition, $tgl_implementasi);

        /**
        * Find harga netto average from master obat alkes
        * 1 Juli 2020
        */
        $dataMasterObatAlkes = $dataSatuanKecilObat = [];
        if(count($idObatAlkes)>0){
            $queryMasterObatAlkes = "SELECT obatalkes_id,hargaratarata,satuankecil_id FROM obatalkes_m WHERE obatalkes_id IN {$inCondition}";
            $rawDataMasterObatAlkes = Yii::$app->db->createCommand($queryMasterObatAlkes)->queryAll();
            if(is_array($rawDataMasterObatAlkes) && count($rawDataMasterObatAlkes)>0){
                $dataMasterObatAlkes = array_column($rawDataMasterObatAlkes, 'hargaratarata','obatalkes_id');
                $dataSatuanKecilObat = array_column($rawDataMasterObatAlkes, 'satuankecil_id','obatalkes_id');
            }
        }

        // case stok awal
        if ($is_stokawal){
            $tmpInsert = [];
            $tmpObat = [];
            $listObat = [];
            // prepare insert stok awal
            foreach ($query as $v_q) {
                $listOfOutStock[] = $v_q['id_stok'];
                $tmpObat[$v_q['obatalkes_id']] = $v_q;
            }
            foreach ($data_sodetail as $key => $val) {
                $listObat[$val['obatalkes_id']][] = $val['volume_fisik'];
            }
            $exceptionList = [];
            foreach ($data_sodetail as $k => $v_dso) {
                $tmpInsert_temp = [];
                $stok_now = $v_q['total_stok'];
                $stok_fisik = $v_dso['volume_fisik'];
                $tmpInsert_temp['ruangan_id'] = $ruangan_id;
                $tmpInsert_temp['stokopnamedetail_id'] = $v_dso['stokopnamedetail_id'];
                $tmpInsert_temp['obatalkes_id'] = $v_dso['obatalkes_id'];
                $tmpInsert_temp['nobatch'] = $tmpObat[$v_dso['obatalkes_id']]['nobatch'];
                $tmpInsert_temp['satuankecil_id'] = $tmpObat[$v_dso['obatalkes_id']]['satuankecil_id'];
                $tmpInsert_temp['tglstok_in'] = date('Y-m-d');
                $tmpInsert_temp['qtystok_in'] = $stok_fisik;
                $tmpInsert_temp['qtystok_out'] = 0;
                $tmpInsert_temp['harganetto'] = @$v_dso['harganetto'];
                $tmpInsert_temp['persendiscount'] = 0;
                $tmpInsert_temp['jmldiscount'] = 0;
                $tmpInsert_temp['persenmargin'] = 0;
                $tmpInsert_temp['jmlmargin'] = 0;
                $tmpInsert_temp['persenppn'] = 0;
                $tmpInsert_temp['jmlppn'] = 0;
                $tmpInsert_temp['stokoa_aktif'] = TRUE;
                $tmpInsert_temp['additional_data'] = null;
                if(!in_array($v_dso['obatalkes_id'], $exceptionList)){
                    $additional = [
                        'is_stokawal' => true,
                        'total_stok'=>array_sum( $listObat[$v_dso['obatalkes_id']] )
                    ];
                    $tmpInsert_temp['additional_data'] = json_encode($additional);
                    array_push($exceptionList, $v_dso['obatalkes_id']);
                }
                $tmpInsert[] = $tmpInsert_temp;
            }

            // Update Stok jadiin false
            if ($listOfOutStock) {
                $inCondition = "(" . implode(",", $listOfOutStock) . ")";
                Yii::$app->db->createCommand("
                    UPDATE stokobatalkes_t SET stokoa_aktif = false, is_active = false
                    WHERE (stokobatalkesasal_id IN {$inCondition} OR stokobatalkes_id IN {$inCondition})
                    AND stokoa_aktif = true
                ")->execute();
            }

            foreach ($tmpInsert as $k => $v) {
                $tmpInsert[$k]['harga_netto_avg'] = isset($dataMasterObatAlkes[$v['obatalkes_id']]) ? $dataMasterObatAlkes[$v['obatalkes_id']] : 0;
            }
            // insert new stok awal
            ModelStok::batchInsert($tmpInsert, false);
        // case penyesuaian
        }else{
            $tmpInsert = [];
            $dataObat = [];
            // prepare insert stok awal
            foreach ($query as $k => $detail) {
                if(isset($dataSatuanKecilObat[$detail['obatalkes_id']]) && empty($detail['satuankecil_id'])){
                    $detail['satuankecil_id'] = $dataSatuanKecilObat[$detail['obatalkes_id']];
                }

                $dataObat[$detail['obatalkes_id']][] = $detail;
            }

            $arr_key_aktif = array_keys($dataObat);

            foreach ($data_sodetail as $v_dso) {
                $primary = $v_dso['obatalkes_id'];
                $is_stokoa_aktif = in_array($primary, $arr_key_aktif);
                if(!empty($tgl_implementasi)){
                    $stokOfTheDay = isset($v_dso['stok_sistem']) ? $v_dso['stok_sistem'] : $v_dso['volume_sistem'];
                    $calculate = $v_dso['volume_fisik'] - $stokOfTheDay;
                }else{
                    $calculate = $v_dso['stok_selisih'];
                }

                $is_stokin = $calculate > 0 ? true : false;
                $qty_fisik = abs($calculate);
                if ($calculate != 0) {
                    if ($is_stokin) {

                        $_tglkadaluarsa = '2025-12-31';
                        if(isset($dataObat[$primary][0]['tglkadaluarsa'])){
                            $_tglkadaluarsa = $dataObat[$primary][0]['tglkadaluarsa'];
                        }else if(isset($v_dso['max_kadaluarsa'])){
                            $_tglkadaluarsa = $v_dso['max_kadaluarsa'];
                        }

                        // stok in
                        $tmpInsert_temp['ruangan_id'] = $ruangan_id;
                        $tmpInsert_temp['stokopnamedetail_id'] = $v_dso['stokopnamedetail_id'];
                        $tmpInsert_temp['obatalkes_id'] = $v_dso['obatalkes_id'];
                        $tmpInsert_temp['nobatch'] = @$dataObat[$primary][0]['nobatch'];
                        $tmpInsert_temp['satuankecil_id'] = @$dataSatuanKecilObat[$v_dso['obatalkes_id']];
                        $tmpInsert_temp['tglkadaluarsa'] = $_tglkadaluarsa;
                        $tmpInsert_temp['qtystok_in'] = $qty_fisik;
                        $tmpInsert_temp['qtystok_out'] = 0;
                        $tmpInsert_temp['tglstok_in'] = !empty($tgl_implementasi) ? $tgl_implementasi : date('Y-m-d H:i:s');
                        $tmpInsert_temp['tglstok_out'] = null;
                        $tmpInsert_temp['persendiscount'] = 0;
                        $tmpInsert_temp['jmldiscount'] = 0;
                        $tmpInsert_temp['persenmargin'] = 0;
                        $tmpInsert_temp['jmlmargin'] = 0;
                        $tmpInsert_temp['persenppn'] = 0;
                        $tmpInsert_temp['jmlppn'] = 0;
                        $tmpInsert_temp['harganetto'] = @$v_dso['harganetto'];
                        $tmpInsert_temp['stokoa_aktif'] = TRUE;
                        $tmpInsert_temp['stokobatalkesasal_id'] = null;
                        $tmpInsert[] = $tmpInsert_temp;
                    } else {
                        // stok out
                        if($is_stokoa_aktif) {
                            if(!empty($tgl_implementasi)){
                                if ($qty_fisik > $stokOfTheDay) {
                                    return ['message' => 'Stok obat '.$v_dso['obatalkes_namalain'].' tidak mencukupi'];
                                }
                            }else{
                                if ($qty_fisik > ArrayHelper::getValue($v_dso, 'stok_sistem', 0)) {
                                    return ['message' => 'Stok obat '.$v_dso['obatalkes_namalain'].' tidak mencukupi'];
                                }
                            }

                            // potong stok untuk obat yang memiliki stokoa_aktif = true
                            $listOfOutStock = [];
                            foreach($dataObat[$primary] as $k => $v):
                                $stoknow = $v['total_stok'];

                                if($qty_fisik >= $stoknow){
                                    $isi = $stoknow;
                                    $qty_fisik -= $stoknow;
                                    $listOfOutStock[] = $v['id_stok'];
                                    $stoknow = 0;
                                } else {
                                    $isi = $qty_fisik;
                                    $qty_fisik = 0;
                                }

                                $tmpInsert_temp = self::setPayloadStokOut($ruangan_id, $v_dso, $v, $isi, $tgl_implementasi);
                                $tmpInsert_temp['stokobatalkesasal_id'] = $v['id_stok'];
                                $tmpInsert[] = $tmpInsert_temp;
                                if($qty_fisik <= 0) break;
                            endforeach;
                        } else {
                            return ['message' => 'Terdapat obat dengan stok sistem 0'];
                        }
                    }
                }
            }

            foreach ($tmpInsert as $k => $v) {
                $tmpInsert[$k]['harga_netto_avg'] = isset($dataMasterObatAlkes[$v['obatalkes_id']]) ? $dataMasterObatAlkes[$v['obatalkes_id']] : 0;
            }

            // insert new stok awal
            ModelStok::batchInsert($tmpInsert, false);

            /** Update Stok jadiin false **/
            if ($listOfOutStock) {
                self::updateOutOfStock($listOfOutStock);
            }
        }

        return true;
    }

    private function setPayloadStokOut($ruangan_id, $v_dso, $value, $isi, $tgl_implementasi = null) {
        $tmpInsert_temp['ruangan_id'] = $ruangan_id;
        $tmpInsert_temp['stokopnamedetail_id'] = $v_dso['stokopnamedetail_id'];
        $tmpInsert_temp['obatalkes_id'] = $v_dso['obatalkes_id'];
        $tmpInsert_temp['nobatch'] = @$value['nobatch'];
        $tmpInsert_temp['satuankecil_id'] = @$value['satuankecil_id'];
        $tmpInsert_temp['tglkadaluarsa'] = $value['tglkadaluarsa'];
        $tmpInsert_temp['qtystok_in'] = 0;
        $tmpInsert_temp['qtystok_out'] = abs($isi);
        $tmpInsert_temp['tglstok_in'] = null;
        $tmpInsert_temp['tglstok_out'] = !empty($tgl_implementasi) ? $tgl_implementasi : date('Y-m-d H:i:s');
        $tmpInsert_temp['persendiscount'] = 0;
        $tmpInsert_temp['jmldiscount'] = 0;
        $tmpInsert_temp['persenmargin'] = 0;
        $tmpInsert_temp['jmlmargin'] = 0;
        $tmpInsert_temp['persenppn'] = 0;
        $tmpInsert_temp['jmlppn'] = 0;
        $tmpInsert_temp['harganetto'] = @$v_dso['harganetto'];
        $tmpInsert_temp['stokoa_aktif'] = FALSE;

        return $tmpInsert_temp;
    }

    public static function returnObatPasien($data_obatalkespasien = [])
    {
        $obatalkespasien_id = "(" . implode(",", $data_obatalkespasien) . ")";
        try {
            $connection = Yii::$app->db;
            $data_stok = $connection->createCommand("
                SELECT
                    stokobatalkesasal_id, 
                    stokobatalkes_id,
                    ruangan_id, 
                    tglkadaluarsa, 
                    nobatch, 
                    harganetto, 
                    persendiscount, 
                    jmldiscount,
                    persenppn, 
                    jmlppn, 
                    persenmargin, 
                    jmlmargin,
                    obatalkespasien_id, 
                    obatalkes_id,
                    qtystok_in,
                    qtystok_out,
                    satuankecil_id
                FROM stokobatalkes_t st
                WHERE obatalkespasien_id IN $obatalkespasien_id
                ORDER BY st.tglkadaluarsa ASC, st.tglstok_in ASC
            ")
            ->queryAll();

            $insert_stokobatalkes = [];
            foreach ($data_stok as $obat) {
                $qty_in = $obat['qtystok_out'];

                $insert_stokobatalkes[] = [
                    'ruangan_id'          => $obat['ruangan_id'],
                    'tglstok_in'          => date('Y-m-d H:i:s'),
                    'stokoa_aktif'        => true,
                    'qtystok_out'         => 0,
                    'obatalkespasien_id'  => $obat['obatalkespasien_id'],
                    'obatalkes_id'        => $obat['obatalkes_id'],
                    'tglkadaluarsa'       => $obat['tglkadaluarsa'],
                    'nobatch'             => $obat['nobatch'],
                    'qtystok_in'          => $qty_in,
                    'satuankecil_id'      => $obat['satuankecil_id'],
                    'harganetto'          => $obat['harganetto'],
                    'persendiscount'      => $obat['persendiscount'],
                    'jmldiscount'         => $obat['jmldiscount'],
                    'persenppn'           => $obat['persenppn'],
                    'jmlppn'              => $obat['jmlppn'],
                    'persenmargin'        => $obat['persenmargin'],
                    'jmlmargin'           => $obat['jmlmargin'],
                ];
            }

            ModelStok::batchInsert($insert_stokobatalkes);
            return true;

        } catch (\Exception $e) {
            Yii::error([
                'log' => $e->getMessage()
            ]);
            throw $e;
        } catch (\yii\db\Exception $e) {
            Yii::error([
                'log' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public static function returnObatPasienPartial($data_obatalkespasien = [])
    {
        $oapasien_ids = array_keys($data_obatalkespasien);
        $obatalkespasien_id = "(" . implode(",", $oapasien_ids) . ")";
        try {
            $connection = Yii::$app->db;
            $data_stok = $connection->createCommand("
                SELECT
                    stokobatalkesasal_id, 
                    stokobatalkes_id,
                    ruangan_id, 
                    tglkadaluarsa, 
                    nobatch, 
                    harganetto, 
                    persendiscount, 
                    jmldiscount,
                    persenppn, 
                    jmlppn, 
                    persenmargin, 
                    jmlmargin,
                    obatalkespasien_id, 
                    obatalkes_id,
                    qtystok_in,
                    qtystok_out,
                    satuankecil_id
                FROM stokobatalkes_t st
                WHERE obatalkespasien_id IN $obatalkespasien_id
                ORDER BY st.tglkadaluarsa DESC, st.tglstok_in ASC
            ")
            ->queryAll();

            $data_stok = ArrayHelper::index($data_stok, null, 'obatalkespasien_id');
            $insert_stokobatalkes = [];
            foreach ($data_stok as $oapasien_id => $stokObat) {
                $qty_retur = $data_obatalkespasien[$oapasien_id]['qty_retur'];
                $ruangan_id = $data_obatalkespasien[$oapasien_id]['ruangan_id'];
                $is_deleted = $data_obatalkespasien[$oapasien_id]['is_deleted'];
                $nama_obat = $data_obatalkespasien[$oapasien_id]['obatalkes_nama'];
                $returresepdetail_id = isset($data_obatalkespasien[$oapasien_id]['returresepdetail_id']) ? $data_obatalkespasien[$oapasien_id]['returresepdetail_id'] : null;
                foreach($stokObat as $obat) {
                    // handle hapus item di penata jasa
                    if($obat['qtystok_out'] - $qty_retur < 0 && $is_deleted) {            
            			throw new \Exception("Stok transaksi obat {$nama_obat} tidak mencukupi. Silakan cek kembali transaksi retur.", 1);
                    }

                    $qty_in = $obat['qtystok_out'] - $qty_retur >= 0 ? $qty_retur : $obat['qtystok_out'];
                    $qty_retur -= $qty_in;
                    if($qty_in > 0) {
                        $insert_stokobatalkes[] = [
                            'ruangan_id' => $ruangan_id,
                            'tglstok_in' => date('Y-m-d H:i:s'),
                            'stokoa_aktif' => true,
                            'qtystok_out' => 0,
                            'obatalkespasien_id' => isset($obat['obatalkespasien_id']) ? $obat['obatalkespasien_id'] : null,
                            'returresepdetail_id' => $returresepdetail_id,
                            'obatalkes_id' => $obat['obatalkes_id'],
                            'tglkadaluarsa' => $obat['tglkadaluarsa'],
                            'nobatch' => $obat['nobatch'],
                            'qtystok_in' => $qty_in,
                            'satuankecil_id' => $obat['satuankecil_id'],
                            'harganetto' => $obat['harganetto'],
                            'persendiscount' => $obat['persendiscount'],
                            'jmldiscount' => $obat['jmldiscount'],
                            'persenppn' => $obat['persenppn'],
                            'jmlppn' => $obat['jmlppn'],
                            'persenmargin' => $obat['persenmargin'],
                            'jmlmargin' => $obat['jmlmargin'],
                        ];
                    }
                }
            }

            ModelStok::batchInsert($insert_stokobatalkes);
            return true;
        } catch (\Exception $e) {
            Yii::error([
                'log' => $e->getMessage()
            ]);
            throw $e;
        } catch (\yii\db\Exception $e) {
            Yii::error([
                'log' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
