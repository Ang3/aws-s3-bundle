<?php

namespace Ang3\Bundle\AwsS3Bundle\Dto;

class FileLocation
{
    public function __construct(private string $bucketName,
                                private string $key,
                                private ?string $prefix = null)
    {
    }

    public function getBucketName(): string
    {
        return $this->bucketName;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getPrefix(): ?string
    {
        return $this->prefix;
    }

    public function getFullKey(): string
    {
        if (null === $this->prefix) {
            return $this->key;
        }

        return sprintf('%s/%s', $this->key, $this->prefix);
    }
}
