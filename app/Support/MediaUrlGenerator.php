<?php

namespace App\Support;

use DateTimeInterface;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\Support\UrlGenerator\BaseUrlGenerator;

class MediaUrlGenerator extends BaseUrlGenerator
{
    public function getUrl(): string
    {
        if (tenant('id')) {
            $url = sprintf('%s/%s/%s', url('/'), $this->media->disk, $this->getPathRelativeToRoot());
        } else {
            $url = $this->getBaseMediaDirectoryUrl() . $this->getPathRelativeToRoot();
        }

        $url = $this->versionUrl($url);

        return $url;
    }

    /*
     * Get the path for the profile of a media item.
     */
    public function getPath(): string
    {
        return $this->getStoragePath() . '/' . $this->getPathRelativeToRoot();
    }

    protected function getBaseMediaDirectoryUrl(): string
    {
        // if ($diskUrl = config()->get("filesystems.disks.{$this->media->disk}.root")) {
        //     return str_replace(url('/'), '', $diskUrl);
        // }

        return $this->getDisk()->url('/');
    }

    public function getTemporaryUrl(DateTimeInterface $expiration, array $options = []): string
    {
        return $this->getDisk()->temporaryUrl($this->getPathRelativeToRoot(), $expiration, $options);
    }

    public function getResponsiveImagesDirectoryUrl(): string
    {
        $base = Str::finish($this->getBaseMediaDirectoryUrl(), '/');

        $path = $this->pathGenerator->getPathForResponsiveImages($this->media);

        return Str::finish(url($base . $path), '/');
    }
}
