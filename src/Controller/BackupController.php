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
use NextDom\Managers\AjaxManager;
use NextDom\Managers\UpdateManager;

class BackupController extends BaseController
{
    /**
     * Render backup page
     *
     * @param array $pageData Page data
     *
     * @return string Content of backup page
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public static function get(&$pageData): string
    {

        $pageData['JS_VARS_RAW']['REPO_LIST'] = '[]';
        $pageData['backupAjaxToken'] = AjaxManager::getToken();
        $pageData['backupReposList'] = UpdateManager::listRepo();
        $pageData['JS_END_POOL'][] = '/public/js/desktop/tools/backup.js';
        $pageData['JS_END_POOL'][] = '/public/js/adminlte/utils.js';

        return Render::getInstance()->get('/desktop/tools/backup.html.twig', $pageData);
    }
}
