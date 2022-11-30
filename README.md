AWS S3 bundle
=============

[![Build Status](https://api.travis-ci.com/Ang3/aws-s3-bundle.svg?branch=main)](https://app.travis-ci.com/github/Ang3/aws-s3-bundle)
[![Latest Stable Version](https://poser.pugx.org/ang3/aws-s3-bundle/v/stable)](https://packagist.org/packages/ang3/aws-s3-bundle)
[![Latest Unstable Version](https://poser.pugx.org/ang3/aws-s3-bundle/v/unstable)](https://packagist.org/packages/ang3/aws-s3-bundle)
[![Total Downloads](https://poser.pugx.org/ang3/aws-s3-bundle/downloads)](https://packagist.org/packages/ang3/aws-s3-bundle)

This bundle integrates AWS S3 to your Symfony project.

**Features**

- Client
- URL generator

Installation
============

Step 1: Download the Bundle
---------------------------

Open a command console, enter your app directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require ang3/aws-s3-bundle
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Step 2: Configure the bundle
----------------------------

In file `.env`, add the contents below and adapt it to your needs:

```dotenv
###> ang3/aws-s3-bundle ###
AWS_S3_KEY="YOUR_KEY"
AWS_S3_SECRET="YOUR_SECRET"
AWS_S3_REGION="YOUR_REGION"
AWS_S3_VERSION="2006-03-01"
AWS_S3_DEFAULT_BUCKET="YOUR_BUCKET"
AWS_S3_DEFAULT_PREFIX=
###< ang3/aws-s3-bundle ###
```

Make sure to replace `YOUR_KEY`, `YOUR_SECRET`, `YOUR_REGION` by your AWS settings.

Optionally, you can set a default bucket and prefix. These values are populated into the service 
`Ang3\Bundle\AwsS3Bundle\Service\AwsS3Config` used in case of generating URL without named bucket.

```dotenv
###> ang3/aws-s3-bundle ###
# ...
AWS_S3_DEFAULT_BUCKET="YOUR_BUCKET"
AWS_S3_DEFAULT_PREFIX=""
###< ang3/aws-s3-bundle ###
```

Usage
=====

Client
------

**Public service ID:** `ang3.aws_s3.client`

To use the ```S3``` client, get it by dependency injection:

```php
namespace App\Service;

use Aws\S3\S3ClientInterface;

class MyService
{
    public function __construct(private S3ClientInterface $s3Client)
    {
    }
}
```

S3 Url generator
----------------

Sometimes, you would want to create a *secured* URL to a stored file (with expiration date). 
This bundle provides a service called ```S3UrlGenerator``` to do it.

First, get the ```S3UrlGenerator``` service by dependency injection:

```php
namespace App\Service;

use Ang3\Bundle\AwsS3Bundle\Service\S3UrlGenerator;

class MyService
{
    public function __construct(private S3UrlGenerator $urlGenerator)
    {
    }
}
```

Then, create your URL for a ```bucket``` and ```key```:

```php
$publicUrl = $this->urlGenerator->generate('my_key', 'my_bucket', '2 weeks');
```

If `my_bucket` is `NULL`, then the default bucket and prefix from configuration is used.

Also, you can generate a URL directly from an instance of 
```Ang3\Bundle\AwsS3Bundle\Dto\FileLocation```:

```php
use Ang3\Bundle\AwsS3Bundle\Dto\FileLocation;

// This bundle provides this class implementing the required interface
$location = new FileLocation('bucket_name', 'file_key', 'optional_key_prefix');
$publicUrl = $this->urlGenerator->locate($location, '2 weeks');
```

That's it!