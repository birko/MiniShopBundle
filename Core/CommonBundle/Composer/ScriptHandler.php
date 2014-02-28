<?php

namespace Core\CommonBundle\Composer;

use Composer\Script\Event;

class ScriptHandler
{
    public static function postInstall(Event $event)
    {
        $io = $event->getIO();
        $composer = $event->getComposer();
        $io->write("Minishop Configuration");
        $dependencies = array(
            'attribute'     => array('common'),
            'banner'        => array('common', 'media'),
            'category'      => array('common'),
            'common'        => array(),
            'content'       => array('common', 'category', 'media'),
            'marketing'     => array('common'),
            'media'         => array('common'),
            'newsletter'    => array(),
            'price'         => array(),
            'product'       => array('common', 'category', 'attribute', 'media', 'price', 'marketing'),
            'shop'          => array('price', 'user'),
            'user'          => array(),
            'usertext'      => array('common'),
            'vendor'        => array('common', 'product'),
            'voucher'       => array('marketing', 'price', 'shop'),
        );
        $config = array('common', 'user');
        foreach ($dependencies as $key => $value) {
            if (!in_array($key, $config)) {
                if ($io->askConfirmation("Enable feature: ". $key. "? [Y,n]", true)) {
                    $config[] = $key;
                    foreach ($value as $dep) {
                        if (!in_array($dep, $config)) {
                            $config[] = $dep;
                        }
                    }
                }
            }
        }
        $io->write("app/AppKernel.php" . ": Configuring");
        $appKernel = file_get_contents("app/AppKernel.php.dist");
        foreach ($config as $val) {
            $appKernel = str_replace("//". $val. ": ", "", $appKernel);
        }
        file_put_contents("app/AppKernel.php", $appKernel);
        $io->write("app/AppKernel.php" . ": Done");
        $configsFiles = array(
            "app.yml",
            "routing.yml",
            "security.yml",
            "config.yml",
        );
        foreach ($configsFiles as $file) {
            $io->write($file . ": Creating");
            $configFile = file_get_contents("app/config/" . $file . ".dist");
            foreach ($config as $val) {
                $configFile = str_replace("#". $val. ": ", "", $configFile);
            }
            file_put_contents("app/config/" . $file, $configFile);
            $io->write( $file . ": Done");
        }
        $io->write("MiniShop Configuration ended.");
        $io->write("---------------------------------");
    }
}
