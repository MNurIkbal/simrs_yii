<?php

use yii\db\Migration;

/**
 * Class m220404_063309_migrate_BTS215_sensuspasienranap_recalculate_v
 */
class m220404_063309_migrate_BTS215_sensuspasienranap_recalculate_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.sensuspasienranap_recalculate_v;');
        $this->execute("
            CREATE VIEW \"public\".\"sensuspasienranap_recalculate_v\" AS
            SELECT a.tgl_sensus,
            a.ruangan_id,
            a.kelaspelayanan_id
            FROM ( SELECT (pasienadmisi_t.tgl_admisi)::date AS tgl_sensus,
            masukkamar_t.ruangan_id,
            masukkamar_t.kelaspelayanan_id
            FROM ((pasienadmisi_t
            JOIN pendaftaran_t ON ((pasienadmisi_t.pasienadmisi_id = pendaftaran_t.pasienadmisi_id)))
            JOIN masukkamar_t ON ((pasienadmisi_t.pasienadmisi_id = masukkamar_t.pasienadmisi_id)))
            WHERE (((pasienadmisi_t.tgl_admisi)::date > ( SELECT konfigsystem_k.set_tgl_sensus
            FROM konfigsystem_k)) AND (pasienadmisi_t.status_ranap <> 453) AND (pasienadmisi_t.pasienbatalperiksa_id IS NULL) AND ((masukkamar_t.tgl_masukkamar)::time without time zone = '00:00:00'::time without time zone))
            GROUP BY ((pasienadmisi_t.tgl_admisi)::date), masukkamar_t.ruangan_id, masukkamar_t.kelaspelayanan_id
            UNION ALL
            SELECT (pindahkamar_t.tgl_pindahkamar)::date AS tgl_sensus,
            pindahkamar_t.ruangan_id,
            pindahkamar_t.kelaspelayanan_id
            FROM (pasienadmisi_t
            LEFT JOIN pindahkamar_t ON ((pindahkamar_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            WHERE (((pindahkamar_t.tgl_pindahkamar)::date >= ( SELECT konfigsystem_k.set_tgl_sensus
            FROM konfigsystem_k)) AND (pasienadmisi_t.tgl_admisi >= ( SELECT konfigsystem_k.set_tgl_sensus
            FROM konfigsystem_k)))
            UNION ALL
            SELECT (pasienpulang_t.tglpasienpulang)::date AS tgl_sensus,
            pasienpulang_t.ruanganakhir_id AS ruangan_id,
            pasienadmisi_t.kelaspelayanan_id
            FROM (pasienadmisi_t
            LEFT JOIN pasienpulang_t ON ((pasienpulang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            WHERE (((pasienpulang_t.tglpasienpulang)::date >= ( SELECT konfigsystem_k.set_tgl_sensus
            FROM konfigsystem_k)) AND (pasienpulang_t.pasienadmisi_id IS NOT NULL) AND (pasienpulang_t.carakeluar_id = ANY (ARRAY[1, 3, 5, 6, 7])) AND (pasienadmisi_t.tgl_admisi >= ( SELECT konfigsystem_k.set_tgl_sensus
            FROM konfigsystem_k)))
            UNION ALL
            SELECT (pindahkamar_t.tgl_pindahkamar)::date AS tgl_sensus,
            masukkamar_t.ruangan_id,
            masukkamar_t.kelaspelayanan_id
            FROM ((pasienadmisi_t
            LEFT JOIN pindahkamar_t ON ((pindahkamar_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            LEFT JOIN masukkamar_t ON ((masukkamar_t.pindahkamar_id = pindahkamar_t.pindahkamar_id)))
            WHERE (((pindahkamar_t.tgl_pindahkamar)::date >= ( SELECT konfigsystem_k.set_tgl_sensus
            FROM konfigsystem_k)) AND (masukkamar_t.pindahkamar_id IS NOT NULL))
            UNION ALL
            SELECT (pasienpulang_t.tglpasienpulang)::date AS tgl_sensus,
            pasienpulang_t.ruanganakhir_id AS ruangan_id,
            pasienadmisi_t.kelaspelayanan_id
            FROM (pasienadmisi_t
            LEFT JOIN pasienpulang_t ON ((pasienpulang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            WHERE (((pasienpulang_t.tglpasienpulang)::date <= ( SELECT konfigsystem_k.set_tgl_sensus
            FROM konfigsystem_k)) AND (((pasienpulang_t.tglpasienpulang)::date - (pasienadmisi_t.tgl_admisi)::date) <= 2) AND (pasienpulang_t.pasienadmisi_id IS NOT NULL) AND (pasienpulang_t.carakeluar_id = 4))
            UNION ALL
            SELECT (pasienpulang_t.tglpasienpulang)::date AS tgl_sensus,
            pasienpulang_t.ruanganakhir_id AS ruangan_id,
            pasienadmisi_t.kelaspelayanan_id
            FROM (pasienadmisi_t
            LEFT JOIN pasienpulang_t ON ((pasienpulang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            WHERE (((pasienpulang_t.tglpasienpulang)::date > ( SELECT konfigsystem_k.set_tgl_sensus
            FROM konfigsystem_k)) AND (((pasienpulang_t.tglpasienpulang)::date - (pasienadmisi_t.tgl_admisi)::date) > 2) AND (pasienpulang_t.pasienadmisi_id IS NOT NULL) AND (pasienpulang_t.carakeluar_id = 4))) a
            GROUP BY a.tgl_sensus, a.ruangan_id, a.kelaspelayanan_id
            ;");
        $this->execute('
            ALTER TABLE public.sensuspasienranap_recalculate_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220404_063309_migrate_BTS215_sensuspasienranap_recalculate_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220404_063309_migrate_BTS215_sensuspasienranap_recalculate_v cannot be reverted.\n";

        return false;
    }
    */
}
