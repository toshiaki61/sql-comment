<?php
namespace SqlComment;
class SqlComment {
    /**
     * @var string
     */
    private $sql;

    /**
     * @var \S2Dao\Node
     */
    private $rootNode;

    /**
     * @var array
     */
    private $argTypes;

    /**
     * @var array
     */
    private $argNames;

    /**
     * Constructs SqlComment
     *
     * @param string $sql
     */
    public function __construct($sql) {
        $this->sql = $sql;
        $this->rootNode = (new \S2Dao\Impl\SqlParserImpl($sql))->parse();
    }

    /**
     * Parse sql
     *
     * @param array $args
     * @param array $argNames
     */
    public function parse(array $args, array $argNames) {
        $this->argNames = $argNames;
        $this->argTypes = $this->getArgTypes($args);
        return $this->apply($args)
            ->getSql();
    }

    /**
     * Apply specified arguments
     *
     * @param array $args
     * @return S2Dao_CommandContext
     */
    protected function apply($args) {
        $ctx = $this->createCommandContext($args);
        $this->rootNode
            ->accept($ctx);
        return $ctx;
    }

    /**
     * Get arguments type
     *
     * @param array $args
     * @return array
     */
    protected function getArgTypes($args) {
        $argTypes = array();
        if ($args === null) {
            return $argTypes;
        }
        $c = count($args);
        for ($i = 0; $i < $c; ++$i) {
            $arg = $args[$i];
            if ($arg != null && is_object($arg)) {
                $argTypes[$i] = get_class($arg);
            } else {
                $argTypes[$i] = gettype($arg);
            }
        }
        return $argTypes;
    }

    /**
     * Creates command context
     *
     * @param array $args
     * @return \S2Dao\CommandContext
     */
    protected function createCommandContext($args) {
        $ctx = new \S2Dao\Impl\CommandContextImpl();
        if ($args === null) {
            return $ctx;
        }

        $typesCount = count($this->argTypes);
        $namesCount = count($this->argNames);
        for ($i = 0, $c = count($args); $i < $c; ++$i) {
            $argType = null;
            if ($args[$i] !== null) {
                if ($i < $typesCount) {
                    $argType = $this->argTypes[$i];
                } else {
                    $argType = $args[$i];
                }
            }
            $argType = \S2Dao\PHPType::getType($argType, $args[$i]);
            if ($i < $namesCount || isset($this->argNames[$i])) {
                $ctx->addArg($this->argNames[$i], $args[$i], $argType);
            } else {
                $ctx->addArg('$' . ($i + 1), $args[$i], $argType);
            }
        }
        return $ctx;
    }
}
