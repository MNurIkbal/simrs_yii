<?php

use yii\db\Migration;

/**
 * Class m220317_051832_migrate_APD252_infopembayaranalokasi_v
 */
class m220317_051832_migrate_APD252_infopembayaranalokasi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.infopembayaranalokasi_v;');
        $this->execute("
            CREATE VIEW \"public\".\"infopembayaranalokasi_v\" AS
            SELECT pembayaranalokasi_t.pembayaranalokasi_id,
            pembayaranalokasi_t.pengajuanklaim_id,
            pembayaranalokasi_t.terimabayarklaim_id,
            pengajuanklaim_t.no_pengajuanklaim,
            pengajuanklaim_t.tgl_pengajuanklaim,
            terimabayarklaim_t.no_terimabayarklaim,
            terimabayarklaim_t.tgl_terimabayarklaim,
            CASE
            WHEN (pengajuanklaim_t.carabayar_id = 0) THEN 0
            ELSE pengajuanklaim_t.carabayar_id
            END AS carabayar_id,
            CASE
            WHEN (pengajuanklaim_t.carabayar_id <> 0) THEN carabayar_m.carabayar_nama
            ELSE 'SEMUA'::character varying
            END AS carabayar_nama,
            CASE
            WHEN (pengajuanklaim_t.penjamin_id = 0) THEN 0
            ELSE pengajuanklaim_t.penjamin_id
            END AS penjamin_id,
            CASE
            WHEN (pengajuanklaim_t.penjamin_id <> 0) THEN penjamin_m.penjamin_nama
            ELSE 'SEMUA'::character varying
            END AS penjamin_nama,
            pengajuanklaim_t.total_piutang AS total_pengajuan,
            pembayaranalokasi_t.total_terbayar AS total_pembayaran,
            pembayaranalokasi_t.sisa_piutang AS sisa,
            pembayaranalokasi_t.is_edit,
            pembayaranalokasi_t.jumlah_pembayaran,
            CASE
            WHEN (instalasi_m.instalasi_nama IS NULL) THEN 'Semua Instalasi'::character varying
            ELSE instalasi_m.instalasi_nama
            END AS instalasi_nama,
            CASE
            WHEN (ruangan_m.ruangan_nama IS NULL) THEN 'Semua Ruangan'::character varying
            ELSE ruangan_m.ruangan_nama
            END AS ruangan_nama,
            pembayaranalokasi_t.catatan,
            pembayaranalokasi_t.tgl_pembayaranalokasi,
            pengajuanklaim_t.tgl_jatuhtempo
            FROM ((((((pembayaranalokasi_t
            JOIN pengajuanklaim_t ON ((pembayaranalokasi_t.pengajuanklaim_id = pengajuanklaim_t.pengajuanklaim_id)))
            JOIN terimabayarklaim_t ON ((pembayaranalokasi_t.terimabayarklaim_id = terimabayarklaim_t.terimabayarklaim_id)))
            LEFT JOIN carabayar_m ON ((pengajuanklaim_t.carabayar_id = carabayar_m.carabayar_id)))
            LEFT JOIN penjamin_m ON ((pengajuanklaim_t.penjamin_id = penjamin_m.penjamin_id)))
            LEFT JOIN instalasi_m ON ((pengajuanklaim_t.instalasi_id = instalasi_m.instalasi_id)))
            LEFT JOIN ruangan_m ON ((pengajuanklaim_t.ruangan_id = ruangan_m.ruangan_id)))
            WHERE (pembayaranalokasi_t.is_deleted = false)
            ;");
        $this->execute('
            ALTER TABLE public.infopembayaranalokasi_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220317_051832_migrate_APD252_infopembayaranalokasi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220317_051832_migrate_APD252_infopembayaranalokasi_v cannot be reverted.\n";

        return false;
    }
    */
}
