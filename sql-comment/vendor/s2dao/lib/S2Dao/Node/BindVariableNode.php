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
class S2Dao_BindVariableNode extends S2Dao_AbstractNode {

    private $expression = '';
    private $names = array();

    public function __construct($expression) {
        $this->expression = $expression;
        $this->names = explode('.', $expression);
    }

    public function getExpression() {
        return $this->expression;
    }

    public function accept(S2Dao_CommandContext $ctx) {
        $value = $ctx->getArg($this->names[0]);
        $clazz = $ctx->getArgType($this->names[0]);
        
        $c = count($this->names);
        for($pos = 1; $pos < $c; $pos++){
            if(null === $value){
                break;
            }
            if (!is_object($value)) {
                break;
            }
            $refClass = new ReflectionClass($value);
            $beanDesc = S2Container_BeanDescFactory::getBeanDesc($refClass);
            $pd = $beanDesc->getPropertyDesc($this->names[$pos]);
            $value = $pd->getValue($value);
            $clazz = $pd->getPropertyType();
        }

        if($this->isNull($clazz)){
            $type = null;
            if($this->isNull($value)){
                $type = gettype(null);
            } else {
                $type = gettype($value);
            }
            $ctx->addSql('?', $value, $type);
        } else {
            $ctx->addSql('?', $value, gettype($clazz));
        }
    }
    
    private function isNull($clazz = null){
        return $clazz === null || $clazz == gettype(null);
    }
}
?>
