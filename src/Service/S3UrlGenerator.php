<?php

namespace Ang3\Bundle\AwsS3Bundle\Service;

use Ang3\Bundle\AwsS3Bundle\Dto\FileLocation;
use Aws\S3\S3ClientInterface;
use DateTime;
use RuntimeException;

class S3UrlGenerator
{
    public const DEFAULT_TTL = '24 hours';

    public function __construct(private S3ClientInterface $s3Client)
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
    public function generate(string $bucket, string $key, DateTime|int|string $expires = null, array $options = []): string
    {
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
