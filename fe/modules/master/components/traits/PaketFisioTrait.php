<?php

namespace app\modules\master\components\traits;

trait PaketFisioTrait
{
    public $paketFisioRoutes = [
        'paket-fisio-index' => 'Doco\master\actions\PaketFisio\IndexPaketAction',
        'paket-fisio-view' => 'Doco\master\actions\PaketFisio\ViewPaketAction',
        'paket-fisio-sub' => 'Doco\master\actions\PaketFisio\SubPaketAction',
        'paket-fisio-create' => 'Doco\master\actions\PaketFisio\CreatePaketAction',
        'paket-fisio-edit' => 'Doco\master\actions\PaketFisio\EditPaketAction',
        'paket-fisio-update' => 'Doco\master\actions\PaketFisio\UpdatePaketAction',
        'paket-fisio-cek-transaksi' => 'Doco\master\actions\PaketFisio\GetDataPaketAction',
        'paket-fisio-get-paket' => 'Doco\master\actions\PaketFisio\GetDataPaketAction',
        'paket-fisio-get-paket-detail' => 'Doco\master\actions\PaketFisio\GetDataPaketDetailAction',
        'paket-fisio-get-ruangan' => 'Doco\master\actions\PaketFisio\GetInstalasiRuanganAction',
        'paket-fisio-save' => 'Doco\master\actions\PaketFisio\SavePaketAction',
        'paket-fisio-delete' => 'Doco\master\actions\PaketFisio\DeletePaketAction',
        'paket-fisio-export-pdf' => 'Doco\master\actions\PaketFisio\ExportPdfPaketAction',
        'paket-fisio-export-excel' => 'Doco\master\actions\PaketFisio\ExportExcelPaketAction'
    ];
}
