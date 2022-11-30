<?php

namespace Ang3\Bundle\AwsS3Bundle\Service;

class DefaultAwsS3Config
{
    public function __construct(private ?string $bucketName = null, private ?string $bucketPrefix = null)
    {
    }

    public function getBucketName(): ?string
    {
        return $this->bucketName;
    }

    public function getBucketPrefix(): ?string
    {
        return $this->bucketPrefix;
    }

    public function generatePath(string $path): string
    {
        return $this->bucketPrefix ? sprintf('%s/%s', $this->bucketPrefix, $path) : $path;
    }
}
