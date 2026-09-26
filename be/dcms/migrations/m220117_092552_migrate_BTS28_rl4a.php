<?php

use yii\db\Migration;

/**
 * Class m220117_092552_migrate_BTS28_rl4a
 */
class m220117_092552_migrate_BTS28_rl4a extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.rl4_b_morbiditasrawatjalandetail_v;');
        $this->execute("
            CREATE VIEW \"public\".\"rl4_b_morbiditasrawatjalandetail_v\" AS
            SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.pasien_id,
            pasien_m.nama_pasien,
            pendaftaran_t.golonganumur_id,
            golonganumur_m.golonganumur_namalainnya,
            pasien_m.jeniskelamin,
            fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
            koreksidiagnosa_t.diagnosa_id,
            diagnosa_m.diagnosa_nama,
            pasienpulang_t.carakeluar_id,
            carakeluar_m.carakeluar_nama,
            koreksidiagnosa_t.tgl_koreksidiagnosa,
            CASE
            WHEN (koreksidiagnosa_t.is_diagnosa_baru = true) THEN 'BARU'::text
            ELSE 'LAMA'::text
            END AS is_diagnosa_barulama,
            pendaftaran_t.instalasi_id,
            instalasi_m.instalasi_nama,
            diagnosa_m.diagnosa_kode AS dtd_noterperinci,
            dtd_m.dtd_kode AS no_dtd
            FROM (((((((((pendaftaran_t
            JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            JOIN golonganumur_m ON ((pendaftaran_t.golonganumur_id = golonganumur_m.golonganumur_id)))
            JOIN koreksidiagnosa_t ON ((pendaftaran_t.pendaftaran_id = koreksidiagnosa_t.pendaftaran_id)))
            JOIN diagnosa_m ON ((koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id)))
            JOIN pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
            JOIN carakeluar_m ON ((pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id)))
            JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
            LEFT JOIN klasifikasidiagnosa_m ON ((diagnosa_m.klasifikasidiagnosa_id = klasifikasidiagnosa_m.klasifikasidiagnosa_id)))
            LEFT JOIN dtd_m ON ((klasifikasidiagnosa_m.dtd_id = dtd_m.dtd_id)))
            WHERE ((pendaftaran_t.instalasi_id = ANY (ARRAY[1, 2])) AND (koreksidiagnosa_t.kelompokdiagnosa_id = 2))
            ;");
            $this->execute('
                ALTER TABLE public.rl4_b_morbiditasrawatjalandetail_v OWNER TO postgres;
            ');

        $this->execute('DROP VIEW if exists public.rl4_b_morbiditasrawatjalandetailexcel_v;');
        $this->execute("
            CREATE VIEW \"public\".\"rl4_b_morbiditasrawatjalandetailexcel_v\" AS
            SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.pasien_id,
            pasien_m.nama_pasien,
            pendaftaran_t.golonganumur_id,
            golonganumur_m.golonganumur_namalainnya,
            pasien_m.jeniskelamin,
            fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
            koreksidiagnosa_t.diagnosa_id,
            diagnosa_m.diagnosa_nama,
            pasienpulang_t.carakeluar_id,
            carakeluar_m.carakeluar_nama,
            koreksidiagnosa_t.tgl_koreksidiagnosa,
            CASE
            WHEN (koreksidiagnosa_t.is_diagnosa_baru = true) THEN 'BARU'::text
            ELSE 'LAMA'::text
            END AS is_diagnosa_barulama,
            pendaftaran_t.instalasi_id,
            instalasi_m.instalasi_nama,
            diagnosa_m.diagnosa_kode AS dtd_noterperinci,
            dtd_m.dtd_kode AS no_dtd
            FROM (((((((((pendaftaran_t
            JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            JOIN golonganumur_m ON ((pendaftaran_t.golonganumur_id = golonganumur_m.golonganumur_id)))
            JOIN koreksidiagnosa_t ON ((pendaftaran_t.pendaftaran_id = koreksidiagnosa_t.pendaftaran_id)))
            JOIN diagnosa_m ON ((koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id)))
            JOIN pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
            JOIN carakeluar_m ON ((pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id)))
            JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
            LEFT JOIN klasifikasidiagnosa_m ON ((diagnosa_m.klasifikasidiagnosa_id = klasifikasidiagnosa_m.klasifikasidiagnosa_id)))
            LEFT JOIN dtd_m ON ((klasifikasidiagnosa_m.dtd_id = dtd_m.dtd_id)))
            WHERE ((pendaftaran_t.instalasi_id = ANY (ARRAY[1, 2])) AND (koreksidiagnosa_t.kelompokdiagnosa_id = 2))
            ;");
            $this->execute('
                ALTER TABLE public.rl4_b_morbiditasrawatjalandetailexcel_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220117_092552_migrate_BTS28_rl4a cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220117_092552_migrate_BTS28_rl4a cannot be reverted.\n";

        return false;
    }
    */
}
