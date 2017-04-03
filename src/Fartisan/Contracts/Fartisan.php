<?php

namespace SanthoshKorukonda\Fartisan\Contracts;

interface Fartisan
{
    /**
     * Send a new message using a text.
     *
     * @param  string|array  $text
     * @return void
     */
    public function build(string $schema, int $index);
}
