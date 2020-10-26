<?php

namespace App\Support;

use Spatie\MediaLibrary\Support\UrlGenerator\BaseUrlGenerator;

class MediaUrlGenerator extends BaseUrlGenerator
{
    public function getUrl(): string
    {
        if (tenant()->id) {
            $url = sprintf('%s/%s/%s', url('/'), $this->media->disk, $this->getPathRelativeToRoot());
        } else {
            $url = $this->getBaseMediaDirectoryUrl() . '/' . $this->getPathRelativeToRoot();
        }

        $url = $this->makeCompatibleForNonUnixHosts($url);

        $url = $this->rawUrlEncodeFilename($url);

        $url = $this->versionUrl($url);

        return $url;
    }
}
