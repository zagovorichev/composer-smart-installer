<?php
declare(strict_types=1);

namespace zagovorichev\composer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;

class InstallPlugin implements PluginInterface
{
    public function activate(Composer $composer, IOInterface $io){
        $installer = new Installer($io, $composer);
        $composer->getInstallationManager()->addInstaller($installer);
    }
}
