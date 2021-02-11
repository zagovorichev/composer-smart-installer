<?php

declare(strict_types = 1);

namespace Zagovorichev\Composer\Tests;

use Composer\Composer;
use Composer\IO\IOInterface;
use PHPUnit\Framework\TestCase;
use Composer\Installer\InstallationManager;
use Zagovorichev\Composer\Installers\Installer;
use Zagovorichev\Composer\Plugin;

class PluginTest extends TestCase
{
    protected $composer;

    protected $io;

    public function setUp(): void
    {
        parent::setUp();

        $this->composer = $this->createMock(Composer::class);
        $this->composer
            ->method('getConfig')
            ->willReturn(new class{
                public function get($name): ?array
                {
                    return null;
                }
            });

        $this->io = $this->createMock(IOInterface::class);
    }

    public function testActive(): void
    {
        $installerMock = $this->createMock(Installer::class);
        $installationManager = $this->createMock(InstallationManager::class);
        $installationManager
            ->expects(self::once())
            ->method('addInstaller')
            ->with($installerMock);

        $this->composer
            ->expects(self::once())
            ->method('getInstallationManager')
            ->willReturn($installationManager);

        // There is no output to test from the activate method. Only test for
        // method call expectations.
        $plugin = new Plugin();
        $plugin->setInstaller($installerMock);
        $plugin->activate($this->composer, $this->io);
    }
}
