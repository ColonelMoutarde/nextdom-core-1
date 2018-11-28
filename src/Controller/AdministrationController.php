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
use NextDom\Managers\UpdateManager;
use NextDom\Helpers\Status;
use NextDom\Helpers\SystemHelper;

class AdministrationController extends BaseController
{

    
    public function __construct()
    {
        parent::__construct();
        Status::isConnectedAdminOrFail();
    }

    /**
     * Render administration page
     *
     * @param Render $render Render engine
     * @param array $pageContent Page data
     *
     * @return string Content of administration page
     *
     * @throws \NextDom\Exceptions\CoreException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function get(Render $render, array &$pageContent): string
    {

        $pageContent['IS_ADMIN']  = Status::isConnectAdmin();
        $pageContent['administrationNbUpdates'] = UpdateManager::nbNeedUpdate();
        $pageContent['administrationMemLoad'] = 100;
        $pageContent['administrationSwapLoad'] = 100;
        $freeData = trim(shell_exec('free'));
        $freeData = explode("\n", $freeData);
        if (count($freeData) > 2) {
            $memData = array_merge(
                array_filter(
                    explode(' ', $freeData[1]),
                    function($value) {
                        return $value !== '';
                    }
                )
            );
            $swapData = array_merge(
                array_filter(
                    explode(' ', $freeData[2]),
                    function($value) {
                        return $value !== '';
                    }
                )
            );
            if ($memData[1] != 0) {
                $pageContent['administrationMemLoad'] = round(100 * $memData[2]/$memData[1], 2);
                if ($memData[1] < 1024) {
            			$memTotal = $memData[1] .' B';
            		} elseif ($memData[1] < (1024*1024)) {
            			$memTotal = round($memData[1] / 1024, 0) .' MB';
                } else {
            			$memTotal = round($memData[1] / 1024 / 1024, 0) .' GB';
            		}
                $pageContent['administrationMemTotal'] = $memTotal;
            }
            else {
                $pageContent['administrationMemLoad'] = 0;
                $pageContent['administrationMemTotal'] = 0;
            }
            if ($swapData[1] != 0) {
                $pageContent['administrationSwapLoad'] = round(100 * $swapData[2]/$swapData[1], 2);
                if ($swapData[1] < 1024) {
            			$swapTotal = $swapData[1] .' B';
            		} elseif ($memData[1] < (1024*1024)) {
            			$swapTotal = round($swapData[1] / 1024, 0) .' MB';
                } else {
            			$swapTotal = round($swapData[1] / 1024 / 1024, 0) .' GB';
            		}
                $pageContent['administrationSwapTotal'] = $swapTotal;
            }
            else {
                $pageContent['administrationSwapLoad'] = 0;
            }
        }
        $uptime = SystemHelper::getUptime() % 31556926;
        $pageContent['administrationUptimeDays'] = explode(".", ($uptime / 86400))[0];
        $pageContent['administrationUptimeHours'] = explode(".", (($uptime % 86400) / 3600))[0];
        $pageContent['administrationUptimeMinutes'] = explode(".", ((($uptime % 86400) % 3600) / 60))[0];
        $pageContent['administrationCore'] = SystemHelper::getProcessorCoresCount();
        $pageContent['administrationCpuLoad'] = round(100 * (sys_getloadavg()[0]/$pageContent['administrationCore']), 2);
        $diskTotal=disk_total_space(NEXTDOM_ROOT);
        $pageContent['administrationHddLoad'] = round(100 - 100 * disk_free_space(NEXTDOM_ROOT) / $diskTotal, 2);
        if ($diskTotal < 1024) {
          $diskTotal = $diskTotal .' B';
        } elseif ($diskTotal < (1024*1024)) {
          $diskTotal = round($diskTotal / 1024, 0) .' KB';
        } elseif ($diskTotal < (1024*1024*1024)) {
          $diskTotal = round($diskTotal / (1024*1024), 0) .' MB';
        } else {
          $diskTotal = round($diskTotal / (1024*1024*1024), 0) .' GB';
        }
        $pageContent['administrationHddTotal'] = $diskTotal;
        $pageContent['administrationHTTPConnexion'] = SystemHelper::getHttpConnectionsCount();
        $pageContent['administrationProcess'] = SystemHelper::getProcessCount();
        $pageContent['JS_END_POOL'][] = '/public/js/desktop/administration.js';
        $pageContent['JS_END_POOL'][] = '/public/js/adminlte/utils.js';

        return $render->get('/desktop/administration.html.twig', $pageContent);
    }
}