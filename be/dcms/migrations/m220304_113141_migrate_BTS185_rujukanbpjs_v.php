<?php

use yii\db\Migration;

/**
 * Class m220304_113141_migrate_BTS185_rujukanbpjs_v
 */
class m220304_113141_migrate_BTS185_rujukanbpjs_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.rujukanbpjs_v;');
        $this->execute("
            CREATE VIEW \"public\".\"rujukanbpjs_v\" AS
            SELECT rujukanbpjs_t.rujukanbpjs_id,
            rujukanbpjs_t.pendaftaran_id,
            rujukanbpjs_t.pasienadmisi_id,
            rujukanbpjs_t.instalasi_id,
            rujukanbpjs_t.diagnosa_id,
            diagnosa_m.diagnosa_nama,
            rujukanbpjs_t.perujuk_id,
            rujukanbpjs_t.kelaspelayanan_id,
            rujukanbpjs_t.tanggal_rujukan,
            rujukanbpjs_t.no_rujukan,
            rujukanbpjs_t.rujukan AS rujukan_id,
            rujukan.lookup_name AS rujukan,
            rujukanbpjs_t.spesialis,
            rujukanbpjs_t.catatan_rujukan,
            rujukanbpjs_t.bpjs_id,
            bpjs_t.nosep,
            bpjs_t.nama_peserta,
            bpjs_t.nokartuasuransi,
            bpjs_t.ppkrujukan,
            bpjs_t.diagnosaawal,
            rujukanbpjs_t.jenis_pelayanan_bpjs AS jenis_pelayanan_bpjs_id,
            jenpel.lookup_name AS jenis_pelayanan_bpjs,
            rujukanbpjs_t.dirujukke,
            rujukanbpjs_t.dirujukke_nama,
            rujukanbpjs_t.diagnosa_rujukan_nama,
            rujukanbpjs_t.tglsep,
            rujukanbpjs_t.additional_data
            FROM (((((((((rujukanbpjs_t
            LEFT JOIN pendaftaran_t ON ((rujukanbpjs_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
            LEFT JOIN pasienadmisi_t ON ((rujukanbpjs_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            LEFT JOIN diagnosa_m ON ((rujukanbpjs_t.diagnosa_id = diagnosa_m.diagnosa_id)))
            LEFT JOIN instalasi_m ON ((rujukanbpjs_t.instalasi_id = instalasi_m.instalasi_id)))
            LEFT JOIN perujuk_m ON ((rujukanbpjs_t.perujuk_id = perujuk_m.perujuk_id)))
            LEFT JOIN bpjs_t ON ((rujukanbpjs_t.bpjs_id = bpjs_t.bpjs_id)))
            LEFT JOIN kelaspelayanan_m ON ((rujukanbpjs_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            LEFT JOIN lookup_m rujukan ON ((((rujukanbpjs_t.rujukan)::text = (rujukan.lookup_value)::text) AND (rujukan.lookup_id = ANY (ARRAY[978, 979, 980])))))
            LEFT JOIN lookup_m jenpel ON ((((rujukanbpjs_t.jenis_pelayanan_bpjs)::text = (jenpel.lookup_value)::text) AND (jenpel.lookup_id = ANY (ARRAY[981, 982])))))
            WHERE (rujukanbpjs_t.is_deleted = false)
            ;");
        $this->execute('
            ALTER TABLE public.rujukanbpjs_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220304_113141_migrate_BTS185_rujukanbpjs_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220304_113141_migrate_BTS185_rujukanbpjs_v cannot be reverted.\n";

        return false;
    }
    */
}
