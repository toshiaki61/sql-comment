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
// $Id: $
//
namespace S2Dao\Node;
/*
 *
 * @author nowel
 * @package org.seasar.s2dao.node
 */
class S2Dao_ParenBindVariableNode extends S2Dao_AbstractNode {

    private $expression = '';
    private $parsedExpression = null;

    public function __construct($expression) {
        $this->expression = $expression;
        $expression = quotemeta($expression);
        $expression = str_replace('\.', '.', $expression);
        $this->parsedExpression = $expression;
    }

    public function getExpression() {
        return $this->expression;
    }

    public function accept(S2Dao_CommandContext $ctx) {
        $expression = preg_replace('/^(\w+)(\s+.*)?/i',
                        '$ctx->getArg("\1")' . '\2', $this->parsedExpression);
        $expression = S2Container_EvalUtil::getExpression($expression);
        $result = eval($expression);
        
        if ($result instanceof S2Dao_List) {
            $this->bindArray($ctx, $result->toArray());
        } else if ($result === null) {
            return;
        } else if (is_array($result)) {
            $this->bindArray($ctx, $result);
        } else {
            $ctx->addSql('?', $result, get_class($result));
        }
    }

    private function bindArray(S2Dao_CommandContext $ctx, array $array) {
        $length = count($array);
        if ($length == 0) {
            return;
        }
        $clazz = null;
        for ($i = 0; $i < $length; ++$i) {
            $o = $array[$i];
            if ($o !== null) {
                if(($clazz = get_class($o)) === false){
                    $clazz = gettype($o);
                }
            }
        }
        $ctx->addSql('(');
        $ctx->addSql('?', $array[0], $clazz);
        for ($i = 1; $i < $length; ++$i) {
            $ctx->addSql(', ?', $array[$i], $clazz);
        }
        $ctx->addSql(')');
    }
}
?>