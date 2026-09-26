<?php
namespace app\modules\v1\businessLogic;

/**
** @author yaya
**/

use Yii;
use app\modules\v1\models\StokObatAlkes as ModelStok;

class StokObatAlkes
{
    const FEFO = 'FEFO';
    const FIFO = 'FIFO';
    public static $distribusi = true;
    
    public static function methodeFEFO($data = [],$tanggalPemakaian, $mutasi = false)
    {
        // Skip ketika tidak di temukan datanya
        if (!count($data)) return true;
        $request = Yii::$app->request;
        $jwtRuangan = Yii::$app->jwt->ruangan_id;
        $ruangan_id = isset($_POST['ruangan_id']) ? $_POST['ruangan_id'] : $jwtRuangan;
        $listIdDetail = $idObatAlkes = [];
        foreach ($data as $val) {
            $idObat = $val['obatalkes_id'];
            $listIdDetail[$idObat] = $val;
            $idObatAlkes[] = $idObat;
        }
        $inCondition = "(" . implode(",", $idObatAlkes) . ")";

        $str = "
        SELECT (CASE 
                    WHEN prmy.stokobatalkesasal_id IS NULL 
                        THEN prmy.stokobatalkes_id 
                    ELSE prmy.stokobatalkesasal_id END) as id_stok,
            prmy.obatalkes_id,
            prmy.qtystok_in,
            prmy.qtystok_out,
            (CASE 
                    WHEN prmy.stokobatalkesasal_id IS NULL 
                        THEN prmy.nobatch
                    ELSE tmp.nobatch END) as nobatch,
            (CASE 
                    WHEN prmy.stokobatalkesasal_id IS NULL 
                        THEN prmy.harganetto
                    ELSE tmp.harganetto END) as harganetto,
            (CASE 
                    WHEN prmy.stokobatalkesasal_id IS NULL 
                        THEN prmy.persendiscount
                    ELSE tmp.persendiscount END) as persendiscount,
            (CASE 
                    WHEN prmy.stokobatalkesasal_id IS NULL 
                        THEN prmy.jmldiscount
                    ELSE tmp.jmldiscount END) as jmldiscount,
            (CASE 
                    WHEN prmy.stokobatalkesasal_id IS NULL 
                        THEN prmy.persenppn
                    ELSE tmp.persenppn END) as persenppn,
            (CASE 
                    WHEN prmy.stokobatalkesasal_id IS NULL 
                        THEN prmy.persenpph
                    ELSE tmp.persenpph END) as persenpph,
            (CASE 
                    WHEN prmy.stokobatalkesasal_id IS NULL 
                        THEN prmy.persenmargin
                    ELSE tmp.persenmargin END) as persenmargin,
            (CASE 
                    WHEN prmy.stokobatalkesasal_id IS NULL 
                        THEN prmy.jmlmargin
                    ELSE tmp.jmlmargin END) as jmlmargin,
            (CASE 
                    WHEN prmy.stokobatalkesasal_id IS NULL 
                        THEN prmy.jmlppn
                    ELSE tmp.jmlppn END) as jmlppn,
            prmy.tglkadaluarsa 
            FROM stokobatalkes_t prmy
            LEFT JOIN stokobatalkes_t tmp ON tmp.stokobatalkes_id = prmy.stokobatalkesasal_id
            WHERE prmy.obatalkes_id IN {$inCondition} AND prmy.ruangan_id = {$ruangan_id}
            AND prmy.stokoa_aktif = true";

        if(self::$distribusi == false){
            $str = "SELECT (CASE WHEN stokobatalkesasal_id IS NULL THEN stokobatalkes_id ELSE stokobatalkesasal_id END) as id_stok,
            obatalkes_id,
            qtystok_in,
            qtystok_out, 
            nobatch,
            null::TEXT as harganetto,
            null::TEXT as persendiscount,
            null::TEXT as jmldiscount,
            null::TEXT as persenppn,
            null::TEXT as persenpph,
            null::TEXT as persenmargin,
            null::TEXT as jmlmargin,
            null::TEXT as jmlppn,
            tglkadaluarsa 
                            FROM stokobatalkes_t
            WHERE obatalkes_id IN {$inCondition} AND ruangan_id = {$ruangan_id}
            AND stokoa_aktif = true";
        }

        $query = Yii::$app->db->createCommand("
            SELECT obatalkes_id,id_stok,
            SUM(dadang.qtystok_in - dadang.qtystok_out) as total_stok,
            dadang.tglkadaluarsa,nobatch,harganetto,persendiscount,jmldiscount,persenppn,persenpph,persenmargin,jmlmargin,jmlppn
            FROM (
                {$str}
            ) as dadang
            GROUP BY dadang.id_stok,dadang.tglkadaluarsa,obatalkes_id,nobatch,
            harganetto,persendiscount,jmldiscount,persenppn,persenpph,persenmargin,jmlmargin,jmlppn
            ORDER BY dadang.tglkadaluarsa ASC
        ")->queryAll();

        $tmpInsert = $listOfOutStock = [];
        // Kuncinya qty_satuanpakai selalu berkurang
        foreach ($query as $detail) {
            $idObat = $detail['obatalkes_id'];
            if (isset($listIdDetail[$idObat])) {
                // ini stok permintaan yang di inputkan
                // contoh permintaan 250 sedangkan stok detail nya 100
                $stokItem = $listIdDetail[$idObat]['qty_satuanpakai'];
                // Lewati ketika stok sudah 0 
                if ($stokItem == 0) continue;
                $totalStok = $detail['total_stok'];
                $idTerima = isset($listIdDetail[$idObat]['terimamutasidetail_id']) 
                                            ? $listIdDetail[$idObat]['terimamutasidetail_id'] : null;
                if ($stokItem > $totalStok) {
                    $listOfOutStock[] = $detail['id_stok'];
                    // ini akan membuat row baru dengan catatan stok harus di update false
                    $row = [
                        'ruangan_id' => $ruangan_id,
                        'obatalkes_id' => $idObat,
                        'tglkadaluarsa' => $detail['tglkadaluarsa'],
                        'nobatch' => $detail['nobatch'],
                        'harganetto' => isset($listIdDetail[$idObat]['harganetto']) ? $listIdDetail[$idObat]['harganetto'] : $detail['harganetto'],
                        'persendiscount' => isset($listIdDetail[$idObat]['persendiscount']) ? $listIdDetail[$idObat]['persendiscount'] : $detail['persendiscount'],
                        'jmldiscount' => isset($listIdDetail[$idObat]['jmldiscount']) ? $listIdDetail[$idObat]['jmldiscount'] : $detail['jmldiscount'],
                        'persenppn' => isset($listIdDetail[$idObat]['persenppn']) ? $listIdDetail[$idObat]['persenppn'] : $detail['persenppn'],
                        'persenpph' => isset($listIdDetail[$idObat]['persenpph']) ? $listIdDetail[$idObat]['persenpph'] : $detail['persenpph'],
                        'persenmargin' => isset($listIdDetail[$idObat]['persenmargin']) ? $listIdDetail[$idObat]['persenmargin'] : $detail['persenmargin'],
                        'jmlmargin' => isset($listIdDetail[$idObat]['jmlmargin']) ? $listIdDetail[$idObat]['jmlmargin'] : $detail['jmlmargin'],
                        'jmlppn' => isset($listIdDetail[$idObat]['jmlppn']) ? $listIdDetail[$idObat]['jmlppn'] : $detail['jmlppn'],
                        'obatalkespasien_id' => isset($listIdDetail[$idObat]['obatalkespasien_id']) ? $listIdDetail[$idObat]['obatalkespasien_id'] : null,
                        'qtystok_in' => 0,
                        'tglstok_out' => date('Y-m-d H:i:s',strtotime($tanggalPemakaian)),
                        'qtystok_out' => $totalStok,
                        'stokobatalkesasal_id' => $detail['id_stok'],
                        'satuankecil_id' => isset($listIdDetail[$idObat]['satuankecil_id']) 
                                                ? $listIdDetail[$idObat]['satuankecil_id'] : null,
                        'pemakaianobatdetail_id' => isset($listIdDetail[$idObat]['pemakaianobatdetail_id']) 
                                                ? $listIdDetail[$idObat]['pemakaianobatdetail_id'] : null,
                        'mutasiobatdetail_id' => isset($listIdDetail[$idObat]['mutasiobatdetail_id']) 
                                                ? $listIdDetail[$idObat]['mutasiobatdetail_id'] : null,
                        'stokoa_aktif' => true,
                        'is_active' => true
                    ];
                    $tmpInsert[] = $row;
                    if ($mutasi) {
                        $tmpInsert[] = self::generateMutasi($row, $idTerima);
                    }
                    $listIdDetail[$idObat]['qty_satuanpakai'] = $stokItem - $totalStok;
                } else {
                    if ($listIdDetail[$idObat]['qty_satuanpakai'] == $totalStok) {
                        $listOfOutStock[] = $detail['id_stok'];
                    }
                    $row = [
                        'ruangan_id' => $ruangan_id,
                        'obatalkes_id' => $idObat,
                        'tglkadaluarsa' => $detail['tglkadaluarsa'],
                        'nobatch' => $detail['nobatch'],
                        'harganetto' => isset($listIdDetail[$idObat]['harganetto']) ? $listIdDetail[$idObat]['harganetto'] : $detail['harganetto'],
                        'persendiscount' => isset($listIdDetail[$idObat]['persendiscount']) ? $listIdDetail[$idObat]['persendiscount'] : $detail['persendiscount'],
                        'jmldiscount' => isset($listIdDetail[$idObat]['jmldiscount']) ? $listIdDetail[$idObat]['jmldiscount'] : $detail['jmldiscount'],
                        'persenppn' => isset($listIdDetail[$idObat]['persenppn']) ? $listIdDetail[$idObat]['persenppn'] : $detail['persenppn'],
                        'persenpph' => isset($listIdDetail[$idObat]['persenpph']) ? $listIdDetail[$idObat]['persenpph'] : $detail['persenpph'],
                        'persenmargin' => isset($listIdDetail[$idObat]['persenmargin']) ? $listIdDetail[$idObat]['persenmargin'] : $detail['persenmargin'],
                        'jmlmargin' => isset($listIdDetail[$idObat]['jmlmargin']) ? $listIdDetail[$idObat]['jmlmargin'] : $detail['jmlmargin'],
                        'jmlppn' => isset($listIdDetail[$idObat]['jmlppn']) ? $listIdDetail[$idObat]['jmlppn'] : $detail['jmlppn'],
                        'obatalkespasien_id' => isset($listIdDetail[$idObat]['obatalkespasien_id']) ? $listIdDetail[$idObat]['obatalkespasien_id'] : null,
                        'qtystok_in' => 0,
                        'tglstok_out' => date('Y-m-d H:i:s',strtotime($tanggalPemakaian)),
                        'qtystok_out' => $listIdDetail[$idObat]['qty_satuanpakai'],
                        'stokobatalkesasal_id' => $detail['id_stok'],
                        'satuankecil_id' => isset($listIdDetail[$idObat]['satuankecil_id']) 
                                                ? $listIdDetail[$idObat]['satuankecil_id'] : null,
                        'pemakaianobatdetail_id' => isset($listIdDetail[$idObat]['pemakaianobatdetail_id']) 
                                                ? $listIdDetail[$idObat]['pemakaianobatdetail_id'] : null,
                        'mutasiobatdetail_id' => isset($listIdDetail[$idObat]['mutasiobatdetail_id']) 
                                                ? $listIdDetail[$idObat]['mutasiobatdetail_id'] : null,
                        'stokoa_aktif' => true,
                        'is_active' => true
                    ];
                    $tmpInsert[] = $row;
                    if ($mutasi) {
                        $tmpInsert[] = self::generateMutasi($row, $idTerima);
                    }
                    $listIdDetail[$idObat]['qty_satuanpakai'] = 0;
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
                UPDATE stokobatalkes_t SET stokoa_aktif = false, is_active = false 
                WHERE (stokobatalkesasal_id IN {$inCondition} OR stokobatalkes_id IN {$inCondition}) 
                AND stokoa_aktif = true
            ")->execute();
        }
        return true;
    }

    public static function methodeFIFO($data = [],$tanggalPemakaian, $mutasi = false)
    {
        // Skip ketika tidak di temukan datanya
        if (!count($data)) return true;
        $request = Yii::$app->request;
        $jwtRuangan = Yii::$app->jwt->ruangan_id;
        $ruangan_id = isset($_POST['ruangan_id']) ? $_POST['ruangan_id'] : $jwtRuangan;
        $listIdDetail = $idObatAlkes = [];
        foreach ($data as $val) {
            $idObat = $val['obatalkes_id'];
            $listIdDetail[$idObat] = $val;
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
            $str ="SELECT (CASE WHEN t.stokobatalkesasal_id IS NULL THEN t.stokobatalkes_id ELSE t.stokobatalkesasal_id END) as id_stok,
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
            t.tglkadaluarsa FROM stokobatalkes_t t
            LEFT JOIN stokobatalkes_t child ON t.stokobatalkesasal_id = child.stokobatalkes_id
            WHERE t.obatalkes_id IN {$inCondition} AND t.ruangan_id = {$ruangan_id}
            AND t.stokoa_aktif = true";
        }
        $query = Yii::$app->db->createCommand("
            SELECT obatalkes_id,id_stok,
            SUM(dadang.qtystok_in - dadang.qtystok_out) as total_stok,
            dadang.tglkadaluarsa,nobatch,harganetto,persendiscount,jmldiscount,persenppn,persenpph,persenmargin,jmlmargin,tglstok_in,jmlppn
            FROM (
                {$str}
            ) as dadang
            GROUP BY dadang.id_stok,dadang.tglkadaluarsa,obatalkes_id,nobatch,
            harganetto,persendiscount,jmldiscount,persenppn,persenpph,persenmargin,jmlmargin,tglstok_in,jmlppn
            ORDER BY dadang.tglstok_in ASC
        ")->queryAll();

        $tmpInsert = $listOfOutStock = [];
        // Kuncinya qty_satuanpakai selalu berkurang
        foreach ($query as $detail) {
            $idObat = $detail['obatalkes_id'];
            if (isset($listIdDetail[$idObat])) {
                // ini stok permintaan yang di inputkan
                // contoh permintaan 250 sedangkan stok detail nya 100
                $stokItem = $listIdDetail[$idObat]['qty_satuanpakai'];
                // Lewati ketika stok sudah 0 
                if ($stokItem == 0) continue;
                $totalStok = $detail['total_stok'];
                $idTerima = isset($listIdDetail[$idObat]['terimamutasidetail_id']) 
                                            ? $listIdDetail[$idObat]['terimamutasidetail_id'] : null;
                if ($stokItem > $totalStok) {
                    $listOfOutStock[] = $detail['id_stok'];
                    // ini akan membuat row baru dengan catatan stok harus di update false
                    $row = [
                        'ruangan_id' => $ruangan_id,
                        'obatalkes_id' => $idObat,
                        'tglkadaluarsa' => $detail['tglkadaluarsa'],
                        'nobatch' => $detail['nobatch'],
                        'harganetto' => isset($listIdDetail[$idObat]['harganetto']) ? $listIdDetail[$idObat]['harganetto'] : $detail['harganetto'],
                        'persendiscount' => isset($listIdDetail[$idObat]['persendiscount']) ? $listIdDetail[$idObat]['persendiscount'] : $detail['persendiscount'],
                        'jmldiscount' => isset($listIdDetail[$idObat]['jmldiscount']) ? $listIdDetail[$idObat]['jmldiscount'] : $detail['jmldiscount'],
                        'persenppn' => isset($listIdDetail[$idObat]['persenppn']) ? $listIdDetail[$idObat]['persenppn'] : $detail['persenppn'],
                        'persenpph' => isset($listIdDetail[$idObat]['persenpph']) ? $listIdDetail[$idObat]['persenpph'] : $detail['persenpph'],
                        'persenmargin' => isset($listIdDetail[$idObat]['persenmargin']) ? $listIdDetail[$idObat]['persenmargin'] : $detail['persenmargin'],
                        'jmlmargin' => isset($listIdDetail[$idObat]['jmlmargin']) ? $listIdDetail[$idObat]['jmlmargin'] : $detail['jmlmargin'],
                        'jmlppn' => isset($listIdDetail[$idObat]['jmlppn']) ? $listIdDetail[$idObat]['jmlppn'] : $detail['jmlppn'],
                        'obatalkespasien_id' => isset($listIdDetail[$idObat]['obatalkespasien_id']) ? $listIdDetail[$idObat]['obatalkespasien_id'] : null,
                        'qtystok_in' => 0,
                        'tglstok_out' => date('Y-m-d H:i:s',strtotime($tanggalPemakaian)),
                        'qtystok_out' => $totalStok,
                        'stokobatalkesasal_id' => $detail['id_stok'],
                        'satuankecil_id' => isset($listIdDetail[$idObat]['satuankecil_id']) 
                                                ? $listIdDetail[$idObat]['satuankecil_id'] : null,
                        'pemakaianobatdetail_id' => isset($listIdDetail[$idObat]['pemakaianobatdetail_id']) 
                                                ? $listIdDetail[$idObat]['pemakaianobatdetail_id'] : null,
                        'mutasiobatdetail_id' => isset($listIdDetail[$idObat]['mutasiobatdetail_id']) 
                                                ? $listIdDetail[$idObat]['mutasiobatdetail_id'] : null,
                        'stokoa_aktif' => false,
                        'is_active' => false
                    ];
                    $tmpInsert[] = $row;
                    if ($mutasi) {
                        $tmpInsert[] = self::generateMutasi($row, $idTerima);
                    }
                    $listIdDetail[$idObat]['qty_satuanpakai'] = $stokItem - $totalStok;
                } else {
                    if ($listIdDetail[$idObat]['qty_satuanpakai'] == $totalStok) {
                        $listOfOutStock[] = $detail['id_stok'];
                    }
                    $row = [
                        'ruangan_id' => $ruangan_id,
                        'obatalkes_id' => $idObat,
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
                        'qtystok_out' => $listIdDetail[$idObat]['qty_satuanpakai'],
                        'stokobatalkesasal_id' => $detail['id_stok'],
                        'satuankecil_id' => isset($listIdDetail[$idObat]['satuankecil_id']) 
                                                ? $listIdDetail[$idObat]['satuankecil_id'] : null,
                        'pemakaianobatdetail_id' => isset($listIdDetail[$idObat]['pemakaianobatdetail_id']) 
                                                ? $listIdDetail[$idObat]['pemakaianobatdetail_id'] : null,
                        'mutasiobatdetail_id' => isset($listIdDetail[$idObat]['mutasiobatdetail_id']) 
                                                ? $listIdDetail[$idObat]['mutasiobatdetail_id'] : null,
                        'stokoa_aktif' => true,
                        'is_active' => true
                    ];
                    $tmpInsert[] = $row;
                    if ($mutasi) {
                        $tmpInsert[] = self::generateMutasi($row, $idTerima);
                    }
                    $listIdDetail[$idObat]['qty_satuanpakai'] = 0;
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
                UPDATE stokobatalkes_t SET stokoa_aktif = false, is_active = false
                WHERE (stokobatalkesasal_id IN {$inCondition} OR stokobatalkes_id IN {$inCondition}) 
                AND stokoa_aktif = true
            ")->execute();
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
        $row['qtystok_in'] = $row['qtystok_out'];
        $row['ruangan_id'] = $_POST['ruangan_penerima_id'];
        $row['tglstok_in'] = $row['tglstok_out'];
        $row['qtystok_out'] = 0;
        $row['terimamutasidetail_id'] = $idTerima;
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
        $inCondition = "(" . implode(",", $idObatAlkes) . ")";
        $query = Yii::$app->db->createCommand("
            SELECT
                id_stok,
                obatalkes_id,
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
                        tglkadaluarsa,
                        qtystok_in,
                        qtystok_out,
                        nobatch
                    FROM
                        stokobatalkes_t
                    WHERE
                        ruangan_id = {$ruangan_id}
                    AND stokoa_aktif = TRUE
                ) AS dadang
            GROUP BY
                dadang.id_stok,
                tglkadaluarsa,
                obatalkes_id,
                nobatch
            ORDER BY
                obatalkes_id ASC
        ")->queryAll();

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

            // insert new stok awal
            ModelStok::batchInsert($tmpInsert, false);

        // case penyesuaian
        }else{
            $tmpInsert = [];
            $dataObat = [];
            // prepare insert stok awal
            foreach ($query as $detail) {
                $dataObat[$detail['obatalkes_id'].'-'.$detail['tglkadaluarsa']][] = $detail;
            }
            // return $data_sodetail;
            foreach ($data_sodetail as $v_dso) {
                $primary = $v_dso['obatalkes_id'].'-'.$v_dso['tglkadaluarsa'];
                $calculate = $v_dso['volume_fisik'] - $v_dso['volume_sistem'];
                // isi 9
                $qty_fisik = $calculate;
                if($calculate != 0){
                    if($qty_fisik > 0){
                        $tmpInsert_temp['ruangan_id'] = $ruangan_id;
                        $tmpInsert_temp['stokopnamedetail_id'] = $v_dso['stokopnamedetail_id'];
                        $tmpInsert_temp['obatalkes_id'] = $v_dso['obatalkes_id'];
                        $tmpInsert_temp['tglkadaluarsa'] = $v_dso['tglkadaluarsa'];
                        $tmpInsert_temp['nobatch'] = @$dataObat[$primary]['nobatch'];
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
                            $tmpInsert_temp['nobatch'] = @$dataObat[$primary]['nobatch'];
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
                }else{
                    $tmpInsert_temp['ruangan_id'] = $ruangan_id;
                    $tmpInsert_temp['stokopnamedetail_id'] = $v_dso['stokopnamedetail_id'];
                    $tmpInsert_temp['obatalkes_id'] = $v_dso['obatalkes_id'];
                    $tmpInsert_temp['tglkadaluarsa'] = $v_dso['tglkadaluarsa'];
                    $tmpInsert_temp['nobatch'] = @$dataObat[$primary]['nobatch'];
                    $tmpInsert_temp['qtystok_in'] = 0;
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
                }
            }
            // insert new stok awal
            ModelStok::batchInsert($tmpInsert, false);
        }

        return true;
    }
}