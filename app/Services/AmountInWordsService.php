<?php

namespace App\Services;

use InvalidArgumentException;

class AmountInWordsService
{
    /**
     * Convert a monetary amount to words.
     *
     * Supported currencies:
     * - KES
     * - USD
     * - CDF
     *
     * Supported languages:
     * - en
     * - fr
     */
    public function convert(
        float|int|string $amount,
        string $currency,
        string $language = 'en'
    ): string {
        $currency = strtoupper(trim($currency));
        $language = strtolower(trim($language));

        if (!in_array($currency, ['KES', 'USD', 'CDF'], true)) {
            throw new InvalidArgumentException(
                "Unsupported currency: {$currency}"
            );
        }

        if (!in_array($language, ['en', 'fr'], true)) {
            $language = 'en';
        }

        $amount = round((float) $amount, 2);

        if ($amount < 0) {
            return $language === 'fr'
                ? 'Moins ' . $this->convert(
                    abs($amount),
                    $currency,
                    $language
                )
                : 'Minus ' . $this->convert(
                    abs($amount),
                    $currency,
                    $language
                );
        }

        /*
         * CDF is normally represented as a whole-number
         * currency on the receipt.
         */
        if ($currency === 'CDF') {
            $whole = (int) round($amount);

            return $this->formatWholeCurrency(
                $whole,
                $currency,
                $language
            );
        }

        $whole = (int) floor($amount);

        $minor = (int) round(
            ($amount - $whole) * 100
        );

        /*
         * Protect against floating-point rounding such as:
         *
         * 99.999999 -> 100.00
         */
        if ($minor >= 100) {
            $whole++;
            $minor = 0;
        }

        return $this->formatCurrencyWithMinor(
            $whole,
            $minor,
            $currency,
            $language
        );
    }

    /**
     * Format a whole-number currency.
     */
    protected function formatWholeCurrency(
        int $amount,
        string $currency,
        string $language
    ): string {
        $number = $language === 'fr'
            ? $this->numberToFrench($amount)
            : $this->numberToEnglish($amount);

        $currencyName = $this->currencyName(
            $currency,
            $language,
            $amount
        );

        if ($language === 'fr') {
            return ucfirst(
                $number
                . ' '
                . $currencyName
                . ' seulement'
            );
        }

        return ucfirst(
            $number
            . ' '
            . $currencyName
            . ' only'
        );
    }

    /**
     * Format currency with major and minor units.
     */
    protected function formatCurrencyWithMinor(
        int $whole,
        int $minor,
        string $currency,
        string $language
    ): string {
        $wholeWords = $language === 'fr'
            ? $this->numberToFrench($whole)
            : $this->numberToEnglish($whole);

        $majorName = $this->currencyName(
            $currency,
            $language,
            $whole
        );

        if ($minor === 0) {
            if ($language === 'fr') {
                return ucfirst(
                    $wholeWords
                    . ' '
                    . $majorName
                    . ' seulement'
                );
            }

            return ucfirst(
                $wholeWords
                . ' '
                . $majorName
                . ' only'
            );
        }

        $minorWords = $language === 'fr'
            ? $this->numberToFrench($minor)
            : $this->numberToEnglish($minor);

        $minorName = $this->minorCurrencyName(
            $currency,
            $language,
            $minor
        );

        if ($language === 'fr') {
            return ucfirst(
                $wholeWords
                . ' '
                . $majorName
                . ' et '
                . $minorWords
                . ' '
                . $minorName
                . ' seulement'
            );
        }

        return ucfirst(
            $wholeWords
            . ' '
            . $majorName
            . ' and '
            . $minorWords
            . ' '
            . $minorName
            . ' only'
        );
    }

    /**
     * Currency major-unit names.
     */
    protected function currencyName(
        string $currency,
        string $language,
        int $amount
    ): string {
        if ($language === 'fr') {
            return match ($currency) {
                'KES' => 'shilling' . ($amount === 1 ? '' : 's') . ' kényan' . ($amount === 1 ? '' : 's'),
                'USD' => 'dollar' . ($amount === 1 ? '' : 's') . ' américain' . ($amount === 1 ? '' : 's'),
                'CDF' => 'franc' . ($amount === 1 ? '' : 's') . ' congolais',
            };
        }

        return match ($currency) {
            'KES' => $amount === 1
                ? 'Kenyan Shilling'
                : 'Kenyan Shillings',

            'USD' => $amount === 1
                ? 'US Dollar'
                : 'US Dollars',

            'CDF' => $amount === 1
                ? 'Congolese Franc'
                : 'Congolese Francs',
        };
    }

    /**
     * Currency minor-unit names.
     */
    protected function minorCurrencyName(
        string $currency,
        string $language,
        int $amount
    ): string {
        if ($language === 'fr') {
            return $amount === 1
                ? 'centime'
                : 'centimes';
        }

        return $amount === 1
            ? 'Cent'
            : 'Cents';
    }

    /**
     * English number conversion.
     */
    protected function numberToEnglish(int $number): string
    {
        if ($number === 0) {
            return 'zero';
        }

        if ($number < 0) {
            return 'minus ' . $this->numberToEnglish(
                abs($number)
            );
        }

        $scales = [
            1000000000000 => 'trillion',
            1000000000 => 'billion',
            1000000 => 'million',
            1000 => 'thousand',
        ];

        foreach ($scales as $value => $name) {
            if ($number >= $value) {
                $major = intdiv($number, $value);
                $remainder = $number % $value;

                $result =
                    $this->numberToEnglish($major)
                    . ' '
                    . $name;

                if ($remainder > 0) {
                    $result .= ' '
                        . $this->numberToEnglish($remainder);
                }

                return $result;
            }
        }

        if ($number >= 100) {
            $hundreds = intdiv($number, 100);
            $remainder = $number % 100;

            $result =
                $this->numberToEnglish($hundreds)
                . ' hundred';

            if ($remainder > 0) {
                $result .= ' '
                    . $this->numberToEnglish($remainder);
            }

            return $result;
        }

        $ones = [
            0 => 'zero',
            1 => 'one',
            2 => 'two',
            3 => 'three',
            4 => 'four',
            5 => 'five',
            6 => 'six',
            7 => 'seven',
            8 => 'eight',
            9 => 'nine',
            10 => 'ten',
            11 => 'eleven',
            12 => 'twelve',
            13 => 'thirteen',
            14 => 'fourteen',
            15 => 'fifteen',
            16 => 'sixteen',
            17 => 'seventeen',
            18 => 'eighteen',
            19 => 'nineteen',
        ];

        if ($number < 20) {
            return $ones[$number];
        }

        $tens = [
            20 => 'twenty',
            30 => 'thirty',
            40 => 'forty',
            50 => 'fifty',
            60 => 'sixty',
            70 => 'seventy',
            80 => 'eighty',
            90 => 'ninety',
        ];

        $tensValue = intdiv($number, 10) * 10;
        $remainder = $number % 10;

        if ($remainder === 0) {
            return $tens[$tensValue];
        }

        return $tens[$tensValue]
            . '-'
            . $this->numberToEnglish($remainder);
    }

    /**
     * French number conversion.
     */
    protected function numberToFrench(int $number): string
    {
        if ($number === 0) {
            return 'zéro';
        }

        if ($number < 0) {
            return 'moins ' . $this->numberToFrench(
                abs($number)
            );
        }

        if ($number >= 1000000000000) {
            $trillions = intdiv(
                $number,
                1000000000000
            );

            $remainder =
                $number % 1000000000000;

            $result =
                $this->numberToFrench($trillions)
                . ' '
                . ($trillions === 1
                    ? 'billion'
                    : 'billions');

            if ($remainder > 0) {
                $result .= ' '
                    . $this->numberToFrench($remainder);
            }

            return $result;
        }

        if ($number >= 1000000000) {
            $billions = intdiv(
                $number,
                1000000000
            );

            $remainder =
                $number % 1000000000;

            $result =
                $this->numberToFrench($billions)
                . ' '
                . ($billions === 1
                    ? 'milliard'
                    : 'milliards');

            if ($remainder > 0) {
                $result .= ' '
                    . $this->numberToFrench($remainder);
            }

            return $result;
        }

        if ($number >= 1000000) {
            $millions = intdiv(
                $number,
                1000000
            );

            $remainder =
                $number % 1000000;

            $result =
                $this->numberToFrench($millions)
                . ' '
                . ($millions === 1
                    ? 'million'
                    : 'millions');

            if ($remainder > 0) {
                $result .= ' '
                    . $this->numberToFrench($remainder);
            }

            return $result;
        }

        if ($number >= 1000) {
            $thousands = intdiv(
                $number,
                1000
            );

            $remainder = $number % 1000;

            if ($thousands === 1) {
                $result = 'mille';
            } else {
                $result =
                    $this->numberToFrench($thousands)
                    . ' mille';
            }

            if ($remainder > 0) {
                $result .= ' '
                    . $this->numberToFrench($remainder);
            }

            return $result;
        }

        if ($number >= 100) {
            $hundreds = intdiv($number, 100);
            $remainder = $number % 100;

            if ($hundreds === 1) {
                $result = 'cent';
            } else {
                $result =
                    $this->numberToFrench($hundreds)
                    . ' cent';

                if ($remainder === 0) {
                    $result .= 's';
                }
            }

            if ($remainder > 0) {
                $result .= ' '
                    . $this->numberToFrench($remainder);
            }

            return $result;
        }

        if ($number < 20) {
            return [
                0 => 'zéro',
                1 => 'un',
                2 => 'deux',
                3 => 'trois',
                4 => 'quatre',
                5 => 'cinq',
                6 => 'six',
                7 => 'sept',
                8 => 'huit',
                9 => 'neuf',
                10 => 'dix',
                11 => 'onze',
                12 => 'douze',
                13 => 'treize',
                14 => 'quatorze',
                15 => 'quinze',
                16 => 'seize',
                17 => 'dix-sept',
                18 => 'dix-huit',
                19 => 'dix-neuf',
            ][$number];
        }

        if ($number < 70) {
            $tens = intdiv($number, 10);
            $remainder = $number % 10;

            $names = [
                2 => 'vingt',
                3 => 'trente',
                4 => 'quarante',
                5 => 'cinquante',
                6 => 'soixante',
            ];

            $result = $names[$tens];

            if ($remainder === 1) {
                return $result . ' et un';
            }

            if ($remainder > 0) {
                $result .= '-'
                    . $this->numberToFrench($remainder);
            }

            return $result;
        }

        if ($number < 80) {
            $remainder = $number - 60;

            if ($remainder === 11) {
                return 'soixante et onze';
            }

            return 'soixante-'
                . $this->numberToFrench($remainder);
        }

        $remainder = $number - 80;

        if ($number === 80) {
            return 'quatre-vingts';
        }

        if ($remainder === 1) {
            return 'quatre-vingt-un';
        }

        return 'quatre-vingt-'
            . $this->numberToFrench($remainder);
    }
}