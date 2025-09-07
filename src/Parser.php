<?php

declare(strict_types=1);

namespace Stancl\GPC;

use Stancl\GPC\Contracts\Line;
use Stancl\GPC\Lines\HeaderLine;
use Stancl\GPC\Lines\RecordLine;

class Parser
{
    /** @var Line[] */
    public array $lines = [];

    public function __construct(
        public string $encoding = 'windows-1250',
        public string $lineSeparator = "\r\n"
    ) {}

    public function parseFile(string $path): static
    {
        return $this->parse(iconv($this->encoding, 'utf-8', file_get_contents($path)));
    }

    public function parse(string $content): static
    {
        str(trim($content))
            ->split("/{$this->lineSeparator}/")
            ->map($this->parseLine(...))
            ->each(fn (Line $line) => $this->lines[] = $line);

        return $this;
    }

    protected function parseLine(string $line): Line
    {
        return match (substr($line, 0, 3)) {
            '074' => new HeaderLine($line),
            '075' => new RecordLine($line),
        };
    }

    /** @return Record[] */
    public function records(): array
    {
        return collect($this->lines)
            ->filter(fn (Line $line) => $line instanceof RecordLine)
            ->map(fn (RecordLine $line) => $line->record)
            ->values()
            ->toArray();
    }

    /**
     * Returns the content as a UTF-8 string (see Generator@content() docblock)
     */
    public function content(): string
    {
        $generator = new Generator($this->encoding, $this->lineSeparator);

        $generator->loadLines($this->lines);

        return $generator->content();
    }

    public function writeContent(string $path, string|null $encoding = null, string|null $lineSeparator = null): bool
    {
        $generator = new Generator($encoding ?? $this->encoding, $lineSeparator ?? $this->lineSeparator);

        $generator->loadLines($this->lines);

        return $generator->writeContent($path);
    }
}
