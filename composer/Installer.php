<?php
declare(strict_types=1);

namespace zagovorichev\composer;

use Composer\Package\PackageInterface;
use Composer\Installer\LibraryInstaller;

class Installer extends LibraryInstaller
{
    private const ENV_VENDOR_DIR_PATH = 'VENDOR_DIR_PATH';
    private const ENV_MODULES_DIR_PATH = 'MODULES_DIR_PATH';
    private const ENV_MODULE_NAME_MAP = 'MODULES_NAME_MAP';
    private const ENV_GIT_MODULES = 'GIT_MODULES';

    /**
     * @var string where to place everything that not marked as an module
     */
    private string $vendorDirPath = '';
    /**
     * @var string where to place specified repositories
     */
    private string $modulesDirPath = '';

    /**
     * @var string default generated path for the lib
     */
    private string $defaultPath;

    private array $modules = [];
    private ?string $envVendorDirPath = null;
    private ?string $envModulesDirPath = null;
    private ?array $moduleNameMap = null;

    private function getModules(): array
    {
        if (!$this->modules) {
            $envModules = getenv(self::ENV_GIT_MODULES);
            $this->modules = is_string($envModules) ? explode(' ', $envModules) : [];
        }
        return $this->modules;
    }

    private function isModule(string $name): bool
    {
        return in_array($name, $this->getModules(), true);
    }

    private function getEnvVendorDirPath(): string
    {
        if ($this->envVendorDirPath === null) {
            $this->envVendorDirPath = getenv(self::ENV_VENDOR_DIR_PATH);
            $this->envVendorDirPath = $this->envVendorDirPath ?: '';
        }
        return $this->envVendorDirPath;
    }

    private function getVendorDirPath(): string
    {
        if (!$this->vendorDirPath) {
            $this->vendorDirPath = $this->getEnvVendorDirPath();
            if (!$this->vendorDirPath) {
                $this->initializeVendorDir();
                $this->vendorDirPath = $this->vendorDir ? $this->vendorDir : '';
            }
        }
        return $this->vendorDirPath;
    }

    private function getEnvModulesDirPath(): string
    {
        if ($this->envModulesDirPath === null) {
            $this->envModulesDirPath = getenv(self::ENV_MODULES_DIR_PATH);
            $this->envModulesDirPath = $this->envModulesDirPath ?: '';
        }
        return $this->envModulesDirPath;
    }

    private function getModulesDirPath(): string
    {
        if (!$this->modulesDirPath) {
            $this->modulesDirPath = $this->getEnvModulesDirPath();
            if (!$this->modulesDirPath) {
                $this->modulesDirPath = $this->getVendorDirPath() . '..' . DIRECTORY_SEPARATOR;
            }
        }
        return $this->modulesDirPath;
    }

    private function getEnvModuleNameMap(): array
    {
        if ($this->moduleNameMap === null) {
            $map = getenv(self::ENV_MODULE_NAME_MAP);
            if ($map) {
                $this->moduleNameMap = [];
                $arrMap = explode(' ', $map);
                if ($arrMap) {
                    foreach ($arrMap as $module) {
                        $arrModMap = explode(':', $module);
                        if (is_array($arrModMap) && count($arrModMap) === 2) {
                            $this->moduleNameMap[$arrModMap[0]] = $arrModMap[1];
                        }
                    }
                }
            }
        }
        return $this->moduleNameMap;
    }

    private function getModuleNameMap(): array
    {
        if ($this->moduleNameMap === null) {
            $map = $this->getEnvModuleNameMap();
            $this->moduleNameMap = $map ?: [];
        }
        return $this->moduleNameMap;
    }

    private function getModuleName(string $packageName): string
    {
        $map = $this->getModuleNameMap();
        if (array_key_exists($packageName, $map)) {
            return $map[$packageName];
        }
        return $packageName;
    }

    private function getDefaultPath(PackageInterface $package): string
    {
        if (!$this->defaultPath) {
            // default path
            $targetDir = $package->getTargetDir();
            $this->defaultPath = $this->getVendorDirPath() .
                $package->getPrettyName() .
                ($targetDir ? DIRECTORY_SEPARATOR . $targetDir : '');
        }

        return $this->defaultPath;
    }

    /**
     * Install module to the right place
     */
    public function getInstallPath(PackageInterface $package)
    {
        $packageName = $package->getPrettyName();
        if ($this->isModule($packageName)) {
            return $this->getModulesDirPath() . $this->getModuleName($packageName);
        }
        return $this->getDefaultPath($package);
    }
}
