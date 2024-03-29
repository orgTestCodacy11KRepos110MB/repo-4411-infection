<?php

namespace Exclude_Mutations_By_Regex;

use Webmozart\Assert\Assert;

class SourceClass
{
    public function hello(): string
    {
        Assert::numeric('1');

        // @codeCoverageIgnoreStart
        $this->getString();
        // @codeCoverageIgnoreEnd

        return 'hello';
    }

    public function getString()
    {
        return 'string';
    }
}
