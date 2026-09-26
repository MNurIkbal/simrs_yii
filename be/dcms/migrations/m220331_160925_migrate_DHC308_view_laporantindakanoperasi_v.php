<?php

use yii\db\Migration;

/**
 * Class m220331_160925_migrate_DHC308_view_laporantindakanoperasi_v
 */
class m220331_160925_migrate_DHC308_view_laporantindakanoperasi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."laporantindakanoperasi_v";
        ');

        $this->execute('
            CREATE VIEW "public"."laporantindakanoperasi_v" AS  SELECT daftartindakan_m.daftartindakan_id AS tindakan_operasi_id,
                daftartindakan_m.daftartindakan_nama AS tindakan_operasi,
                sum(verifikasibedah_r.qty) AS qty,
                verifikasibedah_r.kegiatanoperasi_id, 
                COALESCE(kegiatanoperasi_m.kegiatanoperasi_nama, verifikasibedah_r.kegiatanoperasi_nama) AS kegiatan_operasi,
                verifikasibedah_r.golonganoperasi_id,
                COALESCE(golonganoperasi_m.golonganoperasi_nama, verifikasibedah_r.golonganoperasi_nama) AS golongan_operasi,
                pegawai_m.nama_pegawai AS dokter_operator,
                pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                inpostoperasi_t.created_date AS tgl_operasi
               FROM (((((((verifikasibedah_r
                 JOIN pasienmasukpenunjang_t ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = verifikasibedah_r.pasienmasukpenunjang_id)))
                 JOIN daftartindakan_m ON ((daftartindakan_m.daftartindakan_id = verifikasibedah_r.daftartindakan_id)))
                 JOIN timoperasi_t ON (((pasienmasukpenunjang_t.pasienmasukpenunjang_id = timoperasi_t.pasienmasukpenunjang_id) AND (verifikasibedah_r.dokter_id = timoperasi_t.pegawai_id) AND (verifikasibedah_r.daftartindakan_id = timoperasi_t.daftartindakan_id))))
                 JOIN pegawai_m ON ((pegawai_m.pegawai_id = timoperasi_t.pegawai_id)))
                 JOIN inpostoperasi_t ON ((inpostoperasi_t.pasienmasukpenunjang_id = timoperasi_t.pasienmasukpenunjang_id)))
                 LEFT JOIN kegiatanoperasi_m ON ((kegiatanoperasi_m.kegiatanoperasi_id = verifikasibedah_r.kegiatanoperasi_id)))
                 LEFT JOIN golonganoperasi_m ON ((golonganoperasi_m.golonganoperasi_id = verifikasibedah_r.golonganoperasi_id)))
              WHERE ((timoperasi_t.posisi_tim = 508) AND (timoperasi_t.is_deleted = false) AND ((pasienmasukpenunjang_t.status_periksa)::integer = 483) AND (verifikasibedah_r.useprice = true) AND (verifikasibedah_r.is_deleted = false) AND ((COALESCE(kegiatanoperasi_m.kegiatanoperasi_nama, verifikasibedah_r.kegiatanoperasi_nama))::text <> \'-\'::text) AND ((COALESCE(golonganoperasi_m.golonganoperasi_nama, verifikasibedah_r.golonganoperasi_nama))::text <> \'-\'::text))
              GROUP BY daftartindakan_m.daftartindakan_id, daftartindakan_m.daftartindakan_nama, verifikasibedah_r.kegiatanoperasi_id, kegiatanoperasi_m.kegiatanoperasi_nama, verifikasibedah_r.kegiatanoperasi_nama, verifikasibedah_r.golonganoperasi_id, golonganoperasi_m.golonganoperasi_nama, verifikasibedah_r.golonganoperasi_nama, pegawai_m.nama_pegawai, pasienmasukpenunjang_t.pasienmasukpenunjang_id, inpostoperasi_t.created_date;

        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220331_160925_migrate_DHC308_view_laporantindakanoperasi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220331_160925_migrate_DHC308_view_laporantindakanoperasi_v cannot be reverted.\n";

        return false;
    }
    */
}
