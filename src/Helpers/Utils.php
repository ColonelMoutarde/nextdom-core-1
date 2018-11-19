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
 * NextDom Software is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with NextDom Software. If not, see <http://www.gnu.org/licenses/>.
 */

namespace NextDom\Helpers;

class Utils
{
    /**
     * Ajouter une variable Javascript au code HTML
     *
     * @param string $varName Nom de la variable dans le code HTML
     * @param mixed $varValue Valeur de la variable
     */
    public static function sendVarToJs(string $varName, $varValue)
    {
        echo self::getVarToJs($varName, $varValue);
    }

    public static function getVarToJs(string $varName, $varValue)
    {
        return "<script>" . self::getVarInJs($varName, $varValue) .  "</script>\n";
    }

    /**
     * Ajouter une liste de variables Javascript au HTML
     *
     * TODO: Merger les 2 avecs un test
     * @param array $listOfVarsWithValues Liste des variables 'nom' => 'valeur'
     */
    public static function sendVarsToJS(array $listOfVarsWithValues)
    {
        echo self::getVarsToJS($listOfVarsWithValues);
    }

    /**
     * Get HTML code of list a javascript variables.
     *
     * @param array $listOfVarsWithValues
     * @return string
     */
    public static function getVarsToJS(array $listOfVarsWithValues)
    {
        $result = "<script>\n";
        foreach ($listOfVarsWithValues as $varName => $value) {
            $result .= self::getVarInJs($varName, $value) . "\n";
        }
        $result .= "</script>\n";
        return $result;
    }

    /**
     * Prépare la déclaration d'une variable au format Javascript
     *
     * @param string $varName Nom de la variable
     * @param mixed $varValue Valeur
     *
     * @return string Déclaration javascript
     */
    private static function getVarInJs(string $varName, $varValue): string
    {
        $jsVarValue = '';
        if (is_array($varValue)) {
            $jsVarValue = 'jQuery.parseJSON("' . addslashes(json_encode($varValue, JSON_UNESCAPED_UNICODE)) . '")';
        } else {
            $jsVarValue = '"' . $varValue . '"';
        }
        return "var $varName = $jsVarValue;";
    }

    public static function getArrayToJQueryJson($varToTransform) {
        return 'jQuery.parseJSON("' . addslashes(json_encode($varToTransform, JSON_UNESCAPED_UNICODE)) . '")';
    }
    /**
     * Rediriger vers un autre url
     *
     * @param string $url URL cible
     * @param null $forceType Forcage si 'JS' TODO: ???
     */
    public static function redirect(string $url, $forceType = null)
    {
        if ($forceType == 'JS' || headers_sent() || isset($_GET['ajax'])) {
            echo '<script type="text/javascript">window.location.href="' . $url . '"</script>';
        } else {
            exit(header("Location: $url"));
        }
    }

    /**
     * Test si l'utilisateur est connecté avec certains droits
     *
     * @param string $rights Droits à tester (admin)
     *
     * @return boolean True si l'utilisateur est connecté avec les droits demandés
     */
    public static function isConnect(string $rights = ''): bool
    {
        $rightsKey        = 'isConnect::' . $rights;
        $isSetSessionUser = isset($_SESSION['user']);
        $result           = false;

        if ($isSetSessionUser && isset($GLOBALS[$rightsKey]) && $GLOBALS[$rightsKey]) {
            $result = $GLOBALS[$rightsKey];
        } else {
            
            if (session_status() == PHP_SESSION_DISABLED || !$isSetSessionUser) {
                $result = false;
            } elseif ($isSetSessionUser && is_object($_SESSION['user']) && $_SESSION['user']->is_Connected()) {
                
                if ($rights !== '') {
                    if ($_SESSION['user']->getProfils() == $rights) {
                        $result = true;
                    }
                } else {
                    $result = true;
                }
            }
            $GLOBALS[$rightsKey] = $result;
        }
        return $result;
    }

    /**
     * Obtenir une variable passée en paramètre
     *
     * @param string $name Nom de la variable
     * @param mixed $default Valeur par défaut
     *
     * @return mixed Valeur de la variable
     */
    public static function init(string $name, $default = '')
    {
        if (isset($_GET[$name])) {
            return $_GET[$name];
        }
        if (isset($_POST[$name])) {
            return $_POST[$name];
        }
        if (isset($_REQUEST[$name])) {
            return $_REQUEST[$name];
        }
        return $default;
    }

    /**
     * Obtenir le contenu d'un fichier template.
     *
     * @param string $folder Répertoire dans lequel se trouve le fichier de template
     * @param string $version Version du template
     * @param string $filename Nom du fichier
     * @param string $pluginId Identifiant du plugin
     *
     * @return string Contenu du fichier ou une chaine vide.
     */
    public static function getTemplateFilecontent(string $folder, string $version, string $filename, string $pluginId = ''): string
    {
        $result = '';
        $filePath = NEXTDOM_ROOT . '/plugins/' . $pluginId . '/core/template/' . $version . '/' . $filename . '.html';
        if ($pluginId == '') {
            $filePath = NEXTDOM_ROOT . '/' . $folder . '/template/' . $version . '/' . $filename . '.html';
        }
        if (file_exists($filePath)) {
            $result = file_get_contents($filePath);
        }
        return $result;
    }

    /**
     * Transforme une expression lisible en une expression analysable
     *
     * @param string $expression Expression lisible
     *
     * @return string Expression transformée
     */
    public static function transformExpressionForEvaluation(string $expression): string
    {

        $result = $expression;
        $replaceMap = [
            '=='  => '==',
            '='   => '==',
            '>='  => '>=',
            '<='  => '<=',
            '<==' => '<=',
            '>==' => '>=',
            '===' => '==',
            '!==' => '!=',
            '!='  => '!=',
            'OR'  => '||',
            'OU'  => '||',
            'or'  => '||',
            'ou'  => '||',
            '||'  => '||',
            'AND' => '&&',
            'ET'  => '&&',
            'and' => '&&',
            'et'  => '&&',
            '&&'  => '&&',
            '<'   => '<',
            '>'   => '>',
            '/'   => '/',
            '*'   => '*',
            '+'   => '+',
            '-'   => '-',
            ''    => ''
        ];
        preg_match_all('/(\w+|\d+|\.\d+|".*?"|\'.*?\'|\#.*?\#|\(|\))[ ]*([!*+&|\\-\\/>=<]+|and|or|ou|et)*[ ]*/i', $expression, $pregOutput);
        if (count($pregOutput) > 2) {
            $result = '';
            $exprIndex = 0;
            foreach ($pregOutput[1] as $expr) {
                $result .= $expr . $replaceMap[$pregOutput[2][$exprIndex++]];
            }
        }
        return $result;
    }

}
