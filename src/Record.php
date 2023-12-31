<?php

declare(strict_types=1);

namespace Stancl\GPC;

use Stringable;

class Record implements Stringable
{
    public function __construct(
        // "075"
        public string $localAccount, // 16 bytes; purely left padded with 0s
        public string $foreignAccount, // 16 bytes; left padded with 0s, dashes turned into 000
        public string $transactionId, // 13 bytes; left padded with 0s
        public string $amount, // 12 bytes; left padded with 0s, in cents
        public string $type, // 1 byte; 1 = debet, 2 = kredit, 4 = storno debet, 5 = storno kredit
        public string $variableSymbol, // 10 bytes, left padded with 0s

        // constant symbol = 10 bytes; left padded with 0s, format BBBBKSYM (BBBB = kód banky, KSYM = konstantní symbol)
        // bank code is always 4 characters, and so is constant symbol (it's 4 chars or empty). so there's always '00' padding on the left
        public string $foreignBankCode, // 4 characters
        public string $constantSymbol, // 4 characters or ''
        public string $specificSymbol, // 10 bytes; left padded with 0s
        public string $valuta, // 6 bytes; Fio seems to actually fill this with the date even though their spec says it's "000000"
        public string $message, // 20 alphanumeric bytes; RIGHT padded with spaces, can be the other account's/person's name, message for recipient, or both
        // "0"
        public string $currencyCode, // 4 bytes; 0203 for CZK
        public string $date, // 6 bytes; 260523 for 26.05.2023
        // CRLF is trimmed/added in Parser/Generator so we don't deal with it here
    ) {}

    public static function fromString(string $record): static
    {
        $str = str($record);

        // Substr numbers here follow the bytes described here https://www.fio.cz/docs/cz/struktura-gpc.pdf, with the starting point subtracting one since we count from 0, not from 1
        // (e.g. "075" is bytes 1-3, but it's substr(0, 3) — starting from the first (zeroth) character with a length of 3)

        // $str->substr(starting byte - 1, segment length)

        // instead of ltrim(), we remove the first 2 characters manually, since the segment size is 10,
        // but the content size can only be 8 (4 for bank number and 4 for KS)
        $constantSymbolSegment = $str->substr(71, 10)->substr(2, 8);
        $foreignBankCode = $constantSymbolSegment->substr(0, 4)->toString(); // always has a value
        $constantSymbol = $constantSymbolSegment->substr(4, 4)->toString();

        if ($constantSymbol === '0000') {
            $constantSymbol = '';
        }

        return new static(
            // "075"
            localAccount: $str->substr(3, 16)->ltrim('0')->toString(),
            foreignAccount: $str->substr(19, 16)->ltrim('0')->toString(),
            transactionId: $str->substr(35, 13)->ltrim('0')->toString(),
            amount: $str->substr(48, 12)->ltrim('0')->toString(),
            type: $str->substr(60, 1)->toString(),
            variableSymbol: $str->substr(61, 10)->ltrim('0')->toString(),
            foreignBankCode: $foreignBankCode,
            constantSymbol: $constantSymbol,
            specificSymbol: $str->substr(81, 10)->ltrim('0')->toString(),
            valuta: $str->substr(91, 6)->toString(),
            message: $str->substr(97, 20)->rtrim(' ')->toString(),
            // "0"
            currencyCode: $str->substr(118, 4)->toString(),
            date: $str->substr(122, 6)->toString(),
        );
    }

    public function __toString(): string
    {
        $constantSymbol = str($this->foreignBankCode)->append($this->constantSymbol ?: '0000')->padLeft(10, '0');

        return str('075')
            ->append(str($this->localAccount)->padLeft(16, '0'))
            ->append(str($this->foreignAccount)->padLeft(16, '0'))
            ->append(str($this->transactionId)->padLeft(13, '0'))
            ->append(str($this->amount)->padLeft(12, '0'))
            ->append($this->type)
            ->append(str($this->variableSymbol)->padLeft(10, '0'))
            ->append($constantSymbol)
            ->append(str($this->specificSymbol)->padLeft(10, '0'))
            ->append($this->valuta)
            ->append(str($this->message)->padRight(20, ' '))
            ->append('0')
            ->append($this->currencyCode)
            ->append($this->date)
            ->toString();
    }
}
