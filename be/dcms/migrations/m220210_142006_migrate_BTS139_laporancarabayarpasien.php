<?php

use yii\db\Migration;

/**
 * Class m220210_142006_migrate_BTS139_laporancarabayarpasien
 */
class m220210_142006_migrate_BTS139_laporancarabayarpasien extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.laporanpersentasecarabayar_v;');
        $this->execute("
            CREATE VIEW \"public\".\"laporanpersentasecarabayar_v\" AS
            SELECT 'RJ'::text AS jenis,
            (to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text))::date AS tgl_pendaftaran,
            pendaftaran_t.carabayar_id,
            carabayar_m.carabayar_nama,
            count(pendaftaran_t.carabayar_id) AS total_carabayar,
            round((((count(pendaftaran_t.carabayar_id))::numeric / jumlah.total) * (100)::numeric), 2) AS total_persentase
            FROM (((pendaftaran_t
            JOIN ( SELECT sum(sum_carabayar.total_carabayar) AS total
            FROM ( SELECT pendaftaran_t_1.carabayar_id,
            count(pendaftaran_t_1.carabayar_id) AS total_carabayar
            FROM pendaftaran_t pendaftaran_t_1
            WHERE (pendaftaran_t_1.instalasi_id = 1)
            GROUP BY pendaftaran_t_1.carabayar_id) sum_carabayar) jumlah ON ((pendaftaran_t.is_deleted = false)))
            JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
            JOIN pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
            WHERE ((pendaftaran_t.instalasi_id = 1) AND (carabayar_m.is_active = true) AND (carabayar_m.is_deleted = false) AND (pendaftaran_t.pasienbatalperiksa_id IS NULL))
            GROUP BY pendaftaran_t.carabayar_id, jumlah.total, carabayar_m.carabayar_nama, (to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text))::date
            UNION ALL
            SELECT 'RI'::text AS jenis,
            (to_char(pasienadmisi_t.tgl_admisi, 'YYYY-MM-DD'::text))::date AS tgl_pendaftaran,
            pasienadmisi_t.carabayar_id,
            carabayar_m.carabayar_nama,
            count(pasienadmisi_t.carabayar_id) AS total_carabayar,
            round((((count(pendaftaran_t.carabayar_id))::numeric / jumlah.total) * (100)::numeric), 2) AS total_persentase
            FROM ((((pendaftaran_t
            JOIN ( SELECT sum(sum_carabayar.total_carabayar) AS total
            FROM ( SELECT pendaftaran_t_1.carabayar_id,
            count(pendaftaran_t_1.carabayar_id) AS total_carabayar
            FROM pendaftaran_t pendaftaran_t_1
            GROUP BY pendaftaran_t_1.carabayar_id) sum_carabayar) jumlah ON ((pendaftaran_t.is_deleted = false)))
            JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            JOIN carabayar_m ON ((pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id)))
            JOIN pasienpulang_t ON ((pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
            WHERE ((carabayar_m.is_active = true) AND (carabayar_m.is_deleted = false) AND (pasienadmisi_t.pasienbatalperiksa_id IS NULL))
            GROUP BY pasienadmisi_t.carabayar_id, jumlah.total, carabayar_m.carabayar_nama, (to_char(pasienadmisi_t.tgl_admisi, 'YYYY-MM-DD'::text))::date
            UNION ALL
            SELECT 'RD'::text AS jenis,
            (to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text))::date AS tgl_pendaftaran,
            pendaftaran_t.carabayar_id,
            carabayar_m.carabayar_nama,
            count(pendaftaran_t.carabayar_id) AS total_carabayar,
            round((((count(pendaftaran_t.carabayar_id))::numeric / jumlah.total) * (100)::numeric), 2) AS total_persentase
            FROM (((pendaftaran_t
            JOIN ( SELECT sum(sum_carabayar.total_carabayar) AS total
            FROM ( SELECT pendaftaran_t_1.carabayar_id,
            count(pendaftaran_t_1.carabayar_id) AS total_carabayar
            FROM pendaftaran_t pendaftaran_t_1
            WHERE (pendaftaran_t_1.instalasi_id = 2)
            GROUP BY pendaftaran_t_1.carabayar_id) sum_carabayar) jumlah ON ((pendaftaran_t.is_deleted = false)))
            JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
            JOIN pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
            WHERE ((pendaftaran_t.instalasi_id = 2) AND (carabayar_m.is_active = true) AND (carabayar_m.is_deleted = false) AND (pendaftaran_t.pasienbatalperiksa_id IS NULL))
            GROUP BY pendaftaran_t.carabayar_id, jumlah.total, carabayar_m.carabayar_nama, (to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text))::date
            UNION ALL
            SELECT 'MCU'::text AS jenis,
            (to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text))::date AS tgl_pendaftaran,
            pendaftaran_t.carabayar_id,
            carabayar_m.carabayar_nama,
            count(pendaftaran_t.carabayar_id) AS total_carabayar,
            round((((count(pendaftaran_t.carabayar_id))::numeric / jumlah.total) * (100)::numeric), 2) AS total_persentase
            FROM (((pendaftaran_t
            JOIN ( SELECT sum(sum_carabayar.total_carabayar) AS total
            FROM ( SELECT pendaftaran_t_1.carabayar_id,
            count(pendaftaran_t_1.carabayar_id) AS total_carabayar
            FROM pendaftaran_t pendaftaran_t_1
            WHERE (pendaftaran_t_1.instalasi_id = 2)
            GROUP BY pendaftaran_t_1.carabayar_id) sum_carabayar) jumlah ON ((pendaftaran_t.is_deleted = false)))
            JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
            JOIN pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
            WHERE ((pendaftaran_t.instalasi_id = 21) AND (carabayar_m.is_active = true) AND (carabayar_m.is_deleted = false) AND (pendaftaran_t.pasienbatalperiksa_id IS NULL))
            GROUP BY pendaftaran_t.carabayar_id, jumlah.total, carabayar_m.carabayar_nama, (to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text))::date
            UNION ALL
            SELECT 'PENUNJANG'::text AS jenis,
            (to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text))::date AS tgl_pendaftaran,
            pendaftaran_t.carabayar_id,
            carabayar_m.carabayar_nama,
            count(pendaftaran_t.carabayar_id) AS total_carabayar,
            round((((count(pendaftaran_t.carabayar_id))::numeric / jumlah.total) * (100)::numeric), 2) AS total_persentase
            FROM ((pendaftaran_t
            JOIN ( SELECT sum(sum_carabayar.total_carabayar) AS total
            FROM ( SELECT pendaftaran_t_1.carabayar_id,
            count(pendaftaran_t_1.carabayar_id) AS total_carabayar
            FROM pendaftaran_t pendaftaran_t_1
            WHERE (pendaftaran_t_1.instalasi_id = 2)
            GROUP BY pendaftaran_t_1.carabayar_id) sum_carabayar) jumlah ON ((pendaftaran_t.is_deleted = false)))
            JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
            WHERE ((pendaftaran_t.instalasi_id = ANY (ARRAY[4, 5, 12])) AND (carabayar_m.is_active = true) AND (carabayar_m.is_deleted = false) AND (pendaftaran_t.pasienbatalperiksa_id IS NULL))
            GROUP BY pendaftaran_t.carabayar_id, jumlah.total, carabayar_m.carabayar_nama, (to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text))::date
            ;");
        $this->execute('
            ALTER TABLE public.laporanpersentasecarabayar_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220210_142006_migrate_BTS139_laporancarabayarpasien cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220210_142006_migrate_BTS139_laporancarabayarpasien cannot be reverted.\n";

        return false;
    }
    */
}
