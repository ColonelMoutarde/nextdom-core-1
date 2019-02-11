<?php

/* This file is part of NextDom Software.
 *
 * NextDom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * NextDom Software is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with NextDom Software. If not, see <http://www.gnu.org/licenses/>.
 *
 * @Support <https://www.nextdom.org>
 * @Email   <admin@nextdom.org>
 * @Authors/Contributors: Sylvaner, Byackee, cyrilphoenix71, ColonelMoutarde, edgd1er, slobberbone, Astral0, DanoneKiD
 */

namespace NextDom\Controller;


use NextDom\Helpers\Render;
use NextDom\Helpers\Status;
use NextDom\Helpers\SystemHelper;
use NextDom\Managers\UpdateManager;

class AdministrationController extends BaseController
{
    /**
     * Render administration page
     *
     * @param array $pageData Page data
     *
     * @return string Content of administration page
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public static function get(&$pageData): string
    {
        $pageData['IS_ADMIN'] = Status::isConnectAdmin();
        $pageData['administrationNbUpdates'] = UpdateManager::nbNeedUpdate();
        $pageData['administrationMemLoad'] = 100;
        $pageData['administrationSwapLoad'] = 100;
        $freeData = trim(shell_exec('free'));
        $freeData = explode("\n", $freeData);
        if (count($freeData) > 2) {
            $memData = array_merge(
                array_filter(
                    explode(' ', $freeData[1]),
                    function ($value) {
                        return $value !== '';
                    }
                )
            );
            $swapData = array_merge(
                array_filter(
                    explode(' ', $freeData[2]),
                    function ($value) {
                        return $value !== '';
                    }
                )
            );
            if ($memData[1] != 0) {
                $pageData['administrationMemLoad'] = round(100 * $memData[2] / $memData[1], 2);
                if ($memData[1] < 1024) {
                    $memTotal = $memData[1] . ' B';
                } elseif ($memData[1] < (1024 * 1024)) {
                    $memTotal = round($memData[1] / 1024, 0) . ' MB';
                } else {
                    $memTotal = round($memData[1] / 1024 / 1024, 0) . ' GB';
                }
                $pageData['administrationMemTotal'] = $memTotal;
            } else {
                $pageData['administrationMemLoad'] = 0;
                $pageData['administrationMemTotal'] = 0;
            }
            if ($swapData[1] != 0) {
                $pageData['administrationSwapLoad'] = round(100 * $swapData[2] / $swapData[1], 2);
                if ($swapData[1] < 1024) {
                    $swapTotal = $swapData[1] . ' B';
                } elseif ($memData[1] < (1024 * 1024)) {
                    $swapTotal = round($swapData[1] / 1024, 0) . ' MB';
                } else {
                    $swapTotal = round($swapData[1] / 1024 / 1024, 0) . ' GB';
                }
                $pageData['administrationSwapTotal'] = $swapTotal;
            } else {
                $pageData['administrationSwapLoad'] = 0;
            }
        }
        $uptime = SystemHelper::getUptime() % 31556926;
        $pageData['administrationUptimeDays'] = explode(".", ($uptime / 86400))[0];
        $pageData['administrationUptimeHours'] = explode(".", (($uptime % 86400) / 3600))[0];
        $pageData['administrationUptimeMinutes'] = explode(".", ((($uptime % 86400) % 3600) / 60))[0];
        $pageData['administrationCore'] = SystemHelper::getProcessorCoresCount();
        $pageData['administrationCpuLoad'] = round(100 * (sys_getloadavg()[0] / $pageData['administrationCore']), 2);
        $diskTotal = disk_total_space(NEXTDOM_ROOT);
        $pageData['administrationHddLoad'] = round(100 - 100 * disk_free_space(NEXTDOM_ROOT) / $diskTotal, 2);
        if ($diskTotal < 1024) {
            $diskTotal = $diskTotal . ' B';
        } elseif ($diskTotal < (1024 * 1024)) {
            $diskTotal = round($diskTotal / 1024, 0) . ' KB';
        } elseif ($diskTotal < (1024 * 1024 * 1024)) {
            $diskTotal = round($diskTotal / (1024 * 1024), 0) . ' MB';
        } else {
            $diskTotal = round($diskTotal / (1024 * 1024 * 1024), 0) . ' GB';
        }
        $pageData['administrationHddTotal'] = $diskTotal;
        $pageData['administrationHTTPConnexion'] = SystemHelper::getHttpConnectionsCount();
        $pageData['administrationProcess'] = SystemHelper::getProcessCount();
        $pageData['JS_END_POOL'][] = '/public/js/desktop/administration.js';
        $pageData['JS_END_POOL'][] = '/public/js/adminlte/utils.js';

        return Render::getInstance()->get('/desktop/administration.html.twig', $pageData);
    }
}