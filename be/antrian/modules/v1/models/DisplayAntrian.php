<?php

namespace app\modules\v1\models;
use Yii;
use Doco\components\DocoConstants;

class DisplayAntrian 
{
    public function getRuangan($ruangan)
    {
        $connection = Yii::$app->db;
        $sql = "SELECT * FROM pegawai_v WHERE ruangan_id IN ({$ruangan}) AND kelompokpegawai_id =1 AND is_deleted = false";
        $data = $connection->createCommand($sql)->queryAll();
        $arr = [];
        foreach ($data as $key => $value) {
            $arr[] = [
                'pegawai_id' => $value['pegawai_id'] . '-' . $value['ruangan_id'],
                'nama_pegawai' => $value['nama_pegawai'] . ' (' . $value['ruangan_nama'] . ')',
            ];
        }
        return $arr;
    }

    public function getRuanganByLayar($id)
    {
        $connection = Yii::$app->db;
        $sql = "SELECT l.*, r.ruangan_nama, p.nama
            FROM layarantriandetail_m l
            JOIN ruangan_m r ON r.ruangan_id = l.ruangan_id
            join pegawai_v p on l.pegawai_id = p.pegawai_id and r.instalasi_id = p.instalasi_id and r.ruangan_id = p.ruangan_id
            WHERE l.layarantrian_id = ({$id}) AND l.is_deleted = false";
        $data = $connection->createCommand($sql)->queryAll();
        $arr = [];
        foreach ($data as $key => $value) {
            $arr[] = [
                'ruangan_id' => $value['ruangan_id'],
                'ruangan_nama' => $value['ruangan_nama'],
                'pegawai_id' => $value['pegawai_id'],
                'pegawai_nama' => $value['nama'],
            ];
        }
        return $arr;
    }

    public function getLoketByJenis($id)
    {
        $connection = Yii::$app->db;
        $sql = "SELECT loket_id, loket_nama, loket_fungsi, fungsi_antrian, kode_antrian  FROM infoloket_v
            WHERE jenisantrian_id = {$id} AND fungsi_antrian is not null
        ";
        $data = $connection->createCommand($sql)->queryAll();
        $arr = $group = [];
        foreach ($data as $key => $value) {
            $group[$value['kode_antrian']][$value['loket_nama']][] = $value;

        }
        
        foreach ($group as $kode_antrian => $value) {
            foreach ($value as $loket => $val) {
                $id = $fungsi = [];
                foreach ($val as $key => $v) {
                    $id[] = $v['loket_id'];
                    $fungsi[] = $v['fungsi_antrian'];
                }
                $arr[] = [
                    'loket_id' => json_encode($id),
                    'loket_nama' => 'Loket ' . $loket . ' - ' . $kode_antrian . ' (' . implode(', ', $fungsi) . ')',
                ];
            }
        }
        
        return $arr;
    }

    public function getLoketByLayar($id)
    {
        $connection = Yii::$app->db;
        $sql = "SELECT *
            FROM layarantriandetail_m
            WHERE layarantrian_id = {$id} AND is_deleted = false";
        $data = $connection->createCommand($sql)->queryAll();
        $arr = [];
        foreach ($data as $key => $value) {
            $arr[] = [
                'loket_id' => $value['loket_id']
            ];
        }
        return $arr;
    }

    public function getLoket($id)
    {
        $connection = Yii::$app->db;        
        $sql = "SELECT 
                    l.loket_id, k.kode_antrian, l.loket_nama, l.loket_namalain,
                    concat(group_carabayar,' - ',klasifikasipasien_nama,' (',kode_antrian,')') as jenis
                FROM 
                    loket_m l
                    LEFT JOIN loket_mp lm ON l.loket_id = lm.loket_id
                    LEFT JOIN konfigantrian_v k ON k.konfigantrian_id = lm.konfigantrian_id
                    --JOIN lookup_m lo ON lo.lookup_id = k.fungsiantrian_id
                    where l.jenisantrian_id = {$id}
                    AND l.is_deleted = false AND l.is_active = true";
        $data = $connection->createCommand($sql)->queryAll();
        $arr = $group = $res = [];
        foreach ($data as $key => $value) {
            if (!isset($arr[$value['loket_id']])) {
                $arr[$value['loket_id']] = $value['loket_namalain'] . ' - ' . $value['kode_antrian'] . ' ';
                $group[$value['loket_id']] = [];
            }

            if ($id == DocoConstants::VAR_JA_F) {
                $arr[$value['loket_id']] = $value['loket_namalain'];
            } else {
                $arr[$value['loket_id']] = $value['loket_namalain'] . ' - ' . $value['kode_antrian'] . ' ';
            }
            // if (!in_array($value['lookup_name'], $group[$value['loket_id']])) {
            //     $group[$value['loket_id']][] = $value['lookup_name'];
            // }
            // $arr[$value['loket_id']] .= '(' . implode(', ', $group[$value['loket_id']]) . ')';
            $res[$value['loket_id']] = [
                'loket_id' => $value['loket_id'],
                'loket_nama' => $arr[$value['loket_id']]
            ];
        }
        return $res;
    }
 
}