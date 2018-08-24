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


use NextDom\Managers\CmdManager;
use NextDom\Managers\EqLogicManager;
use NextDom\Managers\JeeObjectManager;
use NextDom\Managers\PluginManager;
use NextDom\Managers\ScenarioExpressionManager;
use NextDom\Managers\ScenarioManager;
use NextDom\Managers\UpdateManager;

class Controller
{
    const routesList = [
        'dashboard-v2' => 'dashboardV2Page',
        'scenario' => 'scenarioPage',
        'administration' => 'administrationPage',
        'backup' => 'backupPage',
        'object' => 'objectPage',
        'message' => 'messagePage',
        'cron' => 'cronPage',
        'user' => 'userPage',
        'update' => 'updatePage',
        'system' => 'systemPage',
        'database' => 'databasePage',
        'display' => 'displayPage',
        'log' => 'logPage',
        'report' => 'reportPage',
        'plugin' => 'pluginPage',
        'custom' => 'customPage',
        'editor' => 'editorPage',
        'migration' => 'migrationPage',
        'history' => 'historyPage',
        'shutdown' => 'shutdownPage',
        'health' => 'healthPage',
        'profils' => 'profilsPage',
        'view' => 'viewPage',
        'view_edit' => 'viewEditPage',
        'eqAnalyze' => 'eqAnalyzePage',
        'eqAnalyse' => 'eqAnalyzePage',
        'aplan' => 'planPage',
        'aplan3d' => 'plan3dPage'
    ];

    /**
     * Get static method of page by his code
     *
     * @param string $page Page code
     *
     * @return mixed|null Static method or null
     */
    public static function getRoute(string $page)
    {
        $route = null;
        if (array_key_exists($page, self::routesList)) {
            $route = self::routesList[$page];
        }
        return $route;
    }

    /**
     * Render dashboard
     *
     * @param Render $render Render engine
     * @param array $pageContent Page data
     *
     * @return string Content of Dashboard V2 page
     *
     * @throws \NextDom\Exceptions\CoreException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public static function dashboardV2Page(Render $render, array &$pageContent): string
    {
        Status::initConnectState();
        Status::isConnectedAdminOrFail();
        $pageContent['JS_VARS']['SEL_OBJECT_ID'] = Utils::init('object_id');
        $pageContent['JS_VARS']['SEL_CATEGORY'] = Utils::init('category', 'all');
        $pageContent['JS_VARS']['SEL_TAG'] = Utils::init('tag', 'all');
        $pageContent['JS_VARS']['SEL_SUMMARY'] = Utils::init('summary');

        if ($pageContent['JS_VARS']['SEL_OBJECT_ID'] == '') {
            $object = JeeObjectManager::byId($_SESSION['user']->getOptions('defaultDashboardObject'));
        } else {
            $object = JeeObjectManager::byId(Utils::init('object_id'));
        }
        if (!is_object($object)) {
            $object = JeeObjectManager::rootObject();
        }
        if (!is_object($object)) {
            throw new \Exception(__('Aucun objet racine trouvé. Pour en créer un, allez dans Outils -> Objets.<br/> Si vous ne savez pas quoi faire ou que c\'est la première fois que vous utilisez Jeedom, n\'hésitez pas à consulter cette <a href="https://jeedom.github.io/documentation/premiers-pas/fr_FR/index" target="_blank">page</a> et celle-là si vous avez un pack : <a href="https://jeedom.com/start" target="_blank">page</a>'));
        }
        $pageContent['JS_VARS']['rootObjectId'] = $object->getId();

        $pageContent['dashboardDisplayObjectByDefault'] = $_SESSION['user']->getOptions('displayObjetByDefault');
        $pageContent['dashboardDisplayScenarioByDefault'] = $_SESSION['user']->getOptions('displayScenarioByDefault');
        $pageContent['dashboardCategory'] = $pageContent['JS_VARS']['SEL_CATEGORY'];
        $pageContent['dashboardTag'] = $pageContent['JS_VARS']['SEL_TAG'];
        $pageContent['dashboardCategories'] = \nextdom::getConfiguration('eqLogic:category', true);
        $pageContent['dashboardTags'] = EqLogicManager::getAllTags();
        $pageContent['dashboardObjectId'] = $pageContent['JS_VARS']['SEL_OBJECT_ID'];
        $pageContent['dashboardObject'] = $object;
        $pageContent['dashboardChildrenObjects'] = JeeObjectManager::buildTree($object);
        if ($pageContent['dashboardDisplayScenarioByDefault'] == 1) {
            $pageContent['dashboardScenarios'] = ScenarioManager::all();
        }
        $pageContent['JS_POOL'][] = '/desktop/js/dashboard.js';
        // A remettre une fois mise sous forme de thème
//        $pageContent['JS_POOL'][] = '/desktop/js/dashboard-v2.js';
        $pageContent['JS_POOL'][] = '/3rdparty/jquery.isotope/isotope.pkgd.min.js';
        $pageContent['JS_POOL'][] = '/3rdparty/jquery.multi-column-select/multi-column-select.js';

        return $render->get('/desktop/dashboard-v2.html.twig', $pageContent);
    }

    /**
     * Render scenario page
     *
     * @param Render $render Render engine
     * @param array $pageContent Page data
     *
     * @return string Content of scenario page
     *
     * @throws \NextDom\Exceptions\CoreException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public static function scenarioPage(Render $render, array &$pageContent): string
    {
        Status::initConnectState();
        Status::isConnectedAdminOrFail();

        $pageContent['scenarios'] = array();
        // TODO: A supprimé pour éviter la requête inutile
        $pageContent['scenarioCount'] = count(ScenarioManager::all());
        $pageContent['scenarios'][-1] = ScenarioManager::all(null);
        $pageContent['scenarioListGroup'] = ScenarioManager::listGroup();
        if (is_array($pageContent['scenarioListGroup'])) {
            foreach ($pageContent['scenarioListGroup'] as $group) {
                $pageContent['scenarios'][$group['group']] = ScenarioManager::all($group['group']);
            }
        }
        $pageContent['scenarioInactiveStyle'] = \nextdom::getConfiguration('eqLogic:style:noactive');
        $pageContent['scenarioEnabled'] = \config::byKey('enableScenario');
        $pageContent['scenarioAllObjects'] = JeeObjectManager::all();

        $pageContent['JS_END_POOL'][] = '/desktop/js/scenario.js';
        $pageContent['JS_END_POOL'][] = '/3rdparty/jquery.sew/jquery.caretposition.js';
        $pageContent['JS_END_POOL'][] = '/3rdparty/jquery.sew/jquery.sew.min.js';

        return $render->get('/desktop/scenario.html.twig', $pageContent);
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
    public static function administrationPage(Render $render, array &$pageContent): string
    {
        global $CONFIG;
        global $NEXTDOM_INTERNAL_CONFIG;

        Status::initConnectState();
        Status::isConnectedAdminOrFail();

        $pageContent['adminReposList'] = UpdateManager::listRepo();
        $keys = array('api', 'apipro', 'dns::token', 'market::allowDNS', 'market::allowBeta', 'market::allowAllRepo', 'ldap::enable', 'apimarket', 'product_name', 'security::bantime');
        foreach ($pageContent['adminReposList'] as $key => $value) {
            $keys[] = $key . '::enable';
        }
        $pageContent['adminConfigs'] = \config::byKeys($keys);
        $pageContent['JS_VARS']['ldapEnable'] = $pageContent['adminConfigs']['ldap::enable'];
        $pageContent['adminIsBan'] = \user::isBan();
        $pageContent['adminHardwareName'] = \nextdom::getHardwareName();
        $pageContent['adminHardwareKey'] = \nextdom::getHardwareKey();
        $pageContent['adminLastKnowDate'] = \cache::byKey('hour')->getValue();
        $pageContent['adminIsRescueMode'] = Status::isRescueMode();
        if (!$pageContent['adminIsRescueMode']) {
            $pageContent['adminPluginsList'] = [];
            $pluginsList = PluginManager::listPlugin(true);
            foreach ($pluginsList as $plugin) {
                $pluginApi = \config::byKey('api', $plugin->getId());
                if ($pluginApi !== '') {
                    $pluginData = [];
                    $pluginData['api'] = $pluginApi;
                    $pluginData['plugin'] = $plugin;
                    $pageContent['adminPluginsList'][] = $pluginData;
                }
            }
        }
        $pageContent['adminDbConfig'] = $CONFIG['db'];
        $pageContent['adminUseLdap'] = function_exists('ldap_connect');

        $pageContent['adminBannedIp'] = [];
        $cache = \cache::byKey('security::banip');
        $values = json_decode($cache->getValue('[]'), true);
        if (is_array($values) && count($values) > 0) {
            foreach ($values as $value) {
                $bannedData = [];
                $bannedData['ip'] = $value['ip'];
                $bannedData['startDate'] = date('Y-m-d H:i:s', $value['datetime']);
                if ($pageContent['adminConfigs']['security::bantime'] < 0) {
                    $bannedData['endDate'] = __('Jamais');
                } else {
                    $bannedData['endDate'] = date('Y-m-d H:i:s', $value['datetime'] + $pageContent['adminConfigs']['security::bantime']);
                }
                $pageContent['adminBannedIp'][] = $bannedData;
            }
        }

        $pageContent['adminNetworkInterfaces'] = [];
        foreach (\network::getInterfaces() as $interface) {
            $intData = [];
            $intData['name'] = $interface;
            $intData['mac'] = \network::getInterfaceMac($interface);
            $intData['ip'] = \network::getInterfaceIp($interface);
            $pageContent['adminNetworkInterfaces'][] = $intData;
        }
        $pageContent['adminDnsRun'] = \network::dns_run();
        $pageContent['adminNetworkExternalAccess'] = \network::getNetworkAccess('external');
        $pageContent['adminCategories'] = \nextdom::getConfiguration('eqLogic:category');
        $pageContent['adminStats'] = \cache::stats();
        $pageContent['adminCacheFolder'] = \cache::getFolder();
        $pageContent['adminMemCachedExists'] = class_exists('memcached');
        $pageContent['adminRedisExists'] = class_exists('redis');
        $pageContent['adminAlerts'] = $NEXTDOM_INTERNAL_CONFIG['alerts'];
        $pageContent['adminOthersLogs'] = array('scenario', 'plugin', 'market', 'api', 'connection', 'interact', 'tts', 'report', 'event');

        $pageContent['CSS_POOL'][] = '/desktop/css/administration.css';
        $pageContent['JS_END_POOL'][] = '/desktop/js/administration.js';

        return $render->get('/desktop/administration.html.twig', $pageContent);
    }

    /**
     * Render backup page
     *
     * @param Render $render Render engine
     * @param array $pageContent Page data
     *
     * @return string Content of backup page
     *
     * @throws \NextDom\Exceptions\CoreException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public static function backupPage(Render $render, array &$pageContent): string
    {
        Status::initConnectState();
        Status::isConnectedAdminOrFail();

        $pageContent['JS_VARS_RAW']['REPO_LIST'] = '[]';

        $pageContent['backupAjaxToken'] = \ajax::getToken();
        $pageContent['backupReposList'] = UpdateManager::listRepo();
        $pageContent['JS_END_POOL'][] = '/desktop/js/backup.js';

        return $render->get('/desktop/backup.html.twig', $pageContent);
    }

    /**
     * Render objects page
     *
     * @param Render $render Render engine
     * @param array $pageContent Page data
     *
     * @return string Content of objects page
     *
     * @throws \NextDom\Exceptions\CoreException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public static function objectPage(Render $render, array &$pageContent): string
    {
        Status::initConnectState();
        Status::isConnectedAdminOrFail();

        $pageContent['JS_VARS']['select_id'] = Utils::init('id', '-1');
        $pageContent['JS_END_POOL'][] = '/desktop/js/object.js';

        $pageContent['objectProductName'] = \config::byKey('product_name');
        $pageContent['objectList'] = JeeObjectManager::buildTree(null, false);
        $pageContent['objectSummary'] = \config::byKey('object:summary');

        return $render->get('/desktop/object.html.twig', $pageContent);
    }

    /**
     * Render message page
     *
     * @param Render $render Render engine
     * @param array $pageContent Page data
     *
     * @return string Content of render page
     *
     * @throws \NextDom\Exceptions\CoreException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public static function messagePage(Render $render, array &$pageContent): string
    {
        Status::initConnectState();
        Status::isConnectedOrFail();

        $pageContent['JS_END_POOL'][] = '/desktop/js/message.js';

        $pageContent['messageSelectedPlugin'] = Utils::init('plugin');
        if ($pageContent['messageSelectedPlugin'] != '') {
            $pageContent['messagesList'] = \message::byPlugin($pageContent['messageSelectedPlugin']);
        } else {
            $pageContent['messagesList'] = \message::all();
        }
        $pageContent['messagePluginsList'] = \message::listPlugin();
        return $render->get('/desktop/message.html.twig', $pageContent);
    }

    /**
     * Render cron page
     *
     * @param Render $render Render engine
     * @param array $pageContent Page data
     *
     * @return string Content of cron page
     *
     * @throws \NextDom\Exceptions\CoreException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public static function cronPage(Render $render, array &$pageContent): string
    {
        Status::initConnectState();
        Status::isConnectedAdminOrFail();

        $pageContent['cronEnabled'] = \config::byKey('enableCron');
        $pageContent['JS_END_POOL'][] = '/desktop/js/cron.js';
        return $render->get('/desktop/cron.html.twig', $pageContent);
    }

    /**
     * Render user page
     *
     * @param Render $render Render engine
     * @param array $pageContent Page data
     *
     * @return string Content of user page
     *
     * @throws \NextDom\Exceptions\CoreException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public static function userPage(Render $render, array &$pageContent): string
    {
        Status::initConnectState();
        Status::isConnectedAdminOrFail();

        $pageContent['userLdapEnabled'] = \config::byKey('ldap::enable');
        if ($pageContent['userLdapEnabled'] != '1') {
            $user = \user::byLogin('nextdom_support');
            $pageContent['userSupportExists'] = is_object($user);
        }
        $pageContent['userSessionsList'] = \listSession();
        $pageContent['usersList'] = \user::all();
        $pageContent['JS_VARS']['ldapEnable'] = $pageContent['userLdapEnabled'];
        $pageContent['JS_END_POOL'][] = '/desktop/js/user.js';

        return $render->get('/desktop/user.html.twig', $pageContent);
    }

    /**
     * Render update page
     *
     * @param Render $render Render engine
     * @param array $pageContent Page data
     *
     * @return string Content of update page
     *
     * @throws \NextDom\Exceptions\CoreException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public static function updatePage(Render $render, array &$pageContent): string
    {
        Status::initConnectState();
        Status::isConnectedAdminOrFail();

        $updates = array();
        foreach (UpdateManager::listCoreUpdate() as $udpate) {
            $updates[str_replace(array('.php', '.sql'), '', $udpate)] = str_replace(array('.php', '.sql'), '', $udpate);
        }
        usort($updates, 'version_compare');
        $pageContent['updatesList'] = array_reverse($updates);

        $pageContent['JS_END_POOL'][] = '/desktop/js/update.js';

        return $render->get('/desktop/update.html.twig', $pageContent);
    }

    /**
     * Render system page
     *
     * @param Render $render Render engine
     * @param array $pageContent Page data
     *
     * @return string Content of system page
     *
     * @throws \NextDom\Exceptions\CoreException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public static function systemPage(Render $render, array &$pageContent): string
    {
        Status::initConnectState();
        Status::isConnectedAdminOrFail();

        $pageData['systemCanSudo'] = \nextdom::isCapable('sudo');
        $pageContent['JS_END_POOL'][] = '/desktop/js/system.js';

        return $render->get('/desktop/system.html.twig', $pageContent);
    }

    /**
     * Render database page
     *
     * @param Render $render Render engine
     * @param array $pageContent Page data
     *
     * @return string Content of database page
     *
     * @throws \NextDom\Exceptions\CoreException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public static function databasePage(Render $render, array &$pageContent): string
    {
        Status::initConnectState();
        Status::isConnectedAdminOrFail();

        $pageContent['JS_END_POOL'][] = '/desktop/js/database.js';

        return $render->get('/desktop/database.html.twig', $pageContent);
    }

    /**
     * Render display page
     *
     * @param Render $render Render engine
     * @param array $pageContent Page data
     *
     * @return string Content of display page
     *
     * @throws \NextDom\Exceptions\CoreException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public static function displayPage(Render $render, array &$pageContent): string
    {
        Status::initConnectState();
        Status::isConnectedAdminOrFail();

        $pageContent['JS_END_POOL'][] = '/desktop/js/display.js';

        $nbEqlogics = 0;
        $nbCmds = 0;
        $objects = JeeObjectManager::all();
        $eqLogics = array();
        $cmds = array();
        $eqLogics[-1] = EqLogicManager::byObjectId(null, false);
        foreach ($eqLogics[-1] as $eqLogic) {
            $cmds[$eqLogic->getId()] = $eqLogic->getCmd();
            $nbCmds += count($cmds[$eqLogic->getId()]);
        }
        $nbEqlogics += count($eqLogics[-1]);
        foreach ($objects as $object) {
            $eqLogics[$object->getId()] = $object->getEqLogic(false, false);
            foreach ($eqLogics[$object->getId()] as $eqLogic) {
                $cmds[$eqLogic->getId()] = $eqLogic->getCmd();
                $nbCmds += count($cmds[$eqLogic->getId()]);
            }
            $nbEqlogics += count($eqLogics[$object->getId()]);
        }

        $pageContent['displayObjects'] = $objects;
        $pageContent['displayNbEqLogics'] = $nbEqlogics;
        $pageContent['displayNbCmds'] = $nbCmds;
        $pageContent['displayEqLogics'] = $eqLogics;
        $pageContent['displayCmds'] = $cmds;

        return $render->get('/desktop/display.html.twig', $pageContent);
    }

    /**
     * Render log page
     *
     * @param Render $render Render engine
     * @param array $pageContent Page data
     *
     * @return string Content of log page
     *
     * @throws \NextDom\Exceptions\CoreException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public static function logPage(Render $render, array &$pageContent): string
    {
        Status::initConnectState();
        Status::isConnectedAdminOrFail();

        $pageContent['JS_END_POOL'][] = '/desktop/js/log.js';
        $currentLogfile = Utils::init('logfile');
        $logFilesList = array();
        $dir = opendir(NEXTDOM_ROOT . '/log/');
        while ($file = readdir($dir)) {
            if ($file != '.' && $file != '..' && $file != '.htaccess' && !is_dir(NEXTDOM_ROOT . '/log/' . $file)) {
                $logFilesList[] = $file;
            }
        }
        natcasesort($logFilesList);
        $pageContent['logFilesList'] = [];
        foreach ($logFilesList as $logFile) {
            $logFileData = [];
            $logFileData['name'] = $logFile;
            $logFileData['icon'] = 'check';
            $logFileData['color'] = 'green';
            if (shell_exec('grep ERROR ' . NEXTDOM_ROOT . '/log/' . $logFile . ' | wc -l ') != 0) {
                $logFileData['icon'] = 'exclamation-triangle';
                $logFileData['color'] = 'red';
            } elseif (shell_exec('grep WARNING ' . NEXTDOM_ROOT . '/log/' . $logFile . ' | wc -l ') != 0) {
                $logFileData['icon'] = 'exclamation-circle';
                $logFileData['color'] = 'orange';
            }
            if ($currentLogfile == $logFile) {
                $logFileData['active'] = true;
            } else {
                $logFileData['active'] = false;
            }
            $logFileData['size'] = round(filesize(NEXTDOM_ROOT . '/log/' . $logFile) / 1024);
            $pageContent['logFilesList'][] = $logFileData;

        }
        return $render->get('/desktop/log.html.twig', $pageContent);
    }

    /**
     * Render report page
     *
     * @param Render $render Render engine
     * @param array $pageContent Page data
     *
     * @return string Content of report page
     *
     * @throws \NextDom\Exceptions\CoreException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public static function reportPage(Render $render, array &$pageContent): string
    {
        Status::initConnectState();
        Status::isConnectedAdminOrFail();

        $pageContent['JS_END_POOL'][] = '/desktop/js/report.js';

        $report_path = NEXTDOM_ROOT . '/data/report/';
        $pageContent['reportViews'] = [];
        foreach (\view::all() as $view) {
            $viewData = [];
            $viewData['id'] = $view->getId();
            $viewData['name'] = $view->getName();
            $viewData['number'] = count(ls($report_path . '/view/' . $view->getId(), '*'));
            $pageContent['reportViews'][] = $viewData;
        }
        $pageContent['reportPlans'] = [];
        foreach (\planHeader::all() as $plan) {
            $planData = [];
            $planData['id'] = $plan->getId();
            $planData['name'] = $plan->getName();
            $planData['number'] = count(ls($report_path . '/plan/' . $plan->getId(), '*'));
            $pageContent['reportPlans'][] = $planData;
        }
        $pageContent['reportPlugins'] = [];
        foreach (PluginManager::listPlugin(true) as $plugin) {
            if ($plugin->getDisplay() != '') {
                $pluginData = [];
                $pluginData['id'] = $plugin->getId();
                $pluginData['name'] = $plugin->getName();
                $pluginData['number'] = count(ls($report_path . '/plugin/' . $plugin->getId(), '*'));
                $pageContent['reportPlugins'][] = $pluginData;
            }
        }
        return $render->get('/desktop/report.html.twig', $pageContent);
    }

    /**
     * Render plugin page
     *
     * @param Render $render Render engine
     * @param array $pageContent Page data
     *
     * @return string Content of plugin page
     *
     * @throws \NextDom\Exceptions\CoreException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public static function pluginPage(Render $render, array &$pageContent): string
    {
        Status::initConnectState();
        Status::isConnectedAdminOrFail();

        $pageContent['JS_END_POOL'][] = '/desktop/js/plugin.js';
        $pageContent['JS_VARS']['sel_plugin_id'] = Utils::init('id', '-1');
        $pageContent['pluginsList'] = PluginManager::listPlugin();
        $pageContent['pluginReposList'] = [];
        foreach (UpdateManager::listRepo() as $repoCode => $repoData) {
            if ($repoData['enable'] && isset($repoData['scope']['hasStore']) && $repoData['scope']['hasStore']) {
                $pageContent['pluginReposList'][$repoCode] = $repoData;
            }
        }
        $pageContent['pluginInactiveOpacity'] = \nextdom::getConfiguration('eqLogic:style:noactive');
        return $render->get('/desktop/plugin.html.twig', $pageContent);
    }

    /**
     * Render custom page
     *
     * @param Render $render Render engine
     * @param array $pageContent Page data
     *
     * @return string Content of custom page
     *
     * @throws \NextDom\Exceptions\CoreException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public static function customPage(Render $render, array &$pageContent): string
    {
        Status::initConnectState();
        Status::isConnectedAdminOrFail();

        $pageContent['customProductName'] = \config::byKey('product_name');
        $pageContent['customJS'] = '';
        if (file_exists(NEXTDOM_ROOT . '/custom/custom.js')) {
            $pageContent['customJS'] = trim(file_get_contents(NEXTDOM_ROOT . '/custom/custom.js'));
        }
        $pageContent['customCSS'] = '';
        if (file_exists(NEXTDOM_ROOT . '/custom/custom.css')) {
            $pageContent['customCSS'] = trim(file_get_contents(NEXTDOM_ROOT . '/custom/custom.css'));
        }
        $pageContent['customMobileJS'] = '';
        if (file_exists(NEXTDOM_ROOT . '/mobile/custom/custom.js')) {
            $pageContent['customMobileJS'] = trim(file_get_contents(NEXTDOM_ROOT . '/mobile/custom/custom.js'));
        }
        $pageContent['customMobileCSS'] = '';
        if (file_exists(NEXTDOM_ROOT . '/mobile/custom/custom.css')) {
            $pageContent['customMobileCSS'] = trim(file_get_contents(NEXTDOM_ROOT . '/mobile/custom/custom.css'));
        }

        $pageContent['JS_END_POOL'][] = '/desktop/js/custom.js';

        return $render->get('/desktop/custom.html.twig', $pageContent);
    }

    /**
     * Render editor page
     *
     * @param Render $render Render engine
     * @param array $pageContent Page data
     *
     * @return string Content of editor page
     *
     * @throws \NextDom\Exceptions\CoreException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public static function editorPage(Render $render, array &$pageContent): string
    {
        Status::initConnectState();
        Status::isConnectedAdminOrFail();

        $pageContent['JS_VARS']['rootPath'] = NEXTDOM_ROOT;

        $pageContent['editorFolders'] = [];
        $pageContent['editorRootPath'] = NEXTDOM_ROOT;

        foreach (\ls(NEXTDOM_ROOT, '*', false, array('folders')) as $folder) {
            $pageContent['editorFolders'][] = $folder;
        }
        $pageContent['JS_END_POOL'][] = '/desktop/js/editor.js';

        return $render->get('/desktop/editor.html.twig', $pageContent);
    }

    /**
     * Render migration page
     *
     * @param Render $render Render engine
     * @param array $pageContent Page data
     *
     * @return string Content of migration page
     *
     * @throws \NextDom\Exceptions\CoreException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public static function migrationPage(Render $render, array &$pageContent): string
    {
        Status::initConnectState();
        Status::isConnectedAdminOrFail();

        $pageContent['migrationAjaxToken'] = \ajax::getToken();
        $pageContent['JS_END_POOL'][] = '/desktop/js/migration.js';

        return $render->get('/desktop/migration.html.twig', $pageContent);
    }

    /**
     * Render history page
     *
     * @param Render $render Render engine
     * @param array $pageContent Page data
     *
     * @return string Content of history page
     *
     * @throws \NextDom\Exceptions\CoreException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public static function historyPage(Render $render, array &$pageContent): string
    {
        Status::initConnectState();
        Status::isConnectedAdminOrFail();

        $pageContent['historyDate'] = array(
            'start' => date('Y-m-d', strtotime(\config::byKey('history::defautShowPeriod') . ' ' . date('Y-m-d'))),
            'end' => date('Y-m-d'),
        );

        $pageContent['historyCmdsList'] = CmdManager::allHistoryCmd();
        $pageContent['historyPluginsList'] = PluginManager::listPlugin();
        $pageContent['historyEqLogicCategories'] = \nextdom::getConfiguration('eqLogic:category');
        $pageContent['historyObjectsList'] = JeeObjectManager::all();

        $pageContent['JS_POOL'][] = '/3rdparty/visjs/vis.min.js';
        $pageContent['CSS_POOL'][] = '/3rdparty/visjs/vis.min.css';
        $pageContent['JS_END_POOL'][] = '/desktop/js/history.js';

        return $render->get('/desktop/history.html.twig', $pageContent);
    }

    /**
     * Render shutdown page
     *
     * @param Render $render Render engine
     * @param array $pageContent Page data
     *
     * @return string Content of shutdown page
     *
     * @throws \NextDom\Exceptions\CoreException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public static function shutdownPage(Render $render, array &$pageContent): string
    {
        Status::initConnectState();
        Status::isConnectedAdminOrFail();
        return $render->get('/desktop/shutdown.html.twig', $pageContent);
    }

    /**
     * Render health page
     *
     * @param Render $render Render engine
     * @param array $pageContent Page data
     *
     * @return string Content of health page
     *
     * @throws \NextDom\Exceptions\CoreException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public static function healthPage(Render $render, array &$pageContent): string
    {
        Status::initConnectState();
        Status::isConnectedAdminOrFail();

        $pageContent['healthInformations'] = \nextdom::health();
        $pageContent['healthPluginsInformations'] = [];
        $pageContent['healthPluginDataToShow'] = false;
        $pageContent['healthTotalNOk'] = 0;
        $pageContent['healthTotalPending'] = 0;
        foreach (PluginManager::listPlugin(true) as $plugin) {
            $pluginData = [];
            if (file_exists(dirname(PluginManager::getPathById($plugin->getId())) . '/../desktop/modal/health.php')) {
                $pluginData['hasSpecificHealth'] = true;
            }
            if ($plugin->getHasDependency() == 1 || $plugin->getHasOwnDeamon() == 1 || method_exists($plugin->getId(), 'health') || $pluginData['hasSpecificHealth']) {
                $pageContent['healthPluginDataToShow'] = true;
                $pluginData['plugin'] = $plugin;
                $pluginData['port'] = false;
                $pluginData['nOk'] = 0;
                $pluginData['pending'] = 0;
                $pluginData['hasDependency'] = false;
                $pluginData['hasOwnDaemon'] = false;
                $pluginData['showOnlyTable'] = false;

                $port = \config::byKey('port', $plugin->getId());
                if ($port != '') {
                    $pluginData['port'] = $port;
                }
                if ($plugin->getHasDependency() == 1 || $plugin->getHasOwnDeamon() == 1 || method_exists($plugin->getId(), 'health')) {
                    $pluginData['showOnlyTable'] = true;
                }
                if ($plugin->getHasDependency() == 1) {
                    $pluginData['hasDependency'] = true;
                    $dependencyInfo = $plugin->dependancy_info();
                    if (isset($dependencyInfo['state'])) {
                        $pluginData['dependencyState'] = $dependencyInfo['state'];
                        if ($pluginData['dependencyState'] == 'nok') {
                            $pluginData['nOk']++;
                        } elseif ($pluginData['dependencyState'] == 'in_progress') {
                            $pluginData['pending']++;
                        } elseif ($pluginData['dependencyState'] != 'ok') {
                            $pluginData['nOk']++;
                        }
                    }
                }
                if ($plugin->getHasOwnDeamon() == 1) {
                    $pluginData['hasOwnDaemon'] = true;
                    $daemonInfo = $plugin->deamon_info();
                    $pluginData['daemonAuto'] = $daemonInfo['auto'];
                    if (isset($daemonInfo['launchable'])) {
                        $pluginData['daemonLaunchable'] = $daemonInfo['launchable'];
                        if ($pluginData['daemonLaunchable'] == 'nok' && $pluginData['daemonAuto'] == 1) {
                            $pluginData['nOk']++;
                        }
                    }
                    $pluginData['daemonLaunchableMessage'] = $daemonInfo['launchable_message'];
                    $pluginData['daemonState'] = $daemonInfo['state'];
                    if ($pluginData['daemonState'] == 'nok' && $pluginData['daemonAuto'] == 1) {
                        $pluginData['nOk']++;
                    }
                }

                if (method_exists($plugin->getId(), 'health')) {
                    $pluginData['health'] = [];
                    // Je vois pas quand ça peut être appelé
                    foreach ($plugin->getId()::health() as $result) {
                        $pluginData['health'][] = [
                            'test' => $result['test'],
                            'state' => $result['state'],
                            'advice' => $result['advice']
                        ];
                        if ($result['state'] == 'nok') {
                            $pluginData['nOk'] = true;
                        }
                    }
                }

                if ($pluginData['nOk'] > 0) {
                    $pageContent['healthTotalNOk']++;
                }
                if ($pluginData['pending'] > 0) {
                    $pageContent['healthTotalPending']++;
                }
                $pageContent['healthPluginsInformations'][] = $pluginData;
            }
        }


        $pageContent['JS_END_POOL'][] = '/desktop/js/health.js';

        return $render->get('/desktop/health.html.twig', $pageContent);
    }

    /**
     * Render profils page
     *
     * @param Render $render Render engine
     * @param array $pageContent Page data
     *
     * @return string Content of profils page
     *
     * @throws \NextDom\Exceptions\CoreException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public static function profilsPage(Render $render, array &$pageContent): string
    {
        Status::initConnectState();
        Status::isConnectedOrFail();

        @session_start();
        $_SESSION['user']->refresh();
        @session_write_close();
        $pageContent['profilsHomePage'] = array(
            'core::dashboard' => __('Dashboard'),
            'core::view' => __('Vue'),
            'core::plan' => __('Design'),
        );
        foreach (PluginManager::listPlugin() as $pluginList) {
            if ($pluginList->isActive() == 1 && $pluginList->getDisplay() != '') {
                $pageContent['profilsHomePage'][$pluginList->getId() . '::' . $pluginList->getDisplay()] = $pluginList->getName();
            }
        }
        $pageContent['profilsUser'] = $_SESSION['user'];
        $pageContent['profilsSessionsList'] = listSession();
        $pageContent['profilsDesktopThemes'] = [];
        foreach (ls(NEXTDOM_ROOT . '/css/themes') as $dir) {
            if (is_dir(NEXTDOM_ROOT . '/css/themes/' . $dir . '/desktop')) {
                $pageContent['profilsDesktopThemes'][] = trim($dir, '/');
            }
        }
        $pageContent['profilsMobileThemes'] = [];
        foreach (ls(NEXTDOM_ROOT . '/css/themes') as $dir) {
            if (is_dir(NEXTDOM_ROOT . '/css/themes' . $dir . '/mobile')) {
                $pageContent['profilsDesktopThemes'][] = trim($dir, '/');
            }
        }
        $pageContent['profilsDisplayTypes'] = \nextdom::getConfiguration('eqLogic:displayType');
        $pageContent['profilsJeeObjects'] = JeeObjectManager::all();
        $pageContent['profilsViews'] = \view::all();
        $pageContent['profilsPlans'] = \planHeader::all();
        $pageContent['profilsAllowRemoteUsers'] = \config::byKey('sso:allowRemoteUser');

        $pageContent['JS_END_POOL'][] = '/desktop/js/profils.js';

        return $render->get('/desktop/profils.html.twig', $pageContent);
    }

    /**
     * Render view page
     *
     * @param Render $render Render engine
     * @param array $pageContent Page data
     *
     * @return string Content of view page
     *
     * @throws \NextDom\Exceptions\CoreException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public static function viewPage(Render $render, array &$pageContent): string
    {

        Status::initConnectState();
        Status::isConnectedOrFail();

        $pageContent['viewsList'] = \view::all();
        $pageContent['viewHideList'] = true;
        $pageContent['viewIsAdmin'] = Status::isConnectAdmin();
        $pageContent['viewDefault'] = $_SESSION['user']->getOptions('displayViewByDefault');
        $pageContent['viewNoControl'] = Utils::init('noControl');

        $currentView = null;
        if (Utils::init('view_id') == '') {
            if ($_SESSION['user']->getOptions('defaultDesktopView') != '') {
                $currentView = \view::byId($_SESSION['user']->getOptions('defaultDesktopView'));
            }
            if (!is_object($currentView)) {
                $currentView = $pageContent['viewsList'][0];
            }
        } else {
            $currentView = \view::byId(init('view_id'));
            if (!is_object($currentView)) {
                throw new \Exception('{{Vue inconnue. Vérifier l\'ID.}}');
            }
        }
        if (!is_object($currentView)) {
            throw new \Exception(__('Aucune vue n\'existe, cliquez <a href="index.php?v=d&p=view_edit">ici</a> pour en créer une.'));
        }
        $pageContent['viewCurrent'] = $currentView;
        if ($_SESSION['user']->getOptions('displayViewByDefault') == 1 && Utils::init('report') != 1) {
            $pageContent['viewHideList'] = false;
        }
        $pageContent['JS_VARS']['view_id'] = $currentView->getId();

        $pageContent['JS_END_POOL'][] = '/desktop/js/view.js';

        return $render->get('/desktop/view.html.twig', $pageContent);
    }

    /**
     * Render view edit page
     *
     * @param Render $render Render engine
     * @param array $pageContent Page data
     *
     * @return string Content of view edit page
     *
     * @throws \NextDom\Exceptions\CoreException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public static function viewEditPage(Render $render, array &$pageContent): string
    {
        Status::initConnectState();
        Status::isConnectedOrFail();

        $pageContent['viewEditViewsList'] = \view::all();

        $pageContent['JS_END_POOL'][] = '/desktop/js/view_edit.js';

        return $render->get('/desktop/view_edit.html.twig', $pageContent);
    }

    /**
     * Render eqLogic analyze page
     *
     * @param Render $render Render engine
     * @param array $pageContent Page data
     *
     * @return string Content of eqLogic analyze page
     *
     * @throws \NextDom\Exceptions\CoreException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public static function eqAnalyzePage(Render $render, array &$pageContent): string
    {
        Status::initConnectState();
        Status::isConnectedOrFail();
        global $NEXTDOM_INTERNAL_CONFIG;
        $pageContent['eqAnalyzeEqLogicList'] = [];
        foreach (EqLogicManager::all() as $eqLogic) {
            $battery_type = str_replace(array('(', ')'), array('', ''), $eqLogic->getConfiguration('battery_type', ''));
            if ($eqLogic->getStatus('battery', -2) != -2) {
                $pageContent['eqAnalyzeEqLogicList'][] = $eqLogic;
            }
        }
        usort($pageContent['eqAnalyzeEqLogicList'], function ($a, $b) {
            $result = 0;
            if ($a->getStatus('battery') < $b->getStatus('battery')) {
                $result = -1;
            } elseif ($a->getStatus('battery') > $b->getStatus('battery')) {
                $result = 1;
            }
            return $result;
        });


        $cmdDataArray = [];
        foreach (EqLogicManager::all() as $eqLogic) {
            $cmdData = [];
            $cmdData['eqLogic'] = $eqLogic;
            $cmdData['infoCmds'] = [];
            $cmdData['actionCmds'] = [];

            foreach ($eqLogic->getCmd('info') as $cmd) {
                if (count($cmd->getConfiguration('actionCheckCmd', array())) > 0) {
                    $data = [];
                    $data['cmd'] = $cmd;
                    $data['actions'] = [];
                    foreach ($cmd->getConfiguration('actionCheckCmd') as $actionCmd) {
                        $data['actions'][] = ScenarioExpressionManager::humanAction($actionCmd);
                    }
                    $cmdData['infoCmds'][] = $data;
                }
            }
            foreach ($eqLogic->getCmd('action') as $cmd) {
                $actionCmdData = [];
                $actionCmdData['cmd'] = $cmd;

                if (count($cmd->getConfiguration('nextdomPreExecCmd', array())) > 0) {
                    $actionCmdData['preExecCmds'] = [];
                    foreach ($cmd->getConfiguration('nextdomPreExecCmd') as $actionCmd) {
                        $actionCmdData['preExecCmds'][] = ScenarioExpressionManager::humanAction($actionCmd);
                    }
                }
                if (count($cmd->getConfiguration('nextdomPostExecCmd', array())) > 0) {
                    $actionCmdData['postExecCmds'] = [];
                    foreach ($cmd->getConfiguration('nextdomPostExecCmd') as $actionCmd) {
                        $actionCmdData['postExecCmds'][] = ScenarioExpressionManager::humanAction($actionCmd);
                    }
                }
                $cmdData['actionCmds'][] = $actionCmdData;
            }
            $cmdDataArray[] = $cmdData;
        }
        $pageContent['eqAnalyzeCmdData'] = $cmdDataArray;
//TODO: Imbriquer les boucles quand le fonctionnement sera sûr
        $pageContent['eqAnalyzeAlerts'] = [];
        foreach (EqLogicManager::all() as $eqLogic) {
            $hasSomeAlerts = 0;
            $listCmds = array();
            foreach ($eqLogic->getCmd('info') as $cmd) {
                foreach ($NEXTDOM_INTERNAL_CONFIG['alerts'] as $level => $value) {
                    if ($value['check']) {
                        if ($cmd->getAlert($level . 'if', '') != '') {
                            $hasSomeAlerts += 1;
                            if (!in_array($cmd, $listCmds)) {
                                $listCmds[] = $cmd;
                            }
                        }
                    }
                }
            }
            if ($eqLogic->getConfiguration('battery_warning_threshold', '') != '') {
                $hasSomeAlerts += 1;
            }
            if ($eqLogic->getConfiguration('battery_danger_threshold', '') != '') {
                $hasSomeAlerts += 1;
            }
            if ($eqLogic->getTimeout('')) {
                $hasSomeAlerts += 1;
            }
            if ($hasSomeAlerts != 0) {
                $alertData = [];
                $alertData['eqLogic'] = $eqLogic;
                foreach ($listCmds as $cmdalert) {
                    foreach ($NEXTDOM_INTERNAL_CONFIG['alerts'] as $level => $value) {
                        if ($value['check']) {
                            if ($cmdalert->getAlert($level . 'if', '') != '') {
                                $during = '';
                                if ($cmdalert->getAlert($level . 'during', '') == '') {
                                    $during = ' effet immédiat';
                                }
                                else {
                                    $during = ' pendant plus de ' . $cmdalert->getAlert($level . 'during', '') . ' minute(s)';
                                }
                                $alertData['msg'] = ucfirst($level) . ' si ' . \nextdom::toHumanReadable(str_replace('#value#', '<b>' . $cmdalert->getName() . '</b>', $cmdalert->getAlert($level . 'if', ''))) . $during . '</br>';
                            }
                        }
                    }
                }
                $pageContent['eqAnalyzeAlerts'][] = $alertData;
            }
        }

        $pageContent['eqAnalyzeNextDomDeadCmd'] = \nextdom::deadCmd();
        $pageContent['eqAnalyzeCmdDeadCmd'] = CmdManager::deadCmd();
        $pageContent['eqAnalyzeJeeObjectDeadCmd'] = JeeObjectManager::deadCmd();
        $pageContent['eqAnalyzeScenarioDeadCmd'] = ScenarioManager::consystencyCheck(true);
        $pageContent['eqAnalyzeInteractDefDeadCmd'] = \interactDef::deadCmd();
        $pageContent['eqAnalyzePluginDeadCmd'] = [];
        foreach(PluginManager::listPlugin(true) as $plugin) {
            $pluginId = $plugin->getId();
            if (method_exists($pluginId, 'deadCmd')) {
                $pageContent['eqAnalyzePluginDeadCmd'][] = $pluginId::deadCmd();
            }
        }
        $pageContent['JS_END_POOL'][] = '/desktop/js/eqAnalyse.js';

        return $render->get('/desktop/eqAnalyze.html.twig', $pageContent);
    }

    /**
     * Render plan page
     *
     * @param Render $render Render engine
     * @param array $pageContent Page data
     *
     * @return string Content of plan page
     *
     * @throws \NextDom\Exceptions\CoreException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public static function planPage(Render $render, array &$pageContent): string
    {
        Status::initConnectState();
        Status::isConnectedOrFail();


        $planHeader = null;
        $planHeaders = \planHeader::all();
        $planHeadersSendToJS = array();
        foreach ($planHeaders as $planHeader_select) {
            $planHeadersSendToJS[] = array('id' => $planHeader_select->getId(), 'name' => $planHeader_select->getName());
        }
        $pageContent['JS_VARS_RAW']['planHeader'] = Utils::getArrayToJQueryJson($planHeadersSendToJS);
        if (Utils::init('plan_id') == '') {
            foreach ($planHeaders as $planHeader_select) {
                if ($planHeader_select->getId() == $_SESSION['user']->getOptions('defaultDashboardPlan')) {
                    $planHeader = $planHeader_select;
                    break;
                }
            }
        } else {
            foreach ($planHeaders as $planHeader_select) {
                if ($planHeader_select->getId() == Utils::init('plan_id')) {
                    $planHeader = $planHeader_select;
                    break;
                }
            }
        }
        if (!is_object($planHeader) && count($planHeaders) > 0) {
            $planHeader = $planHeaders[0];
        }
        if (!is_object($planHeader)) {
            $pageContent['planHeaderError'] = true;
            $pageContent['JS_VARS']['planHeader_id'] = -1;
        } else {
            $pageContent['planHeaderError'] = false;
            $pageContent['JS_VARS']['planHeader_id'] = $planHeader->getId();
        }

        $pageContent['JS_END_POOL'][] = '/desktop/js/plan.js';

        return $render->get('/desktop/plan.html.twig', $pageContent);
    }

    /**
     * Render 3d plan page
     *
     * @param Render $render Render engine
     * @param array $pageContent Page data
     *
     * @return string Content of 3d plan page
     *
     * @throws \NextDom\Exceptions\CoreException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public static function plan3dPage(Render $render, array &$pageContent): string
    {
        Status::initConnectState();
        Status::isConnectedOrFail();

        $plan3dHeader = null;
        $list_plan3dHeader = \plan3dHeader::all();
        if (Utils::init('plan3d_id') == '') {
            if ($_SESSION['user']->getOptions('defaultDesktopPlan3d') != '') {
                $plan3dHeader = \plan3dHeader::byId($_SESSION['user']->getOptions('defaultDesktopPlan3d'));
            }
            if (!is_object($plan3dHeader)) {
                $plan3dHeader = $list_plan3dHeader[0];
            }
        } else {
            $plan3dHeader = \plan3dHeader::byId(Utils::init('plan3d_id'));
            if (!is_object($plan3dHeader)) {
                $plan3dHeader = $list_plan3dHeader[0];
            }
        }
        if (is_object($plan3dHeader)) {
            $pageContent['JS_VARS']['plan3dHeader_id'] = $plan3dHeader->getId();
            $pageContent['plan3dCurrentHeaderId'] = $plan3dHeader->getId();
        } else {
            $pageContent['JS_VARS']['plan3dHeader_id'] = -1;
        }
        $pageContent['plan3dHeader'] = \plan3dHeader::all();
        $pageContent['plan3dFullScreen'] = Utils::init('fullscreen') == 1;

        $pageContent['JS_END_POOL'][] = '/3rdparty/three.js/three.min.js';
        $pageContent['JS_END_POOL'][] = '/3rdparty/three.js/loaders/LoaderSupport.js';
        $pageContent['JS_END_POOL'][] = '/3rdparty/three.js/loaders/OBJLoader.js';
        $pageContent['JS_END_POOL'][] = '/3rdparty/three.js/loaders/MTLLoader.js';
        $pageContent['JS_END_POOL'][] = '/3rdparty/three.js/controls/TrackballControls.js';
        $pageContent['JS_END_POOL'][] = '/3rdparty/three.js/controls/OrbitControls.js';
        $pageContent['JS_END_POOL'][] = '/3rdparty/three.js/renderers/Projector.js';
        $pageContent['JS_END_POOL'][] = '/3rdparty/three.js/objects/Sky.js';
        $pageContent['JS_END_POOL'][] = '/desktop/js/plan3d.js';

        return $render->get('/desktop/plan3d.html.twig', $pageContent);
    }
}