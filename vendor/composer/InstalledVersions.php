<?php


namespace Composer;

use Composer\Autoload\ClassLoader;
use Composer\Semver\VersionParser;


class InstalledVersions
{
    private static $installed = array(
        'root' =>
            array(
                'pretty_version' => 'dev-master',
                'version' => 'dev-master',
                'aliases' =>
                    array(),
                'reference' => '671b474dcd870f6625de3989a118d4ce9e30fb1e',
                'name' => '__root__',
            ),
        'versions' =>
            array(
                '__root__' =>
                    array(
                        'pretty_version' => 'dev-master',
                        'version' => 'dev-master',
                        'aliases' =>
                            array(),
                        'reference' => '671b474dcd870f6625de3989a118d4ce9e30fb1e',
                    ),
                'firebase/php-jwt' =>
                    array(
                        'pretty_version' => 'v5.2.1',
                        'version' => '5.2.1.0',
                        'aliases' =>
                            array(),
                        'reference' => 'f42c9110abe98dd6cfe9053c49bc86acc70b2d23',
                    ),
                'google/auth' =>
                    array(
                        'pretty_version' => 'v1.15.0',
                        'version' => '1.15.0.0',
                        'aliases' =>
                            array(),
                        'reference' => 'b346c07de6613e26443d7b4830e5e1933b830dc4',
                    ),
                'google/cloud-core' =>
                    array(
                        'pretty_version' => 'v1.41.0',
                        'version' => '1.41.0.0',
                        'aliases' =>
                            array(),
                        'reference' => '2e58627e1c4f1417631ba4b0a1098b66ac98665c',
                    ),
                'google/cloud-storage' =>
                    array(
                        'pretty_version' => 'v1.23.1',
                        'version' => '1.23.1.0',
                        'aliases' =>
                            array(),
                        'reference' => 'ca3817dae90d7d757381f3f63c9d7df58ca1416f',
                    ),
                'google/crc32' =>
                    array(
                        'pretty_version' => 'v0.1.0',
                        'version' => '0.1.0.0',
                        'aliases' =>
                            array(),
                        'reference' => 'a8525f0dea6fca1893e1bae2f6e804c5f7d007fb',
                    ),
                'guzzlehttp/guzzle' =>
                    array(
                        'pretty_version' => '6.5.5',
                        'version' => '6.5.5.0',
                        'aliases' =>
                            array(),
                        'reference' => '9d4290de1cfd701f38099ef7e183b64b4b7b0c5e',
                    ),
                'guzzlehttp/promises' =>
                    array(
                        'pretty_version' => '1.4.0',
                        'version' => '1.4.0.0',
                        'aliases' =>
                            array(),
                        'reference' => '60d379c243457e073cff02bc323a2a86cb355631',
                    ),
                'guzzlehttp/psr7' =>
                    array(
                        'pretty_version' => '1.7.0',
                        'version' => '1.7.0.0',
                        'aliases' =>
                            array(),
                        'reference' => '53330f47520498c0ae1f61f7e2c90f55690c06a3',
                    ),
                'monolog/monolog' =>
                    array(
                        'pretty_version' => '1.26.0',
                        'version' => '1.26.0.0',
                        'aliases' =>
                            array(),
                        'reference' => '2209ddd84e7ef1256b7af205d0717fb62cfc9c33',
                    ),
                'paragonie/random_compat' =>
                    array(
                        'pretty_version' => 'v9.99.100',
                        'version' => '9.99.100.0',
                        'aliases' =>
                            array(),
                        'reference' => '996434e5492cb4c3edcb9168db6fbb1359ef965a',
                    ),
                'psr/cache' =>
                    array(
                        'pretty_version' => '1.0.1',
                        'version' => '1.0.1.0',
                        'aliases' =>
                            array(),
                        'reference' => 'd11b50ad223250cf17b86e38383413f5a6764bf8',
                    ),
                'psr/http-message' =>
                    array(
                        'pretty_version' => '1.0.1',
                        'version' => '1.0.1.0',
                        'aliases' =>
                            array(),
                        'reference' => 'f6561bf28d520154e4b0ec72be95418abe6d9363',
                    ),
                'psr/http-message-implementation' =>
                    array(
                        'provided' =>
                            array(
                                0 => '1.0',
                            ),
                    ),
                'psr/log' =>
                    array(
                        'pretty_version' => '1.1.3',
                        'version' => '1.1.3.0',
                        'aliases' =>
                            array(),
                        'reference' => '0f73288fd15629204f9d42b7055f72dacbe811fc',
                    ),
                'psr/log-implementation' =>
                    array(
                        'provided' =>
                            array(
                                0 => '1.0.0',
                            ),
                    ),
                'ralouphie/getallheaders' =>
                    array(
                        'pretty_version' => '3.0.3',
                        'version' => '3.0.3.0',
                        'aliases' =>
                            array(),
                        'reference' => '120b605dfeb996808c31b6477290a714d356e822',
                    ),
                'rize/uri-template' =>
                    array(
                        'pretty_version' => '0.3.2',
                        'version' => '0.3.2.0',
                        'aliases' =>
                            array(),
                        'reference' => '9e5fdd5c47147aa5adf7f760002ee591ed37b9ca',
                    ),
                'symfony/polyfill-intl-idn' =>
                    array(
                        'pretty_version' => 'v1.19.0',
                        'version' => '1.19.0.0',
                        'aliases' =>
                            array(),
                        'reference' => '4ad5115c0f5d5172a9fe8147675ec6de266d8826',
                    ),
                'symfony/polyfill-intl-normalizer' =>
                    array(
                        'pretty_version' => 'v1.19.0',
                        'version' => '1.19.0.0',
                        'aliases' =>
                            array(),
                        'reference' => '8db0ae7936b42feb370840cf24de1a144fb0ef27',
                    ),
                'symfony/polyfill-php70' =>
                    array(
                        'pretty_version' => 'v1.19.0',
                        'version' => '1.19.0.0',
                        'aliases' =>
                            array(),
                        'reference' => '3fe414077251a81a1b15b1c709faf5c2fbae3d4e',
                    ),
                'symfony/polyfill-php72' =>
                    array(
                        'pretty_version' => 'v1.19.0',
                        'version' => '1.19.0.0',
                        'aliases' =>
                            array(),
                        'reference' => 'beecef6b463b06954638f02378f52496cb84bacc',
                    ),
            ),
    );
    private static $canGetVendors;
    private static $installedByVendor = array();


    public static function getInstalledPackages()
    {
        $packages = array();
        foreach (self::getInstalled() as $installed) {
            $packages[] = array_keys($installed['versions']);
        }


        if (1 === \count($packages)) {
            return $packages[0];
        }

        return array_keys(array_flip(\call_user_func_array('array_merge', $packages)));
    }


    public static function isInstalled($packageName)
    {
        foreach (self::getInstalled() as $installed) {
            if (isset($installed['versions'][$packageName])) {
                return true;
            }
        }

        return false;
    }


    public static function satisfies(VersionParser $parser, $packageName, $constraint)
    {
        $constraint = $parser->parseConstraints($constraint);
        $provided = $parser->parseConstraints(self::getVersionRanges($packageName));

        return $provided->matches($constraint);
    }


    public static function getVersionRanges($packageName)
    {
        foreach (self::getInstalled() as $installed) {
            if (!isset($installed['versions'][$packageName])) {
                continue;
            }

            $ranges = array();
            if (isset($installed['versions'][$packageName]['pretty_version'])) {
                $ranges[] = $installed['versions'][$packageName]['pretty_version'];
            }
            if (array_key_exists('aliases', $installed['versions'][$packageName])) {
                $ranges = array_merge($ranges, $installed['versions'][$packageName]['aliases']);
            }
            if (array_key_exists('replaced', $installed['versions'][$packageName])) {
                $ranges = array_merge($ranges, $installed['versions'][$packageName]['replaced']);
            }
            if (array_key_exists('provided', $installed['versions'][$packageName])) {
                $ranges = array_merge($ranges, $installed['versions'][$packageName]['provided']);
            }

            return implode(' || ', $ranges);
        }

        throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
    }


    public static function getVersion($packageName)
    {
        foreach (self::getInstalled() as $installed) {
            if (!isset($installed['versions'][$packageName])) {
                continue;
            }

            if (!isset($installed['versions'][$packageName]['version'])) {
                return null;
            }

            return $installed['versions'][$packageName]['version'];
        }

        throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
    }


    public static function getPrettyVersion($packageName)
    {
        foreach (self::getInstalled() as $installed) {
            if (!isset($installed['versions'][$packageName])) {
                continue;
            }

            if (!isset($installed['versions'][$packageName]['pretty_version'])) {
                return null;
            }

            return $installed['versions'][$packageName]['pretty_version'];
        }

        throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
    }


    public static function getReference($packageName)
    {
        foreach (self::getInstalled() as $installed) {
            if (!isset($installed['versions'][$packageName])) {
                continue;
            }

            if (!isset($installed['versions'][$packageName]['reference'])) {
                return null;
            }

            return $installed['versions'][$packageName]['reference'];
        }

        throw new \OutOfBoundsException('Package "' . $packageName . '" is not installed');
    }


    public static function getRootPackage()
    {
        $installed = self::getInstalled();

        return $installed[0]['root'];
    }


    public static function getRawData()
    {
        return self::$installed;
    }


    public static function reload($data)
    {
        self::$installed = $data;
        self::$installedByVendor = array();
    }


    private static function getInstalled()
    {
        if (null === self::$canGetVendors) {
            self::$canGetVendors = method_exists('Composer\Autoload\ClassLoader', 'getRegisteredLoaders');
        }

        $installed = array();

        if (self::$canGetVendors) {

            foreach (ClassLoader::getRegisteredLoaders() as $vendorDir => $loader) {
                if (isset(self::$installedByVendor[$vendorDir])) {
                    $installed[] = self::$installedByVendor[$vendorDir];
                } elseif (is_file($vendorDir . '/composer/installed.php')) {
                    $installed[] = self::$installedByVendor[$vendorDir] = require $vendorDir . '/composer/installed.php';
                }
            }
        }

        $installed[] = self::$installed;

        return $installed;
    }
}
