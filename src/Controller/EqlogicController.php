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

class EqlogicController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        Status::isConnectedAdminOrFail();
    }
    
    /**
     * 
     * @param \NextDom\Controller\Render $render
     * @param array $pageContent
     * @return string
     */
    public function get(Render $render, array &$pageContent): string
    {

        $pageContent['JS_END_POOL'][] = '/public/js/desktop/params/eqlogic.js';
        $pageContent['JS_END_POOL'][] = '/public/js/adminlte/utils.js';

        return $render->get('/desktop/params/eqlogic.html.twig', $pageContent);
    }
    
}
