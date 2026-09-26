<?php
namespace app\modules\v1\businessLogic;

/**
** @author yaya
**/

use Yii;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\StokBarang as ModelStok;
use yii\db\Expression;

class StokBarang
{
    const FEFO = 'LIFO';
    const FIFO = 'FIFO';

    public static function methodeFEFO($data = [],$tanggalPemakaian, $mutasi = false)
    {
        // Skip ketika tidak di temukan datanya
        if (!count($data)) return true;
        $request = Yii::$app->request;
        $jwtRuangan = Yii::$app->jwt->ruangan_id;
        $ruangan_id = $request->post('ruanganasalmutasi_id') !== null ? $request->post('ruanganasalmutasi_id') : $jwtRuangan;
        $listIdDetail = $idBarang = [];
        foreach ($data as $val) {
            $idBarangAdjustment = $val['barang_id'];
            $listIdDetail[$idBarangAdjustment] = $val;
            $idBarang[] = $idBarangAdjustment;
            $ruangan_id = isset($val['ruangan_id']) ? $val['ruangan_id'] : $jwtRuangan;
        }
        $inCondition = "(" . implode(",", $idBarang) . ")";

        $query = Yii::$app->db->createCommand("
            SELECT barang_id,id_stok,
            SUM(dadang.qtystok_in - dadang.qtystok_out) as total_stok,
            dadang.tglkadaluarsa,nobatch,harganetto,persendiscount,jmldiscount,persenppn,persenpph,persenmargin,jmlmargin
            FROM (
                SELECT (CASE WHEN stokbarangasal_id IS NULL THEN stokbarang_id ELSE stokbarangasal_id END) as id_stok,
                barang_id,
                qtystok_in,
                qtystok_out, nobatch,harganetto,persendiscount,
                jmldiscount,persenppn,persenpph,persenmargin,jmlmargin,
                tglkadaluarsa FROM stokbarang_t
                WHERE barang_id IN {$inCondition} AND ruangan_id = {$ruangan_id}
            ) as dadang
            GROUP BY dadang.id_stok,dadang.tglkadaluarsa,barang_id,nobatch,
            harganetto,persendiscount,jmldiscount,persenppn,persenpph,persenmargin,jmlmargin
            HAVING SUM(dadang.qtystok_in - dadang.qtystok_out) > 0
            ORDER BY dadang.barang_id,dadang.tglkadaluarsa ASC
        ")->queryAll();

        $tmpInsert = $listOfOutStock = [];
        // Kuncinya qty_satuanpakai selalu berkurang
        if(!empty($query)) {
            foreach ($query as $detail) {
                $idBarangAdjustment = $detail['barang_id'];
                if (isset($listIdDetail[$idBarangAdjustment])) {
                    // ini stok permintaan yang di inputkan
                    // contoh permintaan 250 sedangkan stok detail nya 100
                    $stokItem = $listIdDetail[$idBarangAdjustment]['qty_satuanpakai'];
                    // Lewati ketika stok sudah 0
                    if ($stokItem <= 0) continue;
                    $totalStok = $detail['total_stok'];
                    $idTerima = isset($listIdDetail[$idBarangAdjustment]['terimamutasidetail_id'])
                                                ? $listIdDetail[$idBarangAdjustment]['terimamutasidetail_id'] : null;
                    if ($stokItem > $totalStok) {
                        $listOfOutStock[] = $detail['id_stok'];
                        // ini akan membuat row baru dengan catatan stok harus di update false
                        $row = [
                            'ruangan_id' => $ruangan_id,
                            'ruangantujuan_id' => ($mutasi) && isset($listIdDetail[$idBarangAdjustment]['ruangantujuan_id']) ? $listIdDetail[$idBarangAdjustment]['ruangantujuan_id'] : null,
                            'barang_id' => $idBarangAdjustment,
                            'tglkadaluarsa' => $detail['tglkadaluarsa'],
                            'nobatch' => $detail['nobatch'],
                            'harganetto' => $detail['harganetto'],
                            'persendiscount' => $detail['persendiscount'],
                            'jmldiscount' => $detail['jmldiscount'],
                            'persenppn' => $detail['persenppn'],
                            'persenpph' => $detail['persenpph'],
                            'persenmargin' => $detail['persenmargin'],
                            'jmlmargin' => $detail['jmlmargin'],
                            'qtystok_in' => 0,
                            'tglstok_out' => date('Y-m-d H:i:s',strtotime($tanggalPemakaian)),
                            'qtystok_out' => $totalStok,
                            'stokbarangasal_id' => $detail['id_stok'],
                            'satuankecil_id' => isset($listIdDetail[$idBarangAdjustment]['satuankecil_id'])
                                                    ? $listIdDetail[$idBarangAdjustment]['satuankecil_id'] : null,
                            'pemakaianbarangdetail_id' => isset($listIdDetail[$idBarangAdjustment]['pemakaianbarangdetail_id'])
                                                    ? $listIdDetail[$idBarangAdjustment]['pemakaianbarangdetail_id'] : null,
                            'mutasibarangdetail_id' => isset($listIdDetail[$idBarangAdjustment]['mutasibarangdetail_id'])
                                                    ? $listIdDetail[$idBarangAdjustment]['mutasibarangdetail_id'] : null,
                            'adjusmenbarangkeluar_id' => isset($listIdDetail[$idBarangAdjustment]['adjusmenbarangkeluar_id'])
                                                    ? $listIdDetail[$idBarangAdjustment]['adjusmenbarangkeluar_id'] : null,
                            'returbarangdetail_id' => isset($listIdDetail[$idBarangAdjustment]['returbarangdetail_id'])
                                                    ? $listIdDetail[$idBarangAdjustment]['returbarangdetail_id'] : null,
                            'stokbarang_aktif' => true,
                            'is_active' => true
                        ];
                        $tmpInsert[] = $row;
                        if ($mutasi) {
                            $tmpInsert[] = self::generateMutasi($row, $idTerima);
                        }
                        $listIdDetail[$idBarangAdjustment]['qty_satuanpakai'] = $stokItem - $totalStok;
                    } else {
                        if ($listIdDetail[$idBarangAdjustment]['qty_satuanpakai'] == $totalStok) {
                            $listOfOutStock[] = $detail['id_stok'];
                        }
                        $row = [
                            'ruangan_id' => $ruangan_id,
                            'ruangantujuan_id' => ($mutasi) && isset($listIdDetail[$idBarangAdjustment]['ruangantujuan_id']) ? $listIdDetail[$idBarangAdjustment]['ruangantujuan_id'] : null,
                            'barang_id' => $idBarangAdjustment,
                            'tglkadaluarsa' => $detail['tglkadaluarsa'],
                            'nobatch' => $detail['nobatch'],
                            'harganetto' => $detail['harganetto'],
                            'persendiscount' => $detail['persendiscount'],
                            'jmldiscount' => $detail['jmldiscount'],
                            'persenppn' => $detail['persenppn'],
                            'persenpph' => $detail['persenpph'],
                            'persenmargin' => $detail['persenmargin'],
                            'jmlmargin' => $detail['jmlmargin'],
                            'qtystok_in' => 0,
                            'tglstok_out' => date('Y-m-d H:i:s',strtotime($tanggalPemakaian)),
                            'qtystok_out' => $listIdDetail[$idBarangAdjustment]['qty_satuanpakai'],
                            'stokbarangasal_id' => $detail['id_stok'],
                            'satuankecil_id' => isset($listIdDetail[$idBarangAdjustment]['satuankecil_id'])
                                                    ? $listIdDetail[$idBarangAdjustment]['satuankecil_id'] : null,
                            'pemakaianbarangdetail_id' => isset($listIdDetail[$idBarangAdjustment]['pemakaianbarangdetail_id'])
                                                    ? $listIdDetail[$idBarangAdjustment]['pemakaianbarangdetail_id'] : null,
                            'mutasibarangdetail_id' => isset($listIdDetail[$idBarangAdjustment]['mutasibarangdetail_id'])
                                                    ? $listIdDetail[$idBarangAdjustment]['mutasibarangdetail_id'] : null,
                            'adjusmenbarangkeluar_id' => isset($listIdDetail[$idBarangAdjustment]['adjusmenbarangkeluar_id'])
                                                    ? $listIdDetail[$idBarangAdjustment]['adjusmenbarangkeluar_id'] : null,
                            'returbarangdetail_id' => isset($listIdDetail[$idBarangAdjustment]['returbarangdetail_id'])
                                                    ? $listIdDetail[$idBarangAdjustment]['returbarangdetail_id'] : null,
                            'stokbarang_aktif' => false,
                            'is_active' => true
                        ];
                        $tmpInsert[] = $row;
                        if ($mutasi) {
                            $tmpInsert[] = self::generateMutasi($row, $idTerima);
                        }
                        $listIdDetail[$idBarangAdjustment]['qty_satuanpakai'] = 0;
                    }
                }
            }
        }


        if (!empty($tmpInsert) && !$mutasi) {
            foreach ($tmpInsert as $k => $v) {
                if( empty($v["qtystok_in"]) && empty($v["qtystok_out"]) ) {
                    unset($tmpInsert[$k]);
                }
            }
        }
        // Insert
        if (!empty($tmpInsert)) {
            ModelStok::batchInsert($tmpInsert,false);
        }

        // Update Stok jadiin false
        if (!empty($listOfOutStock)) {
            /** dinonaktifkan
            $inCondition = "(" . implode(",", $listOfOutStock) . ")";
            Yii::$app->db->createCommand("
                UPDATE stokbarang_t SET stokbarang_aktif = false, is_active = false
                WHERE (stokbarangasal_id IN {$inCondition} OR stokbarang_id IN {$inCondition})
                AND stokbarang_aktif = true
            ")->execute();
            */
        }
        return true;
    }

    public static function methodeFIFO($data = [],$tanggalPemakaian, $mutasi = false)
    {
        // Skip ketika tidak di temukan datanya
        if (!count($data)) return true;
        $request = Yii::$app->request;
        $jwtRuangan = Yii::$app->jwt->ruangan_id;
        $ruangan_id = $request->post('ruanganasalmutasi_id') !== null ? $request->post('ruanganasalmutasi_id') : $jwtRuangan;
        $listIdDetail = $idBarang = [];
        foreach ($data as $val) {
            $idBarangAdjustment = $val['barang_id'];
            $listIdDetail[$idBarangAdjustment] = $val;
            $idBarang[] = $idBarangAdjustment;
            $ruangan_id = isset($val['ruangan_id']) ? $val['ruangan_id'] : $jwtRuangan;
        }
        $inCondition = "(" . implode(",", $idBarang) . ")";

        $query = Yii::$app->db->createCommand("
            SELECT * FROM(
            SELECT barang_id,id_stok,
            SUM(riwayatstok.qtystok_in - riwayatstok.qtystok_out) as total_stok,
            riwayatstok.tglkadaluarsa,nobatch,harganetto,persendiscount,jmldiscount,persenppn,persenpph,persenmargin,jmlmargin,tglstok_in
            FROM (
                SELECT (CASE WHEN t.stokbarangasal_id IS NULL THEN t.stokbarang_id ELSE t.stokbarangasal_id END) as id_stok,
                t.barang_id,
                t.qtystok_in,
                (CASE WHEN t.tglstok_in IS NULL THEN child.tglstok_in ELSE t.tglstok_in END) as tglstok_in,
                t.qtystok_out, t.nobatch,t.harganetto,t.persendiscount,
                t.jmldiscount,t.persenppn,t.persenpph,t.persenmargin,t.jmlmargin,
                t.tglkadaluarsa FROM stokbarang_t t
                LEFT JOIN stokbarang_t child ON t.stokbarangasal_id = child.stokbarang_id
                WHERE t.barang_id IN {$inCondition} AND t.ruangan_id = {$ruangan_id}
            ) as riwayatstok
            GROUP BY riwayatstok.id_stok,riwayatstok.tglkadaluarsa,barang_id,nobatch,
            harganetto,persendiscount,jmldiscount,persenppn,persenpph,persenmargin,jmlmargin,tglstok_in
            ) t
            WHERE t.total_stok > 0
            ORDER BY t.barang_id,t.tglstok_in ASC
        ")->queryAll();

        $tmpInsert = $listOfOutStock = [];
        // Kuncinya qty_satuanpakai selalu berkurang
        foreach ($query as $detail) {
            $idBarangAdjustment = $detail['barang_id'];
            if (isset($listIdDetail[$idBarangAdjustment])) {
                // ini stok permintaan yang di inputkan
                // contoh permintaan 250 sedangkan stok detail nya 100
                $stokItem = $listIdDetail[$idBarangAdjustment]['qty_satuanpakai'];
                // Lewati ketika stok sudah 0
                if ($stokItem <= 0) continue;
                $totalStok = $detail['total_stok'];
                $idTerima = isset($listIdDetail[$idBarangAdjustment]['terimamutasidetail_id'])
                                            ? $listIdDetail[$idBarangAdjustment]['terimamutasidetail_id'] : null;
                if ($stokItem > $totalStok) {
                    $listOfOutStock[] = $detail['id_stok'];
                    // ini akan membuat row baru dengan catatan stok harus di update false
                    $row = [
                        'ruangan_id' => $ruangan_id,
                        'barang_id' => $idBarangAdjustment,
                        'tglkadaluarsa' => $detail['tglkadaluarsa'],
                        'nobatch' => $detail['nobatch'],
                        'harganetto' => $detail['harganetto'],
                        'persendiscount' => $detail['persendiscount'],
                        'jmldiscount' => $detail['jmldiscount'],
                        'persenppn' => $detail['persenppn'],
                        'persenpph' => $detail['persenpph'],
                        'persenmargin' => $detail['persenmargin'],
                        'jmlmargin' => $detail['jmlmargin'],
                        'qtystok_in' => 0,
                        'tglstok_out' => date('Y-m-d H:i:s',strtotime($tanggalPemakaian)),
                        'qtystok_out' => $totalStok,
                        'stokbarangasal_id' => $detail['id_stok'],
                        'satuankecil_id' => isset($listIdDetail[$idBarangAdjustment]['satuankecil_id'])
                                                ? $listIdDetail[$idBarangAdjustment]['satuankecil_id'] : null,
                        'pemakaianbarangdetail_id' => isset($listIdDetail[$idBarangAdjustment]['pemakaianbarangdetail_id'])
                                                ? $listIdDetail[$idBarangAdjustment]['pemakaianbarangdetail_id'] : null,
                        'mutasibarangdetail_id' => isset($listIdDetail[$idBarangAdjustment]['mutasibarangdetail_id'])
                                                ? $listIdDetail[$idBarangAdjustment]['mutasibarangdetail_id'] : null,
                        'adjusmenbarangkeluar_id' => isset($listIdDetail[$idBarangAdjustment]['adjusmenbarangkeluar_id'])
                                                ? $listIdDetail[$idBarangAdjustment]['adjusmenbarangkeluar_id'] : null,
                        'returbarangdetail_id' => isset($listIdDetail[$idBarangAdjustment]['returbarangdetail_id'])
                                                ? $listIdDetail[$idBarangAdjustment]['returbarangdetail_id'] : null,
                        'stokbarang_aktif' => false,
                        'is_active' => false
                    ];
                    $tmpInsert[] = $row;
                    if ($mutasi) {
                        $tmpInsert[] = self::generateMutasi($row, $idTerima);
                    }
                    $listIdDetail[$idBarangAdjustment]['qty_satuanpakai'] = $stokItem - $totalStok;
                } else {
                    if ($listIdDetail[$idBarangAdjustment]['qty_satuanpakai'] == $totalStok) {
                        $listOfOutStock[] = $detail['id_stok'];
                    }
                    $row = [
                        'ruangan_id' => $ruangan_id,
                        'barang_id' => $idBarangAdjustment,
                        'tglkadaluarsa' => $detail['tglkadaluarsa'],
                        'nobatch' => $detail['nobatch'],
                        'harganetto' => $detail['harganetto'],
                        'persendiscount' => $detail['persendiscount'],
                        'jmldiscount' => $detail['jmldiscount'],
                        'persenppn' => $detail['persenppn'],
                        'persenpph' => $detail['persenpph'],
                        'persenmargin' => $detail['persenmargin'],
                        'jmlmargin' => $detail['jmlmargin'],
                        'qtystok_in' => 0,
                        'tglstok_out' => date('Y-m-d H:i:s',strtotime($tanggalPemakaian)),
                        'qtystok_out' => $listIdDetail[$idBarangAdjustment]['qty_satuanpakai'],
                        'stokbarangasal_id' => $detail['id_stok'],
                        'satuankecil_id' => isset($listIdDetail[$idBarangAdjustment]['satuankecil_id'])
                                                ? $listIdDetail[$idBarangAdjustment]['satuankecil_id'] : null,
                        'pemakaianbarangdetail_id' => isset($listIdDetail[$idBarangAdjustment]['pemakaianbarangdetail_id'])
                                                ? $listIdDetail[$idBarangAdjustment]['pemakaianbarangdetail_id'] : null,
                        'mutasibarangdetail_id' => isset($listIdDetail[$idBarangAdjustment]['mutasibarangdetail_id'])
                                                ? $listIdDetail[$idBarangAdjustment]['mutasibarangdetail_id'] : null,
                        'adjusmenbarangkeluar_id' => isset($listIdDetail[$idBarangAdjustment]['adjusmenbarangkeluar_id'])
                                                ? $listIdDetail[$idBarangAdjustment]['adjusmenbarangkeluar_id'] : null,
                        'returbarangdetail_id' => isset($listIdDetail[$idBarangAdjustment]['returbarangdetail_id'])
                                                ? $listIdDetail[$idBarangAdjustment]['returbarangdetail_id'] : null,
                        'stokbarang_aktif' => true,
                        'is_active' => true
                    ];
                    $tmpInsert[] = $row;
                    if ($mutasi) {
                        $tmpInsert[] = self::generateMutasi($row, $idTerima);
                    }
                    $listIdDetail[$idBarangAdjustment]['qty_satuanpakai'] = 0;
                }
            }
        }

        // Insert

        if ($tmpInsert) {
            ModelStok::batchInsert($tmpInsert,false);
        }

        // Update Stok jadiin false
        if ($listOfOutStock) {
            $inCondition = "(" . implode(",", $listOfOutStock) . ")";
            Yii::$app->db->createCommand("
                UPDATE stokbarang_t SET stokbarang_aktif = false, is_active = false
                WHERE (stokbarangasal_id IN {$inCondition} OR stokbarang_id IN {$inCondition})
                AND stokbarang_aktif = true
            ")->execute();
        }
        return true;
    }

    public static function methodeLIFO($data = [],$tanggalPemakaian, $mutasi = false)
    {
        // Skip ketika tidak di temukan datanya
        if (!count($data)) return true;
        $request = Yii::$app->request;
        $jwtRuangan = Yii::$app->jwt->ruangan_id;
        $ruangan_id = $request->post('ruanganasalmutasi_id') !== null ? $request->post('ruanganasalmutasi_id') : $jwtRuangan;
        $listIdDetail = $idBarang = [];
        foreach ($data as $val) {
            $idBarangAdjustment = $val['barang_id'];
            $listIdDetail[$idBarangAdjustment] = $val;
            $idBarang[] = $idBarangAdjustment;
        }
        $inCondition = "(" . implode(",", $idBarang) . ")";

        $query = Yii::$app->db->createCommand("
            SELECT * FROM(
            SELECT barang_id,id_stok,
            SUM(riwayatstok.qtystok_in - riwayatstok.qtystok_out) as total_stok,
            riwayatstok.tglkadaluarsa,nobatch,harganetto,persendiscount,jmldiscount,persenppn,persenpph,persenmargin,jmlmargin,tglstok_in
            FROM (
                SELECT (CASE WHEN t.stokbarangasal_id IS NULL THEN t.stokbarang_id ELSE t.stokbarangasal_id END) as id_stok,
                t.barang_id,
                t.qtystok_in,
                (CASE WHEN t.tglstok_in IS NULL THEN child.tglstok_in ELSE t.tglstok_in END) as tglstok_in,
                t.qtystok_out, t.nobatch,t.harganetto,t.persendiscount,
                t.jmldiscount,t.persenppn,t.persenpph,t.persenmargin,t.jmlmargin,
                t.tglkadaluarsa FROM stokbarang_t t
                LEFT JOIN stokbarang_t child ON t.stokbarangasal_id = child.stokbarang_id
                WHERE t.barang_id IN {$inCondition} AND t.ruangan_id = {$ruangan_id}
            ) as riwayatstok
            GROUP BY riwayatstok.id_stok,riwayatstok.tglkadaluarsa,barang_id,nobatch,
            harganetto,persendiscount,jmldiscount,persenppn,persenpph,persenmargin,jmlmargin,tglstok_in
            ) t
            WHERE t.total_stok > 0
            ORDER BY t.barang_id,t.tglstok_in DESC
        ")->queryAll();

        $tmpInsert = $listOfOutStock = [];
        // Kuncinya qty_satuanpakai selalu berkurang
        foreach ($query as $detail) {
            $idBarangAdjustment = $detail['barang_id'];
            if (isset($listIdDetail[$idBarangAdjustment])) {
                // ini stok permintaan yang di inputkan
                // contoh permintaan 250 sedangkan stok detail nya 100
                $stokItem = $listIdDetail[$idBarangAdjustment]['qty_satuanpakai'];
                // Lewati ketika stok sudah 0
                if ($stokItem <= 0) continue;
                $totalStok = $detail['total_stok'];
                $idTerima = isset($listIdDetail[$idBarangAdjustment]['terimamutasidetail_id'])
                                            ? $listIdDetail[$idBarangAdjustment]['terimamutasidetail_id'] : null;
                if ($stokItem > $totalStok) {
                    $listOfOutStock[] = $detail['id_stok'];
                    // ini akan membuat row baru dengan catatan stok harus di update false
                    $row = [
                        'ruangan_id' => $ruangan_id,
                        'barang_id' => $idBarangAdjustment,
                        'tglkadaluarsa' => $detail['tglkadaluarsa'],
                        'nobatch' => $detail['nobatch'],
                        'harganetto' => $detail['harganetto'],
                        'persendiscount' => $detail['persendiscount'],
                        'jmldiscount' => $detail['jmldiscount'],
                        'persenppn' => $detail['persenppn'],
                        'persenpph' => $detail['persenpph'],
                        'persenmargin' => $detail['persenmargin'],
                        'jmlmargin' => $detail['jmlmargin'],
                        'qtystok_in' => 0,
                        'tglstok_out' => date('Y-m-d H:i:s',strtotime($tanggalPemakaian)),
                        'qtystok_out' => $totalStok,
                        'stokbarangasal_id' => $detail['id_stok'],
                        'satuankecil_id' => isset($listIdDetail[$idBarangAdjustment]['satuankecil_id'])
                                                ? $listIdDetail[$idBarangAdjustment]['satuankecil_id'] : null,
                        'pemakaianbarangdetail_id' => isset($listIdDetail[$idBarangAdjustment]['pemakaianbarangdetail_id'])
                                                ? $listIdDetail[$idBarangAdjustment]['pemakaianbarangdetail_id'] : null,
                        'mutasibarangdetail_id' => isset($listIdDetail[$idBarangAdjustment]['mutasibarangdetail_id'])
                                                ? $listIdDetail[$idBarangAdjustment]['mutasibarangdetail_id'] : null,
                        'adjusmenbarangkeluar_id' => isset($listIdDetail[$idBarangAdjustment]['adjusmenbarangkeluar_id'])
                                                ? $listIdDetail[$idBarangAdjustment]['adjusmenbarangkeluar_id'] : null,
                        'returbarangdetail_id' => isset($listIdDetail[$idBarangAdjustment]['returbarangdetail_id'])
                                                ? $listIdDetail[$idBarangAdjustment]['returbarangdetail_id'] : null,
                        'stokbarang_aktif' => false,
                        'is_active' => false
                    ];
                    $tmpInsert[] = $row;
                    if ($mutasi) {
                        $tmpInsert[] = self::generateMutasi($row, $idTerima);
                    }
                    $listIdDetail[$idBarangAdjustment]['qty_satuanpakai'] = $stokItem - $totalStok;
                } else {
                    if ($listIdDetail[$idBarangAdjustment]['qty_satuanpakai'] == $totalStok) {
                        $listOfOutStock[] = $detail['id_stok'];
                    }
                    $row = [
                        'ruangan_id' => $ruangan_id,
                        'barang_id' => $idBarangAdjustment,
                        'tglkadaluarsa' => $detail['tglkadaluarsa'],
                        'nobatch' => $detail['nobatch'],
                        'harganetto' => $detail['harganetto'],
                        'persendiscount' => $detail['persendiscount'],
                        'jmldiscount' => $detail['jmldiscount'],
                        'persenppn' => $detail['persenppn'],
                        'persenpph' => $detail['persenpph'],
                        'persenmargin' => $detail['persenmargin'],
                        'jmlmargin' => $detail['jmlmargin'],
                        'qtystok_in' => 0,
                        'tglstok_out' => date('Y-m-d H:i:s',strtotime($tanggalPemakaian)),
                        'qtystok_out' => $listIdDetail[$idBarangAdjustment]['qty_satuanpakai'],
                        'stokbarangasal_id' => $detail['id_stok'],
                        'satuankecil_id' => isset($listIdDetail[$idBarangAdjustment]['satuankecil_id'])
                                                ? $listIdDetail[$idBarangAdjustment]['satuankecil_id'] : null,
                        'pemakaianbarangdetail_id' => isset($listIdDetail[$idBarangAdjustment]['pemakaianbarangdetail_id'])
                                                ? $listIdDetail[$idBarangAdjustment]['pemakaianbarangdetail_id'] : null,
                        'mutasibarangdetail_id' => isset($listIdDetail[$idBarangAdjustment]['mutasibarangdetail_id'])
                                                ? $listIdDetail[$idBarangAdjustment]['mutasibarangdetail_id'] : null,
                        'adjusmenbarangkeluar_id' => isset($listIdDetail[$idBarangAdjustment]['adjusmenbarangkeluar_id'])
                                                ? $listIdDetail[$idBarangAdjustment]['adjusmenbarangkeluar_id'] : null,
                        'returbarangdetail_id' => isset($listIdDetail[$idBarangAdjustment]['returbarangdetail_id'])
                                                ? $listIdDetail[$idBarangAdjustment]['returbarangdetail_id'] : null,
                        'stokbarang_aktif' => false,
                        'is_active' => true
                    ];
                    $tmpInsert[] = $row;
                    if ($mutasi) {
                        $tmpInsert[] = self::generateMutasi($row, $idTerima);
                    }
                    $listIdDetail[$idBarangAdjustment]['qty_satuanpakai'] = 0;
                }
            }
        }

        // Insert

        if ($tmpInsert) {
            ModelStok::batchInsert($tmpInsert,false);
        }

        // Update Stok jadiin false
        if ($listOfOutStock) {
            // $inCondition = "(" . implode(",", $listOfOutStock) . ")";
            // Yii::$app->db->createCommand("
            //     UPDATE stokbarang_t SET stokbarang_aktif = false, is_active = false
            //     WHERE (stokbarangasal_id IN {$inCondition} OR stokbarang_id IN {$inCondition})
            //     AND stokbarang_aktif = true
            // ")->execute();
        }
        return true;
    }

    /**
    * ket kegunaan untuk parsing row stokobatalkes_t
    * @var $row Array
    * @var $idTerima Integer
    * @return array
    **/
    public static function generateMutasi($row, $idTerima)
    {
        $request = Yii::$app->request;
        $row['qtystok_in'] = $row['qtystok_out'];
        $row['ruangan_id'] = $row['ruangantujuan_id'];
        $row['tglstok_in'] = $row['tglstok_out'];
        $row['qtystok_out'] = 0;
        $row['terimamutasibarangdetail_id'] = $idTerima;
        $row['stokbarang_aktif'] = true;
        unset($row['tglstok_out']);
        unset($row['stokbarangasal_id']);
        unset($row['mutasibarangdetail_id']);
        return $row;
    }

    /**
     *
     * @author : metafiliana
     * fungsi update stokobatalkes_t saat SO
     *
     */
    public static function updateStokObatAlkes(array $data_obatalkes, $stokopname_id, $ruangan_id, $is_stokawal)
    {
        if (!count($stokopname_id)) return true;
        $request = Yii::$app->request;

        $data_sodetail = Yii::$app->db->createCommand("
            SELECT
                stokopnamebarangdetail_id,
                barang_id,
                volume_fisik,
                volume_sistem,
                harganetto,
                tglkadaluarsa,
                revisi_stok
            FROM 
                stokopnamebarangdetail_t
            WHERE
                stokopnamebarang_id = {$stokopname_id} AND
                is_deleted = FALSE
        ")->queryAll();

        $listOfOutStock = $idBarang = [];
        $idBarang = count($data_sodetail) > 0 ? array_column($data_sodetail, 'barang_id') : [];
        $inCondition = "(" . implode(",", $idBarang) . ")";

        $query = ModelStok::find()
                    ->select(['barang_id',new Expression('SUM(qtystok_in - qtystok_out) as total_stok')])
                    ->where(['ruangan_id'=>$ruangan_id])
                    ->Andwhere(['IN','barang_id',$idBarang])
                    ->groupBy(['barang_id'])->asArray()->all();

        // case stok awal
        if ($is_stokawal){
            $tmpInsert = [];

            // prepare insert stok awal
            foreach ($query as $v_q) {
                foreach ($data_sodetail as $v_dso) {
                    if ($v_dso['stokbarang_id'] == $v_q['id_stok']){
                        $tmpInsert_temp = [];
                        $stok_now = $v_q['total_stok'];
                        $stok_fisik = $v_dso['volume_fisik'];

                        $tmpInsert_temp['ruangan_id'] = $ruangan_id;
                        $tmpInsert_temp['stokopnamebarangdetail_id'] = $v_dso['stokopnamebarangdetail_id'];
                        $tmpInsert_temp['barang_id'] = $v_dso['barang_id'];
                        $tmpInsert_temp['tglkadaluarsa'] = $v_dso['tglkadaluarsa'];
                        $tmpInsert_temp['nobatch'] = @$v_q['nobatch'];
                        $tmpInsert_temp['qtystok_in'] = $stok_fisik;
                        $tmpInsert_temp['harganetto'] = @$v_q['harganetto'];
                        $tmpInsert_temp['stokbarang_aktif'] = TRUE;

                        $tmpInsert[] = $tmpInsert_temp;
                    }
                }
            }

            // Update Stok jadiin false
            if ($listOfOutStock) {
                $inCondition = "(" . implode(",", $listOfOutStock) . ")";
                Yii::$app->db->createCommand("
                    UPDATE stokbarang_t SET stokbarang_aktif = false, is_active = false
                    WHERE (stokbarangasal_id IN {$inCondition} OR stokbarang_id IN {$inCondition})
                    AND stokbarang_aktif = true
                ")->execute();
            }

            // insert new stok awal
            ModelStok::batchInsert($tmpInsert, false);

        // case penyesuaian
        }else{
            $tmpInsert = [];

            // prepare insert stok awal
            foreach ($query as $v_q) {
                foreach ($data_sodetail as $v_dso) {
                    if ($v_dso['barang_id'] == $v_q['barang_id']){
                        $tmpInsert_temp = [];
                        $stok_now = $v_q['total_stok'];
                        $stok_fisik = !is_null($v_dso['revisi_stok']) ? $v_dso['revisi_stok'] : $v_dso['volume_fisik'];
                        $calculate = $stok_now - $stok_fisik;

                        $tmpInsert_temp['ruangan_id'] = $ruangan_id;
                        $tmpInsert_temp['stokopnamebarangdetail_id'] = $v_dso['stokopnamebarangdetail_id'];
                        $tmpInsert_temp['barang_id'] = $v_dso['barang_id'];
                        $tmpInsert_temp['tglkadaluarsa'] = $v_dso['tglkadaluarsa'];
                        $tmpInsert_temp['nobatch'] = @$v_q['nobatch'];
                        $tmpInsert_temp['stok_fisik'] = $stok_fisik;
                        $tmpInsert_temp['stok_now'] = $stok_now;
                        if ($calculate < 0){
                            $tmpInsert_temp['qtystok_in'] = abs($calculate);
                            $tmpInsert_temp['qtystok_out'] = 0;
                            $tmpInsert_temp['tglstok_in'] = date('Y-m-d H:i:s');
                        }else{
                            $tmpInsert_temp['qtystok_in'] = 0;
                            $tmpInsert_temp['qtystok_out'] = abs($calculate);
                            $tmpInsert_temp['tglstok_out'] = date('Y-m-d H:i:s');
                        }
                        $tmpInsert_temp['harganetto'] = @$v_q['harganetto'];
                        $tmpInsert_temp['stokbarang_aktif'] = TRUE;

                        $tmpInsert[] = $tmpInsert_temp;
                    }
                }
            }

            // insert new stok awal
            ModelStok::batchInsert($tmpInsert, false);
        }

        return true;
    }
}