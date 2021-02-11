<?php
declare(strict_types = 1);

namespace Zagovorichev\Composer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Zagovorichev\Composer\Installers\Installer;

class Plugin implements PluginInterface
{
    private ?Installer $installer = null;

    private function getInstaller(Composer $composer, IOInterface $io): Installer
    {
        if (!$this->installer) {
            $this->installer = new Installer($io, $composer);
        }
        return $this->installer;
    }

    public function setInstaller(Installer $installer): void
    {
        $this->installer = $installer;
    }

    /**
     * {@inheritDoc}
     */
    public function activate(Composer $composer, IOInterface $io): void
    {
        $installer = $this->getInstaller($composer, $io);
        $composer->getInstallationManager()->addInstaller($installer);
    }

    /**
     * {@inheritDoc}
     */
    public function deactivate(Composer $composer, IOInterface $io): void
    {
    }

    /**
     * {@inheritDoc}
     */
    public function uninstall(Composer $composer, IOInterface $io): void
    {
    }
}