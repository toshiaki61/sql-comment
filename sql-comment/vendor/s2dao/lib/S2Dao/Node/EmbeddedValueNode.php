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
class S2Dao_EmbeddedValueNode extends S2Dao_AbstractNode {

    private $expression = '';
    private $baseName = '';
    private $propertyName = '';

    public function __construct($expression) {
        $this->expression = $expression;
        $array = explode('.',$expression);
        $this->baseName = $array[0];
        if (1 < count($array)) {
            $this->propertyName = $array[1];
        }
    }

    public function getExpression() {
        return $this->expression;
    }

    public function accept(S2Dao_CommandContext $ctx) {
        $value = $ctx->getArg($this->baseName);
        $clazz = $ctx->getArgType($this->baseName);

        if ($this->propertyName != null) {
            $beanDesc = BeanDescFactory::getBeanDesc($clazz);
            $pd = $beanDesc->getPropertyDesc($this->propertyName);
            $value = $pd->getValue($value);
            $clazz = $pd->getPropertyType();
        }
        if ($value != null) {
            $ctx->addSql((string)$value);
        }
    }
}
?>
