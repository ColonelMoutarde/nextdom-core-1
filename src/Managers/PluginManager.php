<?php
/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

/* This file is part of NextDom Software.
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

namespace NextDom\Managers;

use NextDom\Enums\DaemonState;
use NextDom\Enums\PluginManagerCron;
use NextDom\Helpers\FileSystemHelper;
use NextDom\Helpers\LogHelper;
use NextDom\Model\Entity\Plugin;

class PluginManager
{
    private static $cache = array();
    private static $enabledPlugins = null;

    /**
     * Get a plugin from his username
     *
     * @param string $id Identifiant du plugin
     *
     * @return mixed|Plugin Plugin
     *
     * @throws \Exception
     */
    public static function byId($id)
    {
        global $NEXTDOM_INTERNAL_CONFIG;
        if (is_string($id) && isset(self::$cache[$id])) {
            return self::$cache[$id];
        }
        if (!file_exists($id) || strpos($id, '/') === false) {
            $id = self::getPathById($id);
        }
        if (!file_exists($id)) {
            throw new \Exception('Plugin introuvable : ' . $id);
        }
        $data = json_decode(file_get_contents($id), true);
        if (!is_array($data)) {
            throw new \Exception('Plugin introuvable (json invalide) : ' . $id . ' => ' . print_r($data, true));
        }
        $plugin = new Plugin();
        $plugin->initPluginFromData($data);
        self::$cache[$plugin->getId()] = $plugin;
        if (!isset($NEXTDOM_INTERNAL_CONFIG['plugin']['category'][$plugin->getCategory()])) {
            foreach ($NEXTDOM_INTERNAL_CONFIG['plugin']['category'] as $key => $value) {
                if (!isset($value['alias'])) {
                    continue;
                }
                if (in_array($plugin->getCategory(), $value['alias'])) {
                    $plugin->setCategory($key);
                    break;
                }
            }
        }
        return $plugin;
    }

    /**
     * Get the path of the info.json file from the plugin ID
     *
     * @param string $id Plugin ID
     * @return string Path to the info.json file
     */
    public static function getPathById(string $id): string
    {
        return NEXTDOM_ROOT . '/plugins/' . $id . '/plugin_info/info.json';
    }

    /**
     * @param bool $activatedOnly
     * @param bool $nameOnly
     * @return array
     * @throws \Exception
     */
    public static function getPluginsByCategory(bool $activatedOnly = false, bool $nameOnly = false): array
    {
        return self::listPlugin($activatedOnly, true, $nameOnly);
    }

    /**
     * Get the list of plugins
     *
     * @param bool $activatedOnly Filter only activated plugins
     * @param bool $orderByCategory Sort by category
     * @param bool $nameOnly Get only plugin names
     * @return Plugin[] List of plugins
     *
     * @throws \Exception
     */
    public static function listPlugin(bool $activatedOnly = false, bool $orderByCategory = false, bool $nameOnly = false): array
    {
        $listPlugin = array();
        if ($activatedOnly) {
            $sql = "SELECT plugin
                    FROM `config`
                    WHERE `key` = 'active'
                    AND `value` = '1'";
            $queryResults = \DB::Prepare($sql, array(), \DB::FETCH_TYPE_ALL);
            if ($nameOnly) {
                foreach ($queryResults as $row) {
                    $listPlugin[] = $row['plugin'];
                }
                return $listPlugin;
            } else {
                foreach ($queryResults as $row) {
                    try {
                        $listPlugin[] = self::byId($row['plugin']);
                    } catch (\Throwable $e) {
                        LogHelper::addError('plugin', $e->getMessage(), 'pluginNotFound::' . $row['plugin']);
                    }
                }
            }
        } else {
            $rootPluginPath = NEXTDOM_ROOT . '/plugins';
            foreach (FileSystemHelper::ls($rootPluginPath, '*') as $dirPlugin) {
                if (is_dir($rootPluginPath . '/' . $dirPlugin)) {
                    $pathInfoPlugin = $rootPluginPath . '/' . $dirPlugin . 'plugin_info/info.json';
                    if (file_exists($pathInfoPlugin)) {
                        try {
                            $listPlugin[] = self::byId($pathInfoPlugin);
                        } catch (\Throwable $e) {
                            LogHelper::addError('plugin', $e->getMessage(), 'pluginNotFound::' . $pathInfoPlugin);
                        }
                    }
                }
            }
        }
        $returnValue = array();
        if ($orderByCategory) {
            if (count($listPlugin) > 0) {
                foreach ($listPlugin as $plugin) {
                    $category = $plugin->getCategory();
                    if ($category == '') {
                        $category = __('Autre');
                    }
                    if (!isset($returnValue[$category])) {
                        $returnValue[$category] = array();
                    }
                    $returnValue[$category][] = $plugin;
                }
                foreach ($returnValue as &$category) {
                    usort($category, 'plugin::orderPlugin');
                }
                ksort($returnValue);
            }
        } else {
            if (isset($listPlugin) && is_array($listPlugin) && count($listPlugin) > 0) {
                usort($listPlugin, 'plugin::orderPlugin');
                $returnValue = $listPlugin;
            }
        }
        return $returnValue;
    }

    /**
     * Comparaison entre 2 plugins pour un tri
     *
     * @param $firstPlugin
     * @param $secondPluginName
     *
     * @return int Résultat de la comparaison
     */
    public static function orderPlugin(Plugin $firstPlugin, Plugin $secondPluginName): int
    {
        return strcmp(strtolower($firstPlugin->getName()), strtolower($secondPluginName->getName()));
    }

    /**
     * @throws \Exception
     */
    public static function heartbeat()
    {
        foreach (self::listPlugin(true) as $plugin) {
            try {
                $heartbeat = ConfigManager::byKey('heartbeat::delay::' . $plugin->getId(), 'core', 0);
                if ($heartbeat == 0 || is_nan($heartbeat)) {
                    continue;
                }
                $eqLogics = EqLogicManager::byType($plugin->getId(), true);
                if (count($eqLogics) == 0) {
                    continue;
                }
                $ok = false;
                foreach ($eqLogics as $eqLogic) {
                    if ($eqLogic->getStatus('lastCommunication', date('Y-m-d H:i:s')) > date('Y-m-d H:i:s', strtotime('-' . $heartbeat . ' minutes' . date('Y-m-d H:i:s')))) {
                        $ok = true;
                        break;
                    }
                }
                if (!$ok) {
                    $message = __('Attention le plugin ') . ' ' . $plugin->getName();
                    $message .= __(' n\'a recu de message depuis ') . $heartbeat . __(' min');
                    $logicalId = 'heartbeat' . $plugin->getId();
                    MessageManager::add($plugin->getId(), $message, '', $logicalId);
                    if ($plugin->getHasOwnDeamon() && ConfigManager::byKey('heartbeat::restartDeamon::' . $plugin->getId(), 'core', 0) == 1) {
                        $plugin->deamon_start(true);
                    }
                }
            } catch (\Exception $e) {
            }
        }
    }

    /**
     * Tâche exécutée toutes les minutes
     *
     * @throws \Exception
     */
    public static function cron()
    {
        self::startCronTask(PluginManagerCron::CRON);
    }

    /**
     * Tâche exécutée toutes les 5 minutes
     *
     * @throws \Exception
     */
    public static function cron5()
    {
        self::startCronTask(PluginManagerCron::CRON_5);
    }

    /**
     * Tâche exécutée toutes les 15 minutes
     *
     * @throws \Exception
     */
    public static function cron15()
    {
        self::startCronTask(PluginManagerCron::CRON_15);
    }

    /**
     * Tâche exécutée toutes les 30 minutes
     *
     * @throws \Exception
     */
    public static function cron30()
    {
        self::startCronTask(PluginManagerCron::CRON_30);
    }

    /**
     * Task performed every day
     *
     * @throws \Exception
     */
    public static function cronDaily()
    {
        self::startCronTask(PluginManagerCron::CRON_DAILY);
    }

    /**
     * Tâche exécutée toutes les heures
     *
     * @throws \Exception
     */
    public static function cronHourly()
    {
        self::startCronTask(PluginManagerCron::CRON_HOURLY);
    }

    /**
     * Start a cron job
     *
     * @param string $cronType Cron job type, see PluginManagerCronEnum
     * // TODO Rajouter un test sur l'enum ???
     * @throws \Exception
     */
    private static function startCronTask(string $cronType = '')
    {
        $cache = CacheManager::byKey('plugin::' . $cronType . '::inprogress');
        if ($cache->getValue(0) > 3) {
            MessageManager::add('core', __('La tache plugin::' . $cronType . ' n\'arrive pas à finir à cause du plugin : ') . CacheManager::byKey('plugin::' . $cronType . '::last')->getValue() . __(' nous vous conseillons de désactiver le plugin et de contacter l\'auteur'));
        }
        CacheManager::set('plugin::' . $cronType . '::inprogress', $cache->getValue(0) + 1);
        foreach (self::listPlugin(true) as $plugin) {
            if (method_exists($plugin->getId(), $cronType)) {
                if (ConfigManager::byKey('functionality::cron::enable', $plugin->getId(), 1) == 1) {
                    $pluginId = $plugin->getId();
                    CacheManager::set('plugin::' . $cronType . '::last', $pluginId);
                    try {
                        $pluginId::$cronType();
                    } catch (\Throwable $e) {
                        LogHelper::add($pluginId, 'error', __('Erreur sur la fonction cron du plugin : ') . $e->getMessage());
                    }
                }
            }
        }
        CacheManager::set('plugin::' . $cronType . '::inprogress', 0);
    }

    /**
     * Start plugin daemons
     *
     * @throws \Exception
     */
    public static function start()
    {
        foreach (self::listPlugin(true) as $plugin) {
            $plugin->deamon_start(false, true);
            if (method_exists($plugin->getId(), 'start')) {
                $pluginId = $plugin->getId();
                try {
                    $pluginId::start();
                } catch (\Throwable $e) {
                    LogHelper::add($pluginId, 'error', __('Erreur sur la fonction start du plugin : ') . $e->getMessage());
                }
            }
        }
    }

    /**
     * Arrête les daemons des plugins
     *
     * @throws \Exception
     */
    public static function stop()
    {
        foreach (self::listPlugin(true) as $plugin) {
            $plugin->deamon_stop();
            if (method_exists($plugin->getId(), 'stop')) {
                $pluginId = $plugin->getId();
                try {
                    $pluginId::stop();
                } catch (\Throwable $e) {
                    LogHelper::add($pluginId, 'error', __('Erreur sur la fonction stop du plugin : ') . $e->getMessage());
                }
            }
        }
    }

    /**
     * Test le daemon TODO ??
     *
     * @throws \Exception
     */
    public static function checkDeamon()
    {
        foreach (self::listPlugin(true) as $plugin) {
            if (ConfigManager::byKey('deamonAutoMode', $plugin->getId(), 1) != 1) {
                continue;
            }
            $dependancy_info = $plugin->dependancy_info();
            if ($dependancy_info['state'] == DaemonState::NOT_OK) {
                try {
                    $plugin->dependancy_install();
                } catch (\Exception $e) {

                }
            } elseif ($dependancy_info['state'] == DaemonState::IN_PROGRESS && $dependancy_info['duration'] > $plugin->getMaxDependancyInstallTime()) {
                if (isset($dependancy_info['progress_file']) && file_exists($dependancy_info['progress_file'])) {
                    shell_exec('rm ' . $dependancy_info['progress_file']);
                }
                ConfigManager::save('deamonAutoMode', 0, $plugin->getId());
                LogHelper::add($plugin->getId(), 'error', __('Attention : l\'installation des dépendances a dépassé le temps maximum autorisé : ') . $plugin->getMaxDependancyInstallTime() . 'min');
            }
            try {
                $plugin->deamon_start(false, true);
            } catch (\Exception $e) {

            }
        }
    }

    /**
     * Test si le plugin est actif
     * TODO: Doit passer en static
     * @param $id
     * @return int
     * @throws \Exception
     */
    public static function isActive($id)
    {
        $result = 0;
        if (self::$enabledPlugins === null) {
            self::$enabledPlugins = ConfigManager::getEnabledPlugins();
        }
        if (isset(self::$enabledPlugins[$id])) {
            $result = self::$enabledPlugins[$id];
        }
        return $result;
    }
}
