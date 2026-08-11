<?php
/**
 * 極簡 GraphQL 查詢語法解析器（自行實作，不依賴外部套件）
 *
 * 支援語法：
 *   - query / mutation（可省略關鍵字的簡寫查詢）與操作名稱
 *   - 變數宣告 $name: Type = 預設值，並可於欄位參數中使用
 *   - 欄位別名 alias: field
 *   - 欄位參數（字串、整數、浮點數、布林、null、列舉、陣列、物件）
 *   - 巢狀選取集合、__typename
 *   - # 註解、逗號（視同空白）、區塊字串 """..."""
 */

/** 解析或執行過程中對外呈現的錯誤 */
class GraphQLError extends Exception
{
}

/** 語法單元 */
class GraphQLToken
{
    public function __construct(
        public string $type,   // name | int | float | string | punct | eof
        public mixed $value,
        public int $position
    ) {
    }
}

class GraphQLParser
{
    /** @var GraphQLToken[] 語法單元串列 */
    private array $tokens = [];

    /** @var int 目前讀取位置 */
    private int $index = 0;

    /**
     * 將查詢字串解析成操作定義陣列
     *
     * @return array<int, array{operation:string, name:?string, variables:array, selectionSet:array}>
     */
    public function parse(string $source): array
    {
        $this->tokens = $this->tokenize($source);
        $this->index  = 0;

        $operations = [];
        while (!$this->isType('eof')) {
            $operations[] = $this->parseOperation();
        }

        if ($operations === []) {
            throw new GraphQLError('syntax error: empty document');
        }

        return $operations;
    }

    /* ===================== 詞法分析 ===================== */

    /** @return GraphQLToken[] */
    private function tokenize(string $source): array
    {
        $tokens = [];
        $length = strlen($source);
        $i      = 0;

        while ($i < $length) {
            $char = $source[$i];

            // 空白與逗號一律忽略
            if (strpos(" \t\r\n,\xef\xbb\xbf", $char) !== false) {
                $i++;
                continue;
            }

            // 註解讀到行尾
            if ($char === '#') {
                while ($i < $length && $source[$i] !== "\n") {
                    $i++;
                }
                continue;
            }

            // 區塊字串 """..."""
            if (substr($source, $i, 3) === '"""') {
                $end = strpos($source, '"""', $i + 3);
                if ($end === false) {
                    throw new GraphQLError('syntax error: unterminated block string');
                }
                $tokens[] = new GraphQLToken('string', trim(substr($source, $i + 3, $end - $i - 3)), $i);
                $i        = $end + 3;
                continue;
            }

            // 一般字串
            if ($char === '"') {
                [$value, $i] = $this->readString($source, $i);
                $tokens[]    = new GraphQLToken('string', $value, $i);
                continue;
            }

            // 數字（含負號、小數、指數）
            if (ctype_digit($char) || ($char === '-' && $i + 1 < $length && ctype_digit($source[$i + 1]))) {
                $start = $i;
                $i++;
                $isFloat = false;
                while ($i < $length && (ctype_digit($source[$i]) || strpos('.eE+-', $source[$i]) !== false)) {
                    if (strpos('.eE', $source[$i]) !== false) {
                        $isFloat = true;
                    }
                    // 指數以外的 + - 不屬於數字
                    if (($source[$i] === '+' || $source[$i] === '-') && strpos('eE', $source[$i - 1]) === false) {
                        break;
                    }
                    $i++;
                }
                $raw      = substr($source, $start, $i - $start);
                $tokens[] = new GraphQLToken($isFloat ? 'float' : 'int', $isFloat ? (float) $raw : (int) $raw, $start);
                continue;
            }

            // 名稱（含底線開頭，如 __typename）
            if (ctype_alpha($char) || $char === '_') {
                $start = $i;
                while ($i < $length && (ctype_alnum($source[$i]) || $source[$i] === '_')) {
                    $i++;
                }
                $tokens[] = new GraphQLToken('name', substr($source, $start, $i - $start), $start);
                continue;
            }

            // 展開運算子
            if (substr($source, $i, 3) === '...') {
                $tokens[] = new GraphQLToken('punct', '...', $i);
                $i       += 3;
                continue;
            }

            // 其他標點符號
            if (strpos('{}()[]:$!=@|&', $char) !== false) {
                $tokens[] = new GraphQLToken('punct', $char, $i);
                $i++;
                continue;
            }

            throw new GraphQLError('syntax error: unexpected character "' . $char . '"');
        }

        $tokens[] = new GraphQLToken('eof', null, $length);

        return $tokens;
    }

    /**
     * 讀取雙引號字串並處理跳脫字元
     *
     * @return array{0:string, 1:int}
     */
    private function readString(string $source, int $i): array
    {
        $length = strlen($source);
        $i++; // 跳過起始引號
        $value = '';

        while ($i < $length && $source[$i] !== '"') {
            if ($source[$i] === '\\') {
                $i++;
                $escape = $source[$i] ?? '';
                $value .= match ($escape) {
                    'n'     => "\n",
                    't'     => "\t",
                    'r'     => "\r",
                    'b'     => "\x08",
                    'f'     => "\x0c",
                    'u'     => $this->readUnicodeEscape($source, $i),
                    default => $escape,
                };
                $i += $escape === 'u' ? 5 : 1;
                continue;
            }
            $value .= $source[$i];
            $i++;
        }

        if ($i >= $length) {
            throw new GraphQLError('syntax error: unterminated string');
        }

        return [$value, $i + 1];
    }

    /** 讀取 \uXXXX 形式的跳脫字元 */
    private function readUnicodeEscape(string $source, int $i): string
    {
        $hex = substr($source, $i + 1, 4);

        return mb_convert_encoding(pack('n', hexdec($hex)), 'UTF-8', 'UTF-16BE');
    }

    /* ===================== 語法分析 ===================== */

    private function current(): GraphQLToken
    {
        return $this->tokens[$this->index];
    }

    private function isType(string $type): bool
    {
        return $this->current()->type === $type;
    }

    private function isPunct(string $value): bool
    {
        $token = $this->current();

        return $token->type === 'punct' && $token->value === $value;
    }

    private function expectPunct(string $value): void
    {
        if (!$this->isPunct($value)) {
            throw new GraphQLError('syntax error: expected "' . $value . '"');
        }
        $this->index++;
    }

    private function expectName(): string
    {
        if (!$this->isType('name')) {
            throw new GraphQLError('syntax error: expected name');
        }

        return $this->tokens[$this->index++]->value;
    }

    /** 解析單一操作（query / mutation） */
    private function parseOperation(): array
    {
        $operation = 'query';
        $name      = null;
        $variables = [];

        if ($this->isType('name')) {
            $keyword = $this->current()->value;
            if (!in_array($keyword, ['query', 'mutation', 'subscription'], true)) {
                throw new GraphQLError('syntax error: unknown operation "' . $keyword . '"');
            }
            if ($keyword === 'subscription') {
                throw new GraphQLError('subscription is not supported');
            }
            $operation = $keyword;
            $this->index++;

            if ($this->isType('name')) {
                $name = $this->expectName();
            }
            if ($this->isPunct('(')) {
                $variables = $this->parseVariableDefinitions();
            }
        }

        return [
            'operation'    => $operation,
            'name'         => $name,
            'variables'    => $variables,
            'selectionSet' => $this->parseSelectionSet(),
        ];
    }

    /** 解析變數宣告 ($id: Int! = 1) */
    private function parseVariableDefinitions(): array
    {
        $this->expectPunct('(');
        $definitions = [];

        while (!$this->isPunct(')')) {
            $this->expectPunct('$');
            $variableName = $this->expectName();
            $this->expectPunct(':');
            $this->parseTypeReference(); // 型別僅解析不驗證

            $default = null;
            if ($this->isPunct('=')) {
                $this->index++;
                $default = $this->parseValue();
            }

            $definitions[$variableName] = ['default' => $default];
        }

        $this->expectPunct(')');

        return $definitions;
    }

    /** 解析型別參考（Int / [Int!]! 等），僅為了跳過語法 */
    private function parseTypeReference(): void
    {
        if ($this->isPunct('[')) {
            $this->index++;
            $this->parseTypeReference();
            $this->expectPunct(']');
        } else {
            $this->expectName();
        }

        if ($this->isPunct('!')) {
            $this->index++;
        }
    }

    /** 解析選取集合 { ... } */
    private function parseSelectionSet(): array
    {
        $this->expectPunct('{');
        $selections = [];

        while (!$this->isPunct('}')) {
            if ($this->isPunct('...')) {
                throw new GraphQLError('fragments are not supported');
            }

            $name  = $this->expectName();
            $alias = $name;

            // alias: field
            if ($this->isPunct(':')) {
                $this->index++;
                $name = $this->expectName();
            }

            $arguments = $this->isPunct('(') ? $this->parseArguments() : [];
            $children  = $this->isPunct('{') ? $this->parseSelectionSet() : null;

            $selections[] = [
                'name'         => $name,
                'alias'        => $alias,
                'arguments'    => $arguments,
                'selectionSet' => $children,
            ];
        }

        $this->expectPunct('}');

        if ($selections === []) {
            throw new GraphQLError('syntax error: empty selection set');
        }

        return $selections;
    }

    /** 解析欄位參數 (name: value, ...) */
    private function parseArguments(): array
    {
        $this->expectPunct('(');
        $arguments = [];

        while (!$this->isPunct(')')) {
            $argumentName = $this->expectName();
            $this->expectPunct(':');
            $arguments[$argumentName] = $this->parseValue();
        }

        $this->expectPunct(')');

        return $arguments;
    }

    /** 解析參數值，變數以 ['__variable' => 名稱] 表示 */
    private function parseValue(): mixed
    {
        $token = $this->current();

        if ($this->isPunct('$')) {
            $this->index++;

            return ['__variable' => $this->expectName()];
        }

        if ($this->isPunct('[')) {
            $this->index++;
            $list = [];
            while (!$this->isPunct(']')) {
                $list[] = $this->parseValue();
            }
            $this->expectPunct(']');

            return $list;
        }

        if ($this->isPunct('{')) {
            $this->index++;
            $object = [];
            while (!$this->isPunct('}')) {
                $key = $this->expectName();
                $this->expectPunct(':');
                $object[$key] = $this->parseValue();
            }
            $this->expectPunct('}');

            return $object;
        }

        if (in_array($token->type, ['int', 'float', 'string'], true)) {
            $this->index++;

            return $token->value;
        }

        if ($token->type === 'name') {
            $this->index++;

            return match ($token->value) {
                'true'  => true,
                'false' => false,
                'null'  => null,
                default => $token->value, // 列舉值以字串表示
            };
        }

        throw new GraphQLError('syntax error: invalid value');
    }
}
