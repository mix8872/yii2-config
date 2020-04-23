<?php

namespace mix8872\config;

use Composer\Composer;
use Composer\DependencyResolver\Operation\UpdateOperation;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Installer\PackageEvent;
use Composer\Installer\PackageEvents;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\ScriptEvents;
use yii\composer\Installer;

class Plugin implements PluginInterface, EventSubscriberInterface
{
    /**
     * @var Installer
     */
    private $_installer;
    /**
     * @var array noted package updates.
     */
    private $_packageUpdates = [];
    /**
     * @var string path to the vendor directory.
     */
    private $_vendorDir;

    public static function getSubscribedEvents()
    {
        return [
            PackageEvents::POST_PACKAGE_UPDATE => 'checkPackageUpdates',
            ScriptEvents::POST_UPDATE_CMD => 'showUpgradeNotes',
        ];
    }

    /**
     * @inheritdoc
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        $this->_installer = new Installer($io, $composer);
        $composer->getInstallationManager()->addInstaller($this->_installer);
        $this->_vendorDir = rtrim($composer->getConfig()->get('vendor-dir'), '/');
    }

    /**
     * Listen to POST_PACKAGE_UPDATE event and take note of the package updates.
     * @param PackageEvent $event
     */
    public function checkPackageUpdates(PackageEvent $event)
    {
        $operation = $event->getOperation();
        if ($operation instanceof UpdateOperation) {
            $this->_packageUpdates[$operation->getInitialPackage()->getName()] = [
                'from' => $operation->getInitialPackage()->getVersion(),
                'fromPretty' => $operation->getInitialPackage()->getPrettyVersion(),
                'to' => $operation->getTargetPackage()->getVersion(),
                'toPretty' => $operation->getTargetPackage()->getPrettyVersion(),
                'direction' => $event->getPolicy()->versionCompare(
                    $operation->getInitialPackage(),
                    $operation->getTargetPackage(),
                    '<'
                ) ? 'up' : 'down',
            ];
        }
    }

    public function showUpgradeNotes(Script\Event $e)
    {
        $packageName = 'mix8872/yii2-config';
        if (!isset($this->_packageUpdates[$packageName])) {
            return;
        }

        $package = $this->_packageUpdates[$packageName];
        if ($package['fromPretty'] === $package['toPretty']) {
            return;
        }

        $io = $e->getIO();

        if ($package['direction'] === 'up') {
            if (preg_match('/^([0-9]\.[0-9]+\.?[0-9]*)/', $package['fromPretty'], $m)) {
                $fromVersionMajor = $m[1];
            } else {
                $fromVersionMajor = $package['fromPretty'];
            }

            $upgradeFile = $this->_vendorDir . '/' . $packageName . '/CHANGELOG.md';

            if (!is_file($upgradeFile) || !is_readable($upgradeFile)) {
                return false;
            }
            $lines = preg_split('~\R~', file_get_contents($upgradeFile));
            $relevantLines = [];
            $foundExactMatch = false;
            foreach($lines as $line) {
                if (preg_match('/^## (\d\.\d+\.\d+)/u', $line, $matches)) {
                    if ($matches[1] === $package['fromPretty']) {
                        $foundExactMatch = true;
                        continue;
                    }
                    if (version_compare($matches[1], $package['fromPretty'], '<') && ($foundExactMatch || version_compare($matches[1], $fromVersionMajor, '<'))) {
                        break;
                    }
                }
                if ($foundExactMatch) {
                    $relevantLines[] = $line;
                }
            }
            if (!$relevantLines) {
                return;
            }

            $io->write("\n  <fg=yellow;options=bold>Seems you have "
                . ($package['direction'] === 'up' ? 'upgraded' : 'downgraded')
                . ' mix8872/config module from '
                . $package['fromPretty'] . ' to ' . $package['toPretty'] . '.</>'
            );
            $io->write("\n  <options=bold>Please check the change notes for possible incompatible changes");
            $io->write('  and adjust your application code accordingly.</>');
            if (count($relevantLines) > 250) {
                $io->write("\n  <fg=yellow;options=bold>The relevant notes for your upgrade are too long to be displayed here.</>");
                $io->write("\n  You can find the change notes in CHANGELOG.md");
            } else {
                $io->write("\n  " . trim(implode("\n  ", $relevantLines)));
            }
        }
    }
}
