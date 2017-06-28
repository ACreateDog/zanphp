<?php

namespace Zan\Framework\Foundation\Booting;

use Zan\Framework\Contract\Foundation\Bootable;
use Zan\Framework\Foundation\Application;

class CheckIfBootable implements Bootable
{
    public function bootstrap(Application $app)
    {
        $requiredExtensions = ["apcu", "lz4", "zan"];
        foreach ($requiredExtensions as $extension) {
            if (!extension_loaded($extension)) {
                sys_error("\033[1;31m$extension is not loaded, use php -m to check php modules\033[0m");
            }
        }

        if (getenv('KDT_RUN_MODE') || get_cfg_var('kdt.RUN_MODE')) {
            $this->phpVerCheck();
            $this->zanVerCheck();
        }
    }

    private function zanVerCheck()
    {
        $targetVersion = "3.0.4";
        $currentVersion = swoole_version();
        if (version_compare($currentVersion, $targetVersion) < 0) {
            sys_error("\033[1;31mYour zan version($currentVersion) is lower than $targetVersion, ".
                "suggest upgrade your swoole extension\033[0m");
        }
    }

    private function phpVerCheck()
    {
        $targetVersion = "7.1.3";
        if (version_compare(PHP_VERSION, $targetVersion) < 0) {
            $currentVersion = PHP_VERSION;
            sys_error("\033[1;31mYour php version($currentVersion) is lower than $targetVersion, ".
                "suggest upgrade your php version\033[0m");
        }
    }
}