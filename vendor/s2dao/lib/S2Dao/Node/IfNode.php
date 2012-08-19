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
// | Authors: nowel                                                       |
// +----------------------------------------------------------------------+
// $Id$
//
namespace S2Dao\Node;
/**
 * @author nowel
 * @package org.seasar.s2dao.node
 */
class IfNode extends \S2Dao\Node\ContainerNode {

    private $expression = '';
    private $parsedExpression = null;
    private $elseNode = null;

    /**
     * @param string $expression
     */
    public function __construct($expression) {
        parent::__construct();
        $this->expression = $expression;
        $exp = quotemeta($expression);
        $exp = str_replace('\.', '.', $exp);
        $this->parsedExpression = $exp;
    }

    /**
     * @return string
     */
    public function getExpression() {
        return $this->expression;
    }

    /**
     * @return \S2Dao\Node\ElseNode
     */
    public function getElseNode() {
        return $this->elseNode;
    }

    /**
     * @param \S2Dao\Node\ElseNode $elseNode
     */
    public function setElseNode(\S2Dao\Node\ElseNode $elseNode) {
        $this->elseNode = $elseNode;
    }

    /**
     * (non-PHPdoc)
     * @see S2Dao\Node.ContainerNode::accept()
     */
    public function accept(\S2Dao\CommandContext $ctx) {
        $result = false;
        if (preg_match('/^([\w\.]+)(\s+.*)?/i',
            $this->parsedExpression,
            $matches)) {
            if (2 < count($matches)) {
                $expression = $matches[2];
                $names = explode('.', $matches[1]);

                $value = $ctx->getArg($names[0]);
                $clazz = $ctx->getArgType($names[0]);
                $objType = gettype(new \stdClass);
                $c = count($names);
                for ($i = 1; $i < $c; $i++) {
                    if (!($objType == $clazz || is_object($clazz))) {
                        continue;
                    }
                    if ($value === null) {
                        continue;
                    }
                    if (!is_object($value)) {
                        break;
                    }
                    $refClass = new \ReflectionClass($value);
                    $beanDesc = \S2Container\Beans\BeanDescFactory::getBeanDesc($refClass);
                    $pd = $beanDesc->getPropertyDesc($names[$i]);
                    $value = $pd->getValue($value);
                    $clazz = $pd->getPropertyType();
                }
            } else {
                $value = $matches[1];
                $expression = '';
            }
            $evaluate = \S2Container\Util\EvalUtil::getExpression("\$value $expression");
            $result = eval($evaluate);
            if (self::isBoolValue($result)) {
                if (self::isTrue($result)) {
                    parent::accept($ctx);
                    $ctx->setEnabled(true);
                } else if ($this->elseNode !== null) {
                    $this->elseNode
                        ->accept($ctx);
                    $ctx->setEnabled(true);
                }
                return;
            }
        }
        throw new \S2Dao\Exception\IllegalBoolExpressionRuntimeException($this->expression);
    }

    /**
     * @param mixed $value
     * @return boolean
     */
    private static function isBoolValue($value = null) {
        if ($value === null) {
            return false;
        }
        if (is_string($value)) {
            $v = trim($value);
            if (self::isTrue($v) || self::isFalse($v)) {
                return true;
            }
            return false;
        }
        return is_bool($value);
    }

    /**
     * @param mixed $value
     * @return boolean
     */
    private static function isTrue($value) {
        if (is_bool($value)) {
            return $value === true;
        }
        if (is_string($value)) {
            return strcasecmp('true', $value) === 0;
        }
        return false;
    }

    /**
     * @param mixed $value
     * @return boolean
     */
    private static function isFalse($value) {
        if (is_bool($value)) {
            return $value === false;
        }
        if (is_string($value)) {
            return strcasecmp('false', $value) === 0;
        }
        return false;
    }
}

