<?php

namespace RingleSoft\JasminCLient\Enums;

enum DataCodingSchemesEnum: int
{
    case SMSC_DEFAULT_ALPHABET = 0;
    case ASCII_LATIN9 = 1;
    case OCTET_UNSPECIFIED1 = 2;
    case LATIN1 = 3;
    case OCTET_UNSPECIFIED2 = 4;
    case JIS = 5;
    case CYRILLIC = 6;
    case LATIN_HEBREW = 7;
    case UCS2_UTF16 = 8;
    case PICTOGRAM_ENCODING = 9;
    case MUSIC_CODES = 10;
    case EXTENDED_KANJI_JIS = 13;
    case KOREAN_GRAPHIC_CHARACTER_SET = 14;

    public function description(): string
    {
        return match ($this) {
            self::SMSC_DEFAULT_ALPHABET => 'SMSC Default Alphabet – GSM 7-bit (Default for ASCII and GSM)',
            self::ASCII_LATIN9 => 'ASCII for short and long code, Latin 9 (ISO-8859-9)',
            self::OCTET_UNSPECIFIED1 => 'Octet Unspecified (8-bit binary)',
            self::LATIN1 => 'Latin 1 (ISO-8859-1)',
            self::OCTET_UNSPECIFIED2 => 'Octet Unspecified (8-bit binary)',
            self::JIS => 'JIS (X 0208-1990)',
            self::CYRILLIC => 'Cyrillic (ISO-8859-5)',
            self::LATIN_HEBREW => 'Latin/Hebrew (ISO-8859-8)',
            self::UCS2_UTF16 => 'UCS2/UTF-16 (ISO/IEC-10646)',
            self::PICTOGRAM_ENCODING => 'Pictogram Encoding',
            self::MUSIC_CODES => 'Music Codes (ISO-2022-JP)',
            self::EXTENDED_KANJI_JIS => 'Extended Kanji JIS (X 0212-1990)',
            self::KOREAN_GRAPHIC_CHARACTER_SET => 'Korean Graphic Character Set (KS C 5601/KS X 1001)',
        };
    }
}
