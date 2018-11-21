<?php
/* This file is part of NextDom.
 *
 * NextDom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * NextDom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with NextDom. If not, see <http://www.gnu.org/licenses/>.
 */
namespace NextDom\Helpers;

use NextDom\Enums\ApiModeEnum;

class Api
{

    /**
     * Get API key from core or plugin
     *
     * @param string $plugin Plugin id
     *
     * @return string API key
     */
    public static function getApiKey(string $plugin = 'core'): string
    {
        if ($plugin == 'apipro') {
            if (\config::byKey('apipro') == '') {
                \config::save('apipro', \config::genKey());
            }
            return \config::byKey('apipro');
        }
        if ($plugin == 'apimarket') {
            if (\config::byKey('apimarket') == '') {
                \config::save('apimarket', \config::genKey());
            }
            return \config::byKey('apimarket');
        }
        if (\config::byKey('api', $plugin) == '') {
            \config::save('api', \config::genKey(), $plugin);
        }
        return \config::byKey('api', $plugin);
    }

    /**
     * Test if api are are enabled
     *
     * @param string $mode
     *
     * @return bool
     */
    public static function apiModeResult(string $mode = ApiModeEnum::API_ENABLE): bool
    {
        $result = true;
        switch ($mode) {
            case ApiModeEnum::API_DISABLE:
                $result = false;
                break;
            case ApiModeEnum::API_WHITEIP:
                $ip = getClientIp();
                $find = false;
                $whiteIps = explode(';', \config::byKey('security::whiteips'));
                if (\config::byKey('security::whiteips') != '' && count($whiteIps) > 0) {
                    foreach ($whiteIps as $whiteIp) {
                        if (netMatch($whiteIp, $ip)) {
                            $find = true;
                        }
                    }
                    if (!$find) {
                        $result = false;
                    }
                }
                break;
            case ApiModeEnum::API_LOCALHOST:
                if (getClientIp() != '127.0.0.1') {
                    $result = false;
                }
                break;
        }
        return $result;
    }

    /**
     * Get API access with key
     *
     * @param string $defaultApiKey
     * @param string $plugin
     * @return bool
     */
    public static function apiAccess(string $defaultApiKey = '', string $plugin = 'core')
    {
        $defaultApiKey = trim($defaultApiKey);
        if ($defaultApiKey == '') {
            return false;
        }
        if ($plugin != 'core' && $plugin != 'proapi' && !self::apiModeResult(\config::byKey('api::' . $plugin . '::mode', 'core', 'enable'))) {
            return false;
        }
        $apikey = self::getApiKey($plugin);
        if ($defaultApiKey != '' && $apikey == $defaultApiKey) {
            return true;
        }
        $user = \user::byHash($defaultApiKey);
        if (is_object($user)) {
            if ($user->getOptions('localOnly', 0) == 1 && !self::apiModeResult('whiteip')) {
                return false;
            }
            GLOBAL $_USER_GLOBAL;
            $_USER_GLOBAL = $user;
            \log::add('connection', 'info', __('core.api-connection') . $user->getLogin());
            return true;
        }
        return false;
    }
}
