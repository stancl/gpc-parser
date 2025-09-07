# gpc-parser

Simple GPC parser and generator, specifically designed around Fio bank's format https://www.fio.cz/docs/cz/struktura-gpc.pdf

For usage, see `example.php`.

## Executable

Also see `bin/gpc-parser`. Can be nice to add to PATH. It just converts file_get_contents($argv\[1\]) into JSON, making it
universally machine readable. Pairs very nicely with nushell. It can be annoying to work with gpc files otherwise when you
cannot quickly view their contents.

```
$ bin/gpc-parser tests/etc/sample.gpc
[{"localAccount":"123456789","foreignAccount":"987654321","transactionId":"12345678","amount":"12300","type":"1","variableSymbol":"1234","foreignBankCode":"0000","constantSymbol":"","specificSymbol":"","valuta":"200723","message":"Objedn\u00e1vka \u010d. 1234","currencyCode":"0203","date":"200723"},{"localAccount":"123456789","foreignAccount":"987654321","transactionId":"18345678","amount":"12300","type":"1","variableSymbol":"1235","foreignBankCode":"0000","constantSymbol":"","specificSymbol":"","valuta":"200723","message":"Objedn\u00e1vka 1235","currencyCode":"0203","date":"200723"},{"localAccount":"123456789","foreignAccount":"987654321","transactionId":"","amount":"12300","type":"1","variableSymbol":"12","foreignBankCode":"0000","constantSymbol":"","specificSymbol":"","valuta":"200723","message":"Platba kartou","currencyCode":"0203","date":"200723"},{"localAccount":"123456789","foreignAccount":"987654321","transactionId":"24345678","amount":"12300","type":"1","variableSymbol":"1236","foreignBankCode":"0000","constantSymbol":"","specificSymbol":"","valuta":"200723","message":"Objednavka 1236","currencyCode":"0203","date":"200723"},{"localAccount":"123456789","foreignAccount":"987654321","transactionId":"","amount":"12300","type":"1","variableSymbol":"12","foreignBankCode":"0000","constantSymbol":"","specificSymbol":"","valuta":"200723","message":"Platba kartou","currencyCode":"0203","date":"200723"}]
$ bin/gpc-parser tests/etc/sample.gpc | from json
╭────┬───────────────┬─────────────────┬────────────────┬─────────┬───────┬─────────────────┬──────────────────┬─────────────────┬─────────────────┬─────────┬─────────────────────┬───────────────┬────────╮
│  # │ localAccount  │ foreignAccount  │ transactionId  │ amount  │ type  │ variableSymbol  │ foreignBankCode  │ constantSymbol  │ specificSymbol  │ valuta  │       message       │ currencyCode  │  date  │
├────┼───────────────┼─────────────────┼────────────────┼─────────┼───────┼─────────────────┼──────────────────┼─────────────────┼─────────────────┼─────────┼─────────────────────┼───────────────┼────────┤
│  0 │ 123456789     │ 987654321       │ 12345678       │ 12300   │ 1     │ 1234            │ 0000             │                 │                 │ 200723  │ Objednávka č. 1234  │ 0203          │ 200723 │
│  1 │ 123456789     │ 987654321       │ 18345678       │ 12300   │ 1     │ 1235            │ 0000             │                 │                 │ 200723  │ Objednávka 1235     │ 0203          │ 200723 │
│  2 │ 123456789     │ 987654321       │                │ 12300   │ 1     │ 12              │ 0000             │                 │                 │ 200723  │ Platba kartou       │ 0203          │ 200723 │
│  3 │ 123456789     │ 987654321       │ 24345678       │ 12300   │ 1     │ 1236            │ 0000             │                 │                 │ 200723  │ Objednavka 1236     │ 0203          │ 200723 │
│  4 │ 123456789     │ 987654321       │                │ 12300   │ 1     │ 12              │ 0000             │                 │                 │ 200723  │ Platba kartou       │ 0203          │ 200723 │
╰────┴───────────────┴─────────────────┴────────────────┴─────────┴───────┴─────────────────┴──────────────────┴─────────────────┴─────────────────┴─────────┴─────────────────────┴───────────────┴────────╯
```
