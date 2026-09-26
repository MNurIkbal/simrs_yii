<?php

use yii\db\Migration;

/**
 * Class m210708_113917_improvment_view_infopasienoperasidetail_v
 */
class m210708_113917_improvment_view_infopasienoperasidetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."infopasienoperasidetail_v";
        ');

        $this->execute('
            CREATE VIEW "public"."infopasienoperasidetail_v" AS  
            SELECT \'NON_PAKET\'::text AS jenis,
                tindakanpelayanan_t.tindakanpelayanan_id,
                pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                tindakanpelayanan_t.tgl_tindakan,
                golonganoperasi_m.golonganoperasi_nama,
                kegiatanoperasi_m.kegiatanoperasi_nama,
                tindakanpelayanan_t.tipepaket_id,
                \'\'::character varying AS tipepaket_nama, 
                tindakanpelayanan_t.daftartindakan_id,
                daftartindakan_m.daftartindakan_nama,
                tindakanpelayanan_t.tarif_satuan,
                tindakanpelayanan_t.cyto_tindakan,
                tindakanpelayanan_t.tarifcyto_tindakan,
                tindakanpelayanan_t.tarif_tindakan,
                tindakanpelayanan_t.qty_tindakan,
                pasienmasukpenunjang_t.status_periksa,
                operasi_m.operasi_id,
                golonganoperasi_m.golonganoperasi_id,
                pasienmasukpenunjang_t.pendaftaran_id,
                pasienmasukpenunjang_t.pasien_id,
                jenisoperasi.golonganoperasi_nama AS jenis_operasi,
                pegawai_m.nama_pegawai AS nama_dokter,
                inpostoperasidetail_t.is_cyto,
                inpostoperasidetail_t.is_penyulit,
                pegawai_m.pegawai_id
               FROM (((((((((((pasienmasukpenunjang_t
                 JOIN tindakanpelayanan_t ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id)))
                 JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                 LEFT JOIN permintaankepenunjang_t ON ((tindakanpelayanan_t.tindakanpelayanan_id = permintaankepenunjang_t.tindakanpelayanan_id)))
                 LEFT JOIN operasi_m ON ((permintaankepenunjang_t.operasi_id = operasi_m.operasi_id)))
                 LEFT JOIN golonganoperasi_m ON ((operasi_m.golonganoperasi_id = golonganoperasi_m.golonganoperasi_id)))
                 LEFT JOIN kegiatanoperasi_m ON ((operasi_m.kegiatanoperasi_id = kegiatanoperasi_m.kegiatanoperasi_id)))
                 LEFT JOIN inpostoperasi_t ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = inpostoperasi_t.pasienmasukpenunjang_id)))
                 LEFT JOIN inpostoperasidetail_t ON ((inpostoperasi_t.inpostoperasi_id = inpostoperasidetail_t.inpostoperasi_id)))
                 LEFT JOIN golonganoperasi_m jenisoperasi ON ((inpostoperasidetail_t.golonganoperasi_id = jenisoperasi.golonganoperasi_id)))
                 LEFT JOIN pegawai_m ON ((inpostoperasidetail_t.dokter_id = pegawai_m.pegawai_id)))
                 JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id)))
              WHERE (ruangan_m.instalasi_id = 12)
            UNION ALL
             SELECT \'PAKET\'::text AS jenis,
                tindakanpelayanan_t.tindakanpelayanan_id,
                pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                tindakanpelayanan_t.tgl_tindakan,
                golonganoperasi_m.golonganoperasi_nama,
                kegiatanoperasi_m.kegiatanoperasi_nama,
                tindakanpelayanan_t.tipepaket_id,
                tipepaket_m.tipepaket_nama,
                paketpelayanan_mp.daftartindakan_id,
                daftartindakan_m.daftartindakan_nama,
                tindakanpelayanan_t.tarif_satuan,
                tindakanpelayanan_t.cyto_tindakan,
                tindakanpelayanan_t.tarifcyto_tindakan,
                tindakanpelayanan_t.tarif_tindakan,
                tindakanpelayanan_t.qty_tindakan,
                pasienmasukpenunjang_t.status_periksa,
                operasi_m.operasi_id,
                golonganoperasi_m.golonganoperasi_id,
                pasienmasukpenunjang_t.pendaftaran_id,
                pasienmasukpenunjang_t.pasien_id,
                jenisoperasi.golonganoperasi_nama AS jenis_operasi,
                pegawai_m.nama_pegawai AS nama_dokter,
                inpostoperasidetail_t.is_cyto,
                inpostoperasidetail_t.is_penyulit,
                pegawai_m.pegawai_id
               FROM (((((((((((((pasienmasukpenunjang_t
                 JOIN tindakanpelayanan_t ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id)))
                 JOIN tipepaket_m ON ((tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id)))
                 JOIN paketpelayanan_mp ON ((tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id)))
                 JOIN daftartindakan_m ON ((paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                 LEFT JOIN permintaankepenunjang_t ON ((tindakanpelayanan_t.tindakanpelayanan_id = permintaankepenunjang_t.tindakanpelayanan_id)))
                 LEFT JOIN operasi_m ON ((permintaankepenunjang_t.operasi_id = operasi_m.operasi_id)))
                 LEFT JOIN golonganoperasi_m ON ((operasi_m.golonganoperasi_id = golonganoperasi_m.golonganoperasi_id)))
                 LEFT JOIN kegiatanoperasi_m ON ((operasi_m.kegiatanoperasi_id = kegiatanoperasi_m.kegiatanoperasi_id)))
                 LEFT JOIN inpostoperasi_t ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = inpostoperasi_t.pasienmasukpenunjang_id)))
                 LEFT JOIN inpostoperasidetail_t ON ((inpostoperasi_t.inpostoperasi_id = inpostoperasidetail_t.inpostoperasi_id)))
                 LEFT JOIN golonganoperasi_m jenisoperasi ON ((inpostoperasidetail_t.golonganoperasi_id = jenisoperasi.golonganoperasi_id)))
                 LEFT JOIN pegawai_m ON ((inpostoperasidetail_t.dokter_id = pegawai_m.pegawai_id)))
                 JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id)))
              WHERE (ruangan_m.instalasi_id = 12);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210708_113917_improvment_view_infopasienoperasidetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210708_113917_improvment_view_infopasienoperasidetail_v cannot be reverted.\n";

        return false;
    }
    */
}
