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
use S2Dao\PHPType;

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
        $matches = [];
        if (!preg_match('/^([\w\.]+)(\s+.*)?/i',
            $this->parsedExpression,
            $matches)) {

            throw new \S2Dao\Exception\IllegalBoolExpressionRuntimeException($this->expression);
        }
        $value = $matches[1];
        $expression = '';
        if (2 < count($matches)) {
            $expression = $matches[2];
            $names = explode('.', $matches[1]);

            $value = $ctx->getArg($names[0]);
            $clazz = $ctx->getArgType($names[0]);
            $objType = gettype(new \stdClass);
            for ($i = 1, $c = count($names); $i < $c; $i++) {
                if (!($objType == $clazz || is_object($clazz))) {
                    continue;
                }
                if ($value === null) {
                    continue;
                }
                if (!is_object($value)) {
                    break;
                }
                $value = $value->{$names[$i]};
                $clazz = PHPType::getType($value);
            }
        }
        $evaluate = \S2Container\Util\EvalUtil::getExpression('$value ' . $expression);
        $result = eval($evaluate);
        if (!self::isBoolValue($result)) {
            throw new \S2Dao\Exception\IllegalBoolExpressionRuntimeException($this->expression);
        }
        if (self::isTrue($result)) {
            parent::accept($ctx);
            $ctx->setEnabled(true);
        } else if ($this->elseNode !== null) {
            $this->getElseNode()
                ->accept($ctx);
            $ctx->setEnabled(true);
        }
    }

    /**
     * @param mixed $value
     * @return boolean
     */
    private static function isBoolValue($value = null) {
        if ($value === null || $value === 'null') {
            return false;
        }
        if (is_string($value)) {
            $value = trim($value);
        }
        return self::isFalse($value) || self::isTrue($value);
    }

    /**
     * @param mixed $value
     * @return boolean
     */
    private static function isTrue($value) {
        if (is_bool($value)) {
            return $value === true;
        }
        return is_string($value) && strcasecmp('true', $value) === 0;
    }

    /**
     * @param mixed $value
     * @return boolean
     */
    private static function isFalse($value) {
        if (is_bool($value)) {
            return $value === false;
        }
        return is_string($value) && strcasecmp('false', $value) === 0;
    }
}

