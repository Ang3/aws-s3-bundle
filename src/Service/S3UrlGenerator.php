<?php

namespace Ang3\Bundle\AwsS3Bundle\Service;

use Ang3\Bundle\AwsS3Bundle\Dto\FileLocation;
use Aws\S3\S3ClientInterface;
use DateTime;
use RuntimeException;

class S3UrlGenerator
{
    public const DEFAULT_TTL = '24 hours';

    public function __construct(private S3ClientInterface $s3Client, private DefaultAwsS3Config $defaultAwsS3Config)
    {
    }

    /**
     * @param DateTime|int|string|null $expires this can be a Unix timestamp, a PHP DateTime object,
     *                                          or a string that can be evaluated by strtotime()
     */
    public function locate(FileLocation $location, DateTime|int|string $expires = null, array $options = []): string
    {
        return $this->generate($location->getBucketName(), $location->getFullKey(), $expires, $options);
    }

    /**
     * @param DateTime|int|string|null $expires this can be a Unix timestamp, a PHP DateTime object,
     *                                          or a string that can be evaluated by strtotime()
     */
    public function generate(string $key, string $bucket = null, DateTime|int|string $expires = null, array $options = []): string
    {
        if (null === $bucket) {
            $defaultBucketName = $this->defaultAwsS3Config->getBucketName();

            if (!$defaultBucketName) {
                throw new \InvalidArgumentException('Missing parameter #2 ($bucketName) - If you wan to use a default bucket and prefix, please set env vars "AWS_S3_DEFAULT_BUCKET" and optionally "AWS_S3_DEFAULT_PREFIX".');
            }

            $bucket = $defaultBucketName;
            $key = $this->defaultAwsS3Config->generatePath($key);
        }

        $this->assertFileExists($bucket, $key);
        $cmd = $this->s3Client->getCommand('GetObject', [
            'Bucket' => $bucket,
            'Key' => $key,
        ]);

        $request = $this->s3Client->createPresignedRequest($cmd, $expires ?: self::DEFAULT_TTL, $options);

        return (string) $request->getUri();
    }

    /**
     * @internal
     *
     * @throws RuntimeException when the file does not exist
     */
    private function assertFileExists(string $bucket, string $key): void
    {
        if (!$this->s3Client->doesObjectExist($bucket, $key)) {
            throw new RuntimeException(sprintf('The file "%s::%s" was not found (maybe deleted).', $bucket, $key));
        }
    }
}
