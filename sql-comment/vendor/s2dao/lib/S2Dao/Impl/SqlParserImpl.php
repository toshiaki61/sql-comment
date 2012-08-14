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
namespace S2Dao\Impl;
/**
 * @author nowel
 * @package org.seasar.s2dao.parser
 */
class SqlParserImpl implements \S2Dao\SqlParser {

    private $tokenizer = null;
    private $nodeStack = array();

    public function __construct($sql) {
        $sql = preg_replace('/;$/s', '', trim($sql));
        $this->tokenizer = new \S2Dao\SqlTokenizerImpl($sql);
    }

    public function parse() {
        $this->push(new \S2Dao\Node\ContainerNode());
        while ($this->tokenizer
            ->next() !== \S2Dao\SqlTokenizer::EOF) {
            $this->parseToken();
        }
        return $this->pop();
    }

    protected function parseToken() {
        switch ($this->tokenizer
            ->getTokenType()) {
            case \S2Dao\SqlTokenizer::SQL:
                $this->parseSql();
                break;
            case \S2Dao\SqlTokenizer::COMMENT:
                $this->parseComment();
                break;
            case \S2Dao\SqlTokenizer::ELSE_:
                $this->parseElse();
                break;
            case \S2Dao\SqlTokenizer::BIND_VARIABLE:
                $this->parseBindVariable();
                break;
        }
    }

    protected function parseSql() {
        $sql = $this->tokenizer
            ->getToken();
        if ($this->isElseMode()) {
            $sql = str_replace('--', '', $sql);
        }
        $node = $this->peek();
        if (($node instanceof \S2Dao\Node\IfNode || $node instanceof \S2Dao\Node\ElseNode) && $node->getChildSize() === 0) {
            $st = new \S2Dao\SqlTokenizerImpl($sql);
            $st->skipWhitespace();
            $token = $st->skipToken();
            $st->skipWhitespace();

            if (preg_match('/(AND|OR)/i', $token)) {
                $node->addChild(new \S2Dao\Node\PrefixSqlNode($st->getBefore(), $st->getAfter()));
            } else {
                $node->addChild(new \S2Dao\Node\SqlNode($sql));
            }
        } else {
            $node->addChild(new \S2Dao\Node\SqlNode($sql));
        }
    }

    protected function parseComment() {
        $comment = $this->tokenizer
            ->getToken();
        if ($this->isTargetComment($comment)) {
            if ($this->isIfComment($comment)) {
                $this->parseIf();
            } else if ($this->isBeginComment($comment)) {
                $this->parseBegin();
            } else if ($this->isEndComment($comment)) {
                return;
            } else {
                $this->parseCommentBindVariable();
            }
        }
    }

    /**
     * @throws \S2Dao\IfConditionNotFoundRuntimeException
     */
    protected function parseIf() {
        $condition = trim(substr($this->tokenizer
            ->getToken(), 2));
        if (empty($condition)) {
            throw new \S2Dao\IfConditionNotFoundRuntimeException();
        }
        $ifNode = new \S2Dao\Node\IfNode($condition);
        $this->peek()
            ->addChild($ifNode);
        $this->push($ifNode);
        $this->parseEnd();
    }

    protected function parseBegin() {
        $beginNode = new \S2Dao\Node\BeginNode();
        $this->peek()
            ->addChild($beginNode);
        $this->push($beginNode);
        $this->parseEnd();
    }

    /**
     * @throws \S2Dao\EndCommentNotFoundRuntimeException
     */
    protected function parseEnd() {
        while (\S2Dao\SqlTokenizer::EOF != $this->tokenizer
            ->next()) {
            if ($this->tokenizer
                ->getTokenType() === \S2Dao\SqlTokenizer::COMMENT && $this->isEndComment($this->tokenizer
                        ->getToken())) {
                $this->pop();
                return;
            }
            $this->parseToken();
        }
        throw new \S2Dao\EndCommentNotFoundRuntimeException();
    }

    protected function parseElse() {
        $parent = $this->peek();
        if (!($parent instanceof \S2Dao\Node\IfNode)) {
            return;
        }
        $ifNode = $this->pop();
        $elseNode = new \S2Dao\Node\ElseNode();
        $ifNode->setElseNode($elseNode);
        $this->push($elseNode);
        $this->tokenizer
            ->skipWhitespace();
    }

    protected function parseCommentBindVariable() {
        $expr = $this->tokenizer
            ->getToken();
        $s = $this->tokenizer
            ->skipToken();
        if ($s !== false && strpos($s, '(') === 0 && substr($s, -1, 1) === ')') {
            $this->peek()
                ->addChild(new \S2Dao\Node\ParenBindVariableNode($expr));
        } else if (strpos($expr, '$') === 0) {
            $this->peek()
                ->addChild(new \S2Dao\Node\EmbeddedValueNode(substr($expr, 1)));
        } else {
            $this->peek()
                ->addChild(new \S2Dao\Node\BindVariableNode($expr));
        }
    }

    protected function parseBindVariable() {
        $expr = $this->tokenizer
            ->getToken();
        $this->peek()
            ->addChild(new \S2Dao\Node\BindVariableNode($expr));
    }

    protected function pop() {
        return array_pop($this->nodeStack);
    }

    protected function peek() {
        $st = array();
        $st = (array) $this->nodeStack;
        return array_pop($st);
    }

    protected function push(\S2Dao\Node $node) {
        $this->nodeStack[] = $node;
    }

    protected function isElseMode() {
        $stack = $this->nodeStack;
        $c = count($stack);
        for ($i = 0; $i < $c; ++$i) {
            if ($stack[$i] instanceof \S2Dao\Node\ElseNode) {
                return true;
            }
        }
        return false;
    }

    private static function isTargetComment($comment = null) {
        return $comment !== null && 0 < strlen($comment) && substr($comment,
            0,
            1) !== null;
    }

    private static function isIfComment($comment) {
        return strpos($comment, 'IF') === 0;
    }

    private static function isBeginComment($content = null) {
        return $content !== null && strcmp('BEGIN', $content) === 0;
    }

    private static function isEndComment($content = null) {
        return $content !== null && strcmp('END', $content) === 0;
    }
}
