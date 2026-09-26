<?php

use yii\db\Migration;

/**
 * Class m190416_074110_sync_pengeluaranobat_update
 */
class m190416_074110_sync_pengeluaranobat_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
   {
        $this->execute('
    DROP VIEW sync_pengeluaranobat;
        ');

        $this->execute("CREATE OR REPLACE VIEW sync_pengeluaranobat AS 
 SELECT 'OBAT'::text AS jenis_transaksi,
    'PENJUALAN_RESEP'::text AS jenis,
    obatalkespasien_t.obatalkespasien_id AS id,
    COALESCE(pendaftaran_t.no_pendaftaran, penjualanresep_t.noresep) AS no_pendaftaran,
    penjualanresep_t.noresep AS nomor,
        CASE
            WHEN penjualanresep_t.jenispenjualan::text = '344'::text THEN pasien_m.nama_pasien
            WHEN penjualanresep_t.jenispenjualan::text = '343'::text THEN penjualanresep_t.nama_pembeli
            WHEN penjualanresep_t.jenispenjualan::text = '345'::text THEN pegawai_m.nama_pegawai
            ELSE NULL::character varying
        END AS nama,
    obatalkes_m.obatalkes_nama,
    jenisobatalkes_m.jenisobatalkes_nama,
    jenisobatalkes_m.jenisobatalkes_kode,
    obatalkespasien_t.qty_oa AS qty,
    obatalkespasien_t.hargajual_oa AS harga,
    stokobatalkes_t.harganetto * obatalkespasien_t.qty_oa AS harga_netto,
    COALESCE(obatalkespasien_t.discount, 0::double precision) AS discount,
    'DITAGIHKAN'::text AS is_ditagihkan,
        CASE
            WHEN penjualanresep_t.jenispenjualan::text = '344'::text THEN concat(pendaftaran_t.no_pendaftaran, '-', pasien_m.nama_pasien, '-', obatalkes_m.obatalkes_nama, '-', jenisobatalkes_m.jenisobatalkes_nama)
            WHEN penjualanresep_t.jenispenjualan::text = '343'::text THEN concat(penjualanresep_t.noresep, '-', penjualanresep_t.nama_pembeli, '-', obatalkes_m.obatalkes_nama, '-', jenisobatalkes_m.jenisobatalkes_nama)
            WHEN penjualanresep_t.jenispenjualan::text = '345'::text THEN concat(penjualanresep_t.noresep, '-', pegawai_m.nama_pegawai, '-', obatalkes_m.obatalkes_nama, '-', jenisobatalkes_m.jenisobatalkes_nama)
            ELSE NULL::text
        END AS uraian,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    obatalkespasien_t.ruangan_id,
    ruangan_m.ruangan_nama,
    penjualanresep_t.tglpenjualan AS tgl_transaksi,
    COALESCE(pasien_m.no_rekam_medik, '0'::character varying) AS no_rekam_medik,
    obatalkespasien_t.is_jurnal,
    stokobatalkes_t.jmlppn,
    penjualanresep_t.penjualanresep_id
   FROM obatalkespasien_t
     JOIN stokobatalkes_t ON obatalkespasien_t.obatalkespasien_id = stokobatalkes_t.obatalkespasien_id
     JOIN penjualanresep_t ON obatalkespasien_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
     JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
     LEFT JOIN pendaftaran_t ON obatalkespasien_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN pegawai_m ON penjualanresep_t.karyawan_id = pegawai_m.pegawai_id
     LEFT JOIN ruangan_m ON obatalkespasien_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
  WHERE NOT (penjualanresep_t.penjualanresep_id IN ( SELECT COALESCE(syncakuntansi_r.penjualanresep_id, 0) AS \"coalesce\"
           FROM syncakuntansi_r
          WHERE syncakuntansi_r.is_sync IS TRUE))
UNION ALL
 SELECT 'OBAT'::text AS jenis_transaksi,
    'BMHP'::text AS jenis,
    obatalkespasien_t.obatalkespasien_id AS id,
    pendaftaran_t.no_pendaftaran,
    'BMHP'::character varying AS nomor,
    pasien_m.nama_pasien AS nama,
    obatalkes_m.obatalkes_nama,
    jenisobatalkes_m.jenisobatalkes_nama,
    jenisobatalkes_m.jenisobatalkes_kode,
    obatalkespasien_t.qty_oa AS qty,
    obatalkespasien_t.hargajual_oa AS harga,
    stokobatalkes_t.harganetto * obatalkespasien_t.qty_oa AS harga_netto,
    COALESCE(obatalkespasien_t.discount, 0::double precision) AS discount,
        CASE
            WHEN obatalkespasien_t.hargajual_oa = 0::double precision THEN 'TIDAK_DITAGIHKAN'::text
            ELSE 'DITAGIHKAN'::text
        END AS is_ditagihkan,
    concat(pendaftaran_t.no_pendaftaran, '-', pasien_m.nama_pasien, '-', obatalkes_m.obatalkes_nama, '-', jenisobatalkes_m.jenisobatalkes_nama) AS uraian,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    ruangan_m.instalasi_id AS ruangan_id,
    ruangan_m.ruangan_nama,
    tindakanpelayanan_t.tgl_tindakan AS tgl_transaksi,
    COALESCE(pasien_m.no_rekam_medik, '0'::character varying) AS no_rekam_medik,
    obatalkespasien_t.is_jurnal,
    stokobatalkes_t.jmlppn,
    NULL::integer AS penjualanresep_id
   FROM obatalkespasien_t
     JOIN tindakanpelayanan_t ON obatalkespasien_t.tindakanpelayanan_id = tindakanpelayanan_t.tindakanpelayanan_id
     JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN stokobatalkes_t ON obatalkespasien_t.obatalkespasien_id = stokobatalkes_t.obatalkespasien_id
     JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
     JOIN pendaftaran_t ON obatalkespasien_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN ruangan_m ON obatalkespasien_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
  WHERE NOT (obatalkespasien_t.obatalkespasien_id IN ( SELECT COALESCE(syncakuntansi_r.obatalkespasien_id, 0) AS \"coalesce\"
           FROM syncakuntansi_r
          WHERE syncakuntansi_r.is_sync IS TRUE))
UNION ALL
 SELECT 'OBAT'::text AS jenis_transaksi,
    'BMHP'::text AS jenis,
    obatalkespasien_t.obatalkespasien_id AS id,
    pendaftaran_t.no_pendaftaran,
    'BMHP'::character varying AS nomor,
    pasien_m.nama_pasien AS nama,
    obatalkes_m.obatalkes_nama,
    jenisobatalkes_m.jenisobatalkes_nama,
    jenisobatalkes_m.jenisobatalkes_kode,
    obatalkespasien_t.qty_oa AS qty,
    obatalkespasien_t.hargajual_oa AS harga,
    stokobatalkes_t.harganetto * obatalkespasien_t.qty_oa AS harga_netto,
    COALESCE(obatalkespasien_t.discount, 0::double precision) AS discount,
        CASE
            WHEN obatalkespasien_t.hargajual_oa = 0::double precision THEN 'TIDAK_DITAGIHKAN'::text
            ELSE 'DITAGIHKAN'::text
        END AS is_ditagihkan,
    concat(pendaftaran_t.no_pendaftaran, '-', pasien_m.nama_pasien, '-', obatalkes_m.obatalkes_nama, '-', jenisobatalkes_m.jenisobatalkes_nama) AS uraian,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    obatalkespasien_t.ruangan_id,
    ruangan_m.ruangan_nama,
    obatalkespasien_t.tglpelayanan AS tgl_transaksi,
    COALESCE(pasien_m.no_rekam_medik, '0'::character varying) AS no_rekam_medik,
    obatalkespasien_t.is_jurnal,
    stokobatalkes_t.jmlppn,
    NULL::integer AS penjualanresep_id
   FROM obatalkespasien_t
     JOIN stokobatalkes_t ON obatalkespasien_t.obatalkespasien_id = stokobatalkes_t.obatalkespasien_id
     JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
     JOIN pendaftaran_t ON obatalkespasien_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN ruangan_m ON obatalkespasien_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
  WHERE obatalkespasien_t.tindakanpelayanan_id IS NULL AND obatalkespasien_t.penjualanresep_id IS NULL AND NOT (obatalkespasien_t.obatalkespasien_id IN ( SELECT COALESCE(syncakuntansi_r.obatalkespasien_id, 0) AS \"coalesce\"
           FROM syncakuntansi_r
          WHERE syncakuntansi_r.is_sync IS TRUE))
UNION ALL
 SELECT 'OBAT'::text AS jenis_transaksi,
    'PEMAKAIAN_OBAT'::text AS jenis,
    pemakaianobatdetail_t.pemakaianobatdetail_id AS id,
    pemakaianobat_t.nopemakaian_obat AS no_pendaftaran,
    pemakaianobat_t.nopemakaian_obat AS nomor,
    pegawai_m.nama_pegawai AS nama,
    obatalkes_m.obatalkes_nama,
    jenisobatalkes_m.jenisobatalkes_nama,
    jenisobatalkes_m.jenisobatalkes_kode,
    pemakaianobatdetail_t.qty_satuanpakai AS qty,
    stokobatalkes_t.harganetto AS harga,
    stokobatalkes_t.harganetto AS harga_netto,
    0 AS discount,
    'DITAGIHKAN'::text AS is_ditagihkan,
    concat(pemakaianobat_t.nopemakaian_obat, '-', pegawai_m.nama_pegawai, '-', obatalkes_m.obatalkes_nama, '-', jenisobatalkes_m.jenisobatalkes_nama) AS uraian,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    pemakaianobat_t.ruangan_id,
    ruangan_m.ruangan_nama,
    pemakaianobat_t.tglpemakaianobat AS tgl_transaksi,
    NULL::character varying AS no_rekam_medik,
    false AS is_jurnal,
    stokobatalkes_t.jmlppn,
    NULL::integer AS penjualanresep_id
   FROM pemakaianobat_t
     JOIN pemakaianobatdetail_t ON pemakaianobat_t.pemakaianobat_id = pemakaianobatdetail_t.pemakaianobat_id
     JOIN stokobatalkes_t ON pemakaianobatdetail_t.pemakaianobatdetail_id = stokobatalkes_t.pemakaianobatdetail_id
     JOIN obatalkes_m ON pemakaianobatdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
     JOIN pegawai_m ON pemakaianobat_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN ruangan_m ON pemakaianobat_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
  WHERE NOT (pemakaianobatdetail_t.pemakaianobatdetail_id IN ( SELECT COALESCE(syncakuntansi_r.pemakaianobatdetail_id, 0) AS \"coalesce\"
           FROM syncakuntansi_r
          WHERE syncakuntansi_r.is_sync IS TRUE))
UNION ALL
 SELECT 'OBAT'::text AS jenis_transaksi,
    'ADJUSMEN_KELUAR'::text AS jenis,
    adjusmenobatkeluar_t.adjusmenobatkeluar_id AS id,
    adjusmenobat_t.no_adjusmen AS no_pendaftaran,
    adjusmenobat_t.no_adjusmen AS nomor,
    pegawai_m.nama_pegawai AS nama,
    obatalkes_m.obatalkes_nama,
    jenisobatalkes_m.jenisobatalkes_nama,
    jenisobatalkes_m.jenisobatalkes_kode,
    adjusmenobatkeluar_t.qty_konversi AS qty,
    stokobatalkes_t.harganetto AS harga,
    stokobatalkes_t.harganetto AS harga_netto,
    0 AS discount,
    'DITAGIHKAN'::text AS is_ditagihkan,
    concat(adjusmenobat_t.no_adjusmen, '-', pegawai_m.nama_pegawai, '-', obatalkes_m.obatalkes_nama, '-', jenisobatalkes_m.jenisobatalkes_nama) AS uraian,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    stokobatalkes_t.ruangan_id,
    ruangan_m.ruangan_nama,
    adjusmenobat_t.tgl_adjusmen AS tgl_transaksi,
    '0'::character varying AS no_rekam_medik,
    false AS is_jurnal,
    stokobatalkes_t.jmlppn,
    NULL::integer AS penjualanresep_id
   FROM adjusmenobat_t
     JOIN adjusmenobatkeluar_t ON adjusmenobat_t.adjusmenobat_id = adjusmenobatkeluar_t.adjusmenobat_id
     JOIN stokobatalkes_t ON adjusmenobatkeluar_t.adjusmenobatkeluar_id = stokobatalkes_t.adjusmenobatkeluar_id
     JOIN ruangan_m ON stokobatalkes_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN obatalkes_m ON adjusmenobatkeluar_t.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
     JOIN pegawai_m ON adjusmenobat_t.peg_menyetujui_id = pegawai_m.pegawai_id
  WHERE NOT (adjusmenobatkeluar_t.adjusmenobatkeluar_id IN ( SELECT COALESCE(syncakuntansi_r.adjusmenobatkeluar_id, 0) AS \"coalesce\"
           FROM syncakuntansi_r
          WHERE syncakuntansi_r.is_sync IS TRUE))
UNION ALL
 SELECT 'JASA_RACIK'::text AS jenis_transaksi,
    ''::text AS jenis,
    penjualanresep_t.penjualanresep_id AS id,
    COALESCE(pendaftaran_t.no_pendaftaran, penjualanresep_t.noresep) AS no_pendaftaran,
    penjualanresep_t.noresep AS nomor,
        CASE
            WHEN penjualanresep_t.jenispenjualan::text = '344'::text THEN pasien_m.nama_pasien
            WHEN penjualanresep_t.jenispenjualan::text = '343'::text THEN penjualanresep_t.nama_pembeli
            WHEN penjualanresep_t.jenispenjualan::text = '345'::text THEN pegawai_m.nama_pegawai
            ELSE NULL::character varying
        END AS nama,
    NULL::character varying AS obatalkes_nama,
    NULL::character varying AS jenisobatalkes_nama,
    'J_RACIK'::character varying AS jenisobatalkes_kode,
    0 AS qty,
    COALESCE(penjualanresep_t.totaltarifservice, 0::double precision) AS harga,
    0 AS harga_netto,
    0 AS discount,
    'DITAGIHKAN'::text AS is_ditagihkan,
        CASE
            WHEN penjualanresep_t.jenispenjualan::text = '344'::text THEN concat(pendaftaran_t.no_pendaftaran, '-', pasien_m.nama_pasien, '-Jasa Racik')
            WHEN penjualanresep_t.jenispenjualan::text = '343'::text THEN concat(penjualanresep_t.noresep, '-', penjualanresep_t.nama_pembeli, '-Jasa Racik')
            WHEN penjualanresep_t.jenispenjualan::text = '345'::text THEN concat(penjualanresep_t.noresep, '-', pegawai_m.nama_pegawai, '-Jasa Racik')
            ELSE NULL::text
        END AS uraian,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    penjualanresep_t.ruangan_id,
    ruangan_m.ruangan_nama,
    penjualanresep_t.tglresep AS tgl_transaksi,
    COALESCE(pasien_m.no_rekam_medik, '0'::character varying) AS no_rekam_medik,
    false AS is_jurnal,
    0 AS jmlppn,
    penjualanresep_t.penjualanresep_id
   FROM penjualanresep_t
     LEFT JOIN ruangan_m ON penjualanresep_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN pegawai_m ON penjualanresep_t.karyawan_id = pegawai_m.pegawai_id
     LEFT JOIN pendaftaran_t ON penjualanresep_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
  WHERE NOT (penjualanresep_t.penjualanresep_id IN ( SELECT COALESCE(syncakuntansi_r.penjualanresep_id, 0) AS \"coalesce\"
           FROM syncakuntansi_r
          WHERE syncakuntansi_r.is_sync IS TRUE))
UNION ALL
 SELECT 'JASA_ADMINISTRASI'::text AS jenis_transaksi,
    ''::text AS jenis,
    penjualanresep_t.penjualanresep_id AS id,
    COALESCE(pendaftaran_t.no_pendaftaran, penjualanresep_t.noresep) AS no_pendaftaran,
    penjualanresep_t.noresep AS nomor,
        CASE
            WHEN penjualanresep_t.jenispenjualan::text = '344'::text THEN pasien_m.nama_pasien
            WHEN penjualanresep_t.jenispenjualan::text = '343'::text THEN penjualanresep_t.nama_pembeli
            WHEN penjualanresep_t.jenispenjualan::text = '345'::text THEN pegawai_m.nama_pegawai
            ELSE NULL::character varying
        END AS nama,
    NULL::character varying AS obatalkes_nama,
    NULL::character varying AS jenisobatalkes_nama,
    'ADMINISTRASI'::character varying AS jenisobatalkes_kode,
    0 AS qty,
    COALESCE(penjualanresep_t.biayaadministrasi, 0::double precision) AS harga,
    0 AS harga_netto,
    0 AS discount,
    'DITAGIHKAN'::text AS is_ditagihkan,
        CASE
            WHEN penjualanresep_t.jenispenjualan::text = '344'::text THEN concat(pendaftaran_t.no_pendaftaran, '-', pasien_m.nama_pasien, '-Jasa Administrasi')
            WHEN penjualanresep_t.jenispenjualan::text = '343'::text THEN concat(penjualanresep_t.noresep, '-', penjualanresep_t.nama_pembeli, '-Jasa Administrasi')
            WHEN penjualanresep_t.jenispenjualan::text = '345'::text THEN concat(penjualanresep_t.noresep, '-', pegawai_m.nama_pegawai, '-Jasa Administrasi')
            ELSE NULL::text
        END AS uraian,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    penjualanresep_t.ruangan_id,
    ruangan_m.ruangan_nama,
    penjualanresep_t.tglresep AS tgl_transaksi,
    COALESCE(pasien_m.no_rekam_medik, '0'::character varying) AS no_rekam_medik,
    false AS is_jurnal,
    0 AS jmlppn,
    penjualanresep_t.penjualanresep_id
   FROM penjualanresep_t
     LEFT JOIN ruangan_m ON penjualanresep_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN pegawai_m ON penjualanresep_t.karyawan_id = pegawai_m.pegawai_id
     LEFT JOIN pendaftaran_t ON penjualanresep_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
  WHERE NOT (penjualanresep_t.penjualanresep_id IN ( SELECT COALESCE(syncakuntansi_r.penjualanresep_id, 0) AS \"coalesce\"
           FROM syncakuntansi_r
          WHERE syncakuntansi_r.is_sync IS TRUE))
UNION ALL
 SELECT 'PEMBULATAN'::text AS jenis_transaksi,
    ''::text AS jenis,
    penjualanresep_t.penjualanresep_id AS id,
    COALESCE(pendaftaran_t.no_pendaftaran, penjualanresep_t.noresep) AS no_pendaftaran,
    penjualanresep_t.noresep AS nomor,
        CASE
            WHEN penjualanresep_t.jenispenjualan::text = '344'::text THEN pasien_m.nama_pasien
            WHEN penjualanresep_t.jenispenjualan::text = '343'::text THEN penjualanresep_t.nama_pembeli
            WHEN penjualanresep_t.jenispenjualan::text = '345'::text THEN pegawai_m.nama_pegawai
            ELSE NULL::character varying
        END AS nama,
    NULL::character varying AS obatalkes_nama,
    NULL::character varying AS jenisobatalkes_nama,
    'PEMBULATAN'::character varying AS jenisobatalkes_kode,
    0 AS qty,
    COALESCE(penjualanresep_t.pembulatanharga, 0::double precision) AS harga,
    0 AS harga_netto,
    0 AS discount,
    'DITAGIHKAN'::text AS is_ditagihkan,
        CASE
            WHEN penjualanresep_t.jenispenjualan::text = '344'::text THEN concat(pendaftaran_t.no_pendaftaran, '-', pasien_m.nama_pasien, '-Pembulatan')
            WHEN penjualanresep_t.jenispenjualan::text = '343'::text THEN concat(penjualanresep_t.noresep, '-', penjualanresep_t.nama_pembeli, '-Pembulatan')
            WHEN penjualanresep_t.jenispenjualan::text = '345'::text THEN concat(penjualanresep_t.noresep, '-', pegawai_m.nama_pegawai, '-Pembulatan')
            ELSE NULL::text
        END AS uraian,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    penjualanresep_t.ruangan_id,
    ruangan_m.ruangan_nama,
    penjualanresep_t.tglresep AS tgl_transaksi,
    COALESCE(pasien_m.no_rekam_medik, '0'::character varying) AS no_rekam_medik,
    false AS is_jurnal,
    0 AS jmlppn,
    penjualanresep_t.penjualanresep_id
   FROM penjualanresep_t
     LEFT JOIN ruangan_m ON penjualanresep_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN pegawai_m ON penjualanresep_t.karyawan_id = pegawai_m.pegawai_id
     LEFT JOIN pendaftaran_t ON penjualanresep_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
  WHERE NOT (penjualanresep_t.penjualanresep_id IN ( SELECT COALESCE(syncakuntansi_r.penjualanresep_id, 0) AS \"coalesce\"
           FROM syncakuntansi_r
          WHERE syncakuntansi_r.is_sync IS TRUE));
               ");
        
        $this->execute('
   ALTER TABLE sync_pengeluaranobat
  OWNER TO postgres;

        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190416_074110_sync_pengeluaranobat_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190416_074110_sync_pengeluaranobat_update cannot be reverted.\n";

        return false;
    }
    */
}
