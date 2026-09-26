<?php

use yii\db\Migration;

/**
 * Class m210218_095628_migrate_20210218_3164_laporanrekapkinerja_v
 */
class m210218_095628_migrate_20210218_3164_laporanrekapkinerja_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.laporanrekapkinerja_v;');
        $this->execute("
            CREATE VIEW \"public\".\"laporanrekapkinerja_v\" AS
            SELECT sensuspasienranap_r.tgl_sensus,
            sensuspasienranap_r.ruangan_id,
            ruangan_m.ruangan_nama,
            0 AS jmltt_kapasitas,
            0 AS jmltt_tersedia,
            0 AS jml_pasiensebelumnya,
            sensuspasienranap_r.pasien_masuk,
            sensuspasienranap_r.pasien_pindahan,
            (sensuspasienranap_r.pasien_masuk + sensuspasienranap_r.pasien_pindahan) AS jml_pasien,
            sensuspasienranap_r.pasien_keluarhidup,
            sensuspasienranap_r.pasien_keluardipindahkan,
            sensuspasienranap_r.pasien_keluarmeninggalkur48,
            sensuspasienranap_r.pasien_keluarmeninggalleb48,
            (((sensuspasienranap_r.pasien_keluarhidup + sensuspasienranap_r.pasien_keluardipindahkan) + sensuspasienranap_r.pasien_keluarmeninggalkur48) + sensuspasienranap_r.pasien_keluarmeninggalleb48) AS pasien_keluarjumlah,
            sensuspasienranap_r.pasien_akhir,
            sensuspasienranap_r.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            sensuspasienranap_r.pasien_akhir AS hp,
            ((sensuspasienranap_r.pasien_awal + sensuspasienranap_r.pasien_masuk) + sensuspasienranap_r.pasien_pindahan) AS los,
            NULL::text AS alos,
            NULL::text AS bor,
            NULL::text AS toi,
            NULL::text AS bto,
            NULL::text AS ndr,
            NULL::text AS gdr,
            kelaspelayanan_m.urutankelas
            FROM ((sensuspasienranap_r
            LEFT JOIN ruangan_m ON ((sensuspasienranap_r.ruangan_id = ruangan_m.ruangan_id)))
            LEFT JOIN kelaspelayanan_m ON ((sensuspasienranap_r.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            WHERE (ruangan_m.instalasi_id = 3)
            ;");
            $this->execute('
                ALTER TABLE public.laporanrekapkinerja_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210218_095628_migrate_20210218_3164_laporanrekapkinerja_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210218_095628_migrate_20210218_3164_laporanrekapkinerja_v cannot be reverted.\n";

        return false;
    }
    */
}
