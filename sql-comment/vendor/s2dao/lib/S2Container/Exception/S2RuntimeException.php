<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
// +----------------------------------------------------------------------+
// | PHP version 5                                                        |
// +----------------------------------------------------------------------+
// | Copyright 2005-2007 the Seasar Foundation and the Others.            |
// +----------------------------------------------------------------------+
// | Licensed under the Apache License, Version 2.0 (the "License");      |
// | you may not use this file except in compliance with the License.     |
// | You may obtain a copy of the License at                              |
// |                                                                      |
// |     http://www.apache.org/licenses/LICENSE-2.0                       |
// |                                                                      |
// | Unless required by applicable law or agreed to in writing, software  |
// | distributed under the License is distributed on an "AS IS" BASIS,    |
// | WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND,                        |
// | either express or implied. See the License for the specific language |
// | governing permissions and limitations under the License.             |
// +----------------------------------------------------------------------+
// | Authors: klove                                                       |
// +----------------------------------------------------------------------+
//
// $Id: $
namespace S2Container\Exception;
/**
 * @package org.seasar.framework.exception
 * @author klove
 */
class S2RuntimeException extends \Exception {
    private $messageCode_;
    private $args_;
    private $message_;
    private $simpleMessage_;
    private static $msgMap_ = 
        ['ESSR0001' => '{0} not found',
        'ESSR0003' => '[{0}] unexpected:[{1}]',
        'ESSR0007' => '{0} should not be null or empty',
        'ESSR0017' => 'Exception occurred, because {0}',
        'ESSR0043' => 'The target which {0} invoked is illegal, because {1}',
        'ESSR0045' => 'Two or more components[{1}] are registered in {0}',
        'ESSR0046' => 'Component[{0}] not found',
        'ESSR0047' => 'The circulation reference was occurred in {0}',
        'ESSR0049' => 'The method[{1}] of {0} not found',
        'ESSR0057' => 'Method[{1}] of class[{0}] not found',
        'ESSR0058' => 'Can not configure constructor of {0}, because {1}',
        'ESSR0059' => 'Can not configure property[{1}] of {0}, because {2}',
        'ESSR0060' => 'Can not configure method[{1}] of {0}, because {2}',
        'ESSR0065' => 'Property[{1}] of class[{0}] not found',
        'ESSR0069' => 'Actual class [{1}] is not applicable in defined class [{0}]',
        'ESSR0070' => 'Field[{1}] of class[{0}] not found',
        'ESSR0075' => 'The container of [{0}] has not been registered yet.',
        'ESSR0076' => 'The circulation include was occurred in {0}, pathway {1}',
        'ESSR0081' => 'Illegal InitMethod annotation({1}) of class({0})',
        'ESSR1001' => 'Invalid xml {0}',
        'ESSR1002' => 'Illegal argument {0}',
        'ESSR1003' => 'Unsupported operation {0}',
        'ESSR1004' => 'Instantiation error',
        'ESSR1005' => '{0} [{1}] exists. but not [{2}] instance. ignored.',
        'ESSR1006' => '{0} is not readable.',
        'ESSR1007' => 'Constant[{1}] of class[{0}] not found',
        'ESSR1008' => 'No expression nor component ReflectionClass not found. component name : [{0}] component class name : [{1}]',
        'ESSR1009' => 'The target which {0} invoked is illegal, because target is not object. Target class is [{1}].',
        'ESSR1010' => 'Target[{0}] is not object. Target class[{1}] is not ReflectionClass.',
        'ESSR1011' => 'Container builder class[{1}] for extension[{0}] not implements S2ContainerBuilder interface.',
        'ESSR1012' => 'Container builder class not found for extension[{0}].',
        'ESSR1013' => 'Can not aspect to abstract class having abstract protected method. [{0}::{1}()].'];

    /**
     *
     */
    public function __construct($messageCode, $args = null, $cause = null) {
        $cause instanceof Exception ? $msg = $cause->getMessage() . "\n" : $msg = "";
        $msg .= self::getMessageWithArgs($messageCode, $args);
        parent::__construct($msg);
    }

    /**
     * @param string message id code
     * @params array message words
     */
    private static function getMessageWithArgs($code, $args) {
        if (!is_array($args)) {
            return "$args not array.\n";
        }
        if (!is_string($code)) {
            return "$code not string.\n";
        }

        if (!isset(self::$msgMap_[$code])) {
            return "$code not found in " . implode(",", self::$msgMap_) . ".\n";
        }
        $msg = self::$msgMap_[$code];

        $msg = preg_replace('/{/', '{$args[', $msg);
        $msg = preg_replace('/}/', ']}', $msg);
        $msg = \S2Container\Util\EvalUtil::getExpression('"' . $msg . '"');

        if (defined('S2CONTAINER_PHP5_DEBUG_EVAL') && S2CONTAINER_PHP5_DEBUG_EVAL) {
            \S2Container\Logger\S2Logger::getLogger(__CLASS__)->debug("[ $msg ]",
                    __METHOD__);
        }

        return eval($msg);
    }

}

