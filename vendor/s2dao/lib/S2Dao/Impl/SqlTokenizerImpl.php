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
use S2Dao\Node\ElseNode;

use S2Dao\SqlTokenizer;

class SqlTokenizerImpl implements SqlTokenizer {

    private $sql = '';
    private $position = 0;
    private $token = null;
    private $tokenType = SqlTokenizer::SQL;
    private $nextTokenType = SqlTokenizer::SQL;
    private $bindVariableNum = 0;

    /**
     * Constructs SqlTokenizerImpl.
     * @param string $sql
     */
    public function __construct($sql) {
        $this->sql = $sql;
    }

    /**
     * (non-PHPdoc)
     * @see S2Dao.SqlTokenizer::getPosition()
     */
    public function getPosition() {
        return $this->position;
    }

    /**
     * (non-PHPdoc)
     * @see S2Dao.SqlTokenizer::getToken()
     */
    public function getToken() {
        return $this->token;
    }

    /**
     * (non-PHPdoc)
     * @see S2Dao.SqlTokenizer::getBefore()
     */
    public function getBefore() {
        return substr($this->sql, 0, $this->position);
    }

    /**
     * (non-PHPdoc)
     * @see S2Dao.SqlTokenizer::getAfter()
     */
    public function getAfter() {
        return substr($this->sql, $this->position);
    }

    /**
     * (non-PHPdoc)
     * @see S2Dao.SqlTokenizer::getTokenType()
     */
    public function getTokenType() {
        return $this->tokenType;
    }

    /**
     * (non-PHPdoc)
     * @see S2Dao.SqlTokenizer::getNextTokenType()
     */
    public function getNextTokenType() {
        return $this->nextTokenType;
    }

    /**
     * (non-PHPdoc)
     * @see S2Dao.SqlTokenizer::next()
     */
    public function next() {
        if ($this->position >= strlen($this->sql)) {
            $this->nextTokenType = SqlTokenizer::EOF;
        }
        switch ($this->nextTokenType) {
            case SqlTokenizer::SQL:
                $this->parseSql();
                break;
            case SqlTokenizer::COMMENT:
                $this->parseComment();
                break;
            case SqlTokenizer::ELSE_:
                $this->parseElse();
                break;
            case SqlTokenizer::BIND_VARIABLE:
                $this->parseBindVariable();
                break;
            default:
                $this->parseEof();
                break;
        }

        return $this->tokenType;
    }

    protected function parseSql() {
        $commentStartPos = strpos($this->sql, '/*', $this->position);
        $lineCommentStartPos = strpos($this->sql, '--', $this->position);
        $bindVariableStartPos = strpos($this->sql, '?', $this->position);
        $elseCommentStartPos = -1;
        $elseCommentLength = -1;
        if ($lineCommentStartPos !== false && 0 <= $lineCommentStartPos) {
            $skipPos = $this->skipWhitespace($lineCommentStartPos + 2);
            static $else = 'ELSE';
            static $elseLen = 4;
            if ($skipPos + $elseLen < strlen($this->sql) && $else === substr($this->sql,
                $skipPos,
                $elseLen)) {
                $elseCommentStartPos = $lineCommentStartPos;
                $elseCommentLength = $skipPos + $elseLen - $lineCommentStartPos;
            }
        }
        $nextStartPos = $this->getNextStartPos($commentStartPos,
                $elseCommentStartPos,
                $bindVariableStartPos);

        if ($nextStartPos === false || $nextStartPos < 0) {
            $this->token = substr($this->sql, $this->position);
            $this->nextTokenType = SqlTokenizer::EOF;
            $this->position = strlen($this->sql);
            $this->tokenType = SqlTokenizer::SQL;
        } else {
            $endPos = $nextStartPos - $this->position;
            $this->token = substr($this->sql, $this->position, $endPos);
            $this->tokenType = SqlTokenizer::SQL;
            $needNext = $nextStartPos == $this->position;

            if ($nextStartPos == $commentStartPos) {
                $this->nextTokenType = SqlTokenizer::COMMENT;
                $this->position = $commentStartPos + 2;
            } else if ($nextStartPos == $elseCommentStartPos) {
                $this->nextTokenType = SqlTokenizer::ELSE_;
                $this->position = $elseCommentStartPos + $elseCommentLength + 1;
            } else if ($bindVariableStartPos !== false && $nextStartPos == $bindVariableStartPos) {
                $this->nextTokenType = SqlTokenizer::BIND_VARIABLE;
                $this->position = $bindVariableStartPos;
            }
            if ($needNext) {
                $this->next();
            }
        }
    }

    /**
     * @param int $commentStartPos
     * @param int $elseCommentStartPos
     * @param int $bindVariableStartPos
     * @return int
     */
    protected function getNextStartPos($commentStartPos, $elseCommentStartPos, $bindVariableStartPos) {

        $nextStartPos = -1;
        if ($commentStartPos !== false && $commentStartPos >= 0) {
            $nextStartPos = $commentStartPos;
        }
        if ($elseCommentStartPos >= 0 && ($nextStartPos < 0 || $elseCommentStartPos < $nextStartPos)) {
            $nextStartPos = $elseCommentStartPos;
        }
        if ($bindVariableStartPos !== false && $bindVariableStartPos >= 0 && ($nextStartPos < 0 || $bindVariableStartPos < $nextStartPos)) {
            $nextStartPos = $bindVariableStartPos;
        }
        return $nextStartPos;
    }

    protected function nextBindVariableName() {
        return '$' . ++$this->bindVariableNum;
    }

    /**
     * @throws \S2Dao\Exception\TokenNotClosedRuntimeException
     */
    protected function parseComment() {
        $commentEndPos = strpos($this->sql, '*/', $this->position);
        if ($commentEndPos === false || $commentEndPos < 0) {
            throw new \S2Dao\Exception\TokenNotClosedRuntimeException('*/', substr($this->sql,
                $this->position));
        }
        $endPos = $commentEndPos - $this->position;
        $this->token = substr($this->sql, $this->position, $endPos);
        $this->nextTokenType = SqlTokenizer::SQL;
        $this->position = $commentEndPos + 2;
        $this->tokenType = SqlTokenizer::COMMENT;
    }

    protected function parseBindVariable() {
        $this->token = $this->nextBindVariableName();
        $this->nextTokenType = SqlTokenizer::SQL;
        $this->position += 1;
        $this->tokenType = SqlTokenizer::BIND_VARIABLE;
    }

    protected function parseElse() {
        $this->token = null;
        $this->nextTokenType = SqlTokenizer::SQL;
        $this->tokenType = SqlTokenizer::ELSE_;
    }

    protected function parseEof() {
        $this->token = null;
        $this->tokenType = SqlTokenizer::EOF;
        $this->nextTokenType = SqlTokenizer::EOF;
    }

    /**
     * (non-PHPdoc)
     * @see S2Dao.SqlTokenizer::skipToken()
     */
    public function skipToken() {
        $index = strlen($this->sql);
        $quote = $this->position < strlen($this->sql) ? substr($this->sql,
            $this->position,
            1) : '';
        $quoting = $quote === '\'' || $quote === '(';
        if ($quote === '(') {
            $quote = ')';
        }
        $i = $quoting ? ($this->position + 1) : $this->position;
        $len = strlen($this->sql);
        for (; $i < $len; ++$i) {
            $c = substr($this->sql, $i, 1);
            $next = $i + 1;
            if (preg_match('/(\s|,|\)|\()/', $c) && !$quoting) {
                $index = $i;
                break;
            } else if ($c === '/' && $next < $len && substr($this->sql,
                $next,
                1) === '*') {
                $index = $i;
                break;
            } else if ($c === '-' && $next < $len && substr($this->sql,
                $next,
                1) === '-') {
                $index = $i;
                break;
            } else if ($quoting && $quote == '\'' && $c == '\'' && ($len <= $next || substr($this->sql,
                $next,
                1) !== '\'')) {
                $index = $next;
                break;
            } else if ($quoting && $c == $quote) {
                $index = $next;
                break;
            }
        }
        $tok = substr($this->sql, $this->position, $index - $this->position);
        $this->token = $tok;
        $this->tokenType = SqlTokenizer::SQL;
        $this->nextTokenType = SqlTokenizer::SQL;
        $this->position = $index;

        return $this->token;
    }

    /**
     * (non-PHPdoc)
     * @see S2Dao.SqlTokenizer::skipWhitespace()
     */
    public function skipWhitespace($position = null) {
        if ($position === null) {
            $index = $this->skipWhitespace($this->position);
            $this->token = substr($this->sql, $this->position, $index);
            $this->position = $index;

            return $this->position;
        }
        $matches = [];
        if (preg_match('/^([\s\v]+)/', substr($this->sql, $position), $matches)) {
            return $position + strlen($matches[1]);
        }

        return $position;
    }
}

