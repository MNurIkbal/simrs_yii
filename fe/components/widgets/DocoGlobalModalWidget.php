<?php

namespace app\components\widgets;

use Yii;
use yii\base\Widget;

class DocoGlobalModalWidget extends Widget
{
    public $type;
    public $title;
    public $randString;
    public $url;
    public $url_sync;
    public $url_download;
    public $params;

    public function init()
    {
        parent::init();

        $this->url_sync     = $this->buildFinalUrl($this->url_sync);
        $this->url_download = $this->buildFinalUrl($this->url_download);
    }

    public function run()
    {
        return $this->render('@app/components/widgets/views/_DocoGlobalModalWidget', [
            'type'          => $this->type ?: 'excel',
            'title'         => $this->title,
            'randString'    => $this->randString,
            'url'           => $this->url,
            'url_sync'      => $this->url_sync,
            'url_download'  => $this->url_download,
        ]);
    }

    private function buildFinalUrl($path)
    {
        if (empty($path)) {
            return null;
        }

        $baseUrl = $this->url;
        $queryParams = [];

        if (!empty($this->type)) {
            $queryParams['type'] = $this->type;
        }

        $queryString = http_build_query($queryParams);

        $separator = $queryString ? '&' : '?';
        $finalUrl = "{$baseUrl}{$path}" . ($queryString ? "?{$queryString}" : '') . "{$separator}randString={$this->randString}";

        return $finalUrl;
    }
}
