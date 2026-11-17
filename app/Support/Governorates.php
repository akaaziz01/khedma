<?php

namespace App\Support;

class Governorates
{
    /**
     * The 24 official Tunisian governorates.
     */
    public const ALL = [
        'Ariana',
        'Beja',
        'Ben Arous',
        'Bizerte',
        'Gabes',
        'Gafsa',
        'Jendouba',
        'Kairouan',
        'Kasserine',
        'Kebili',
        'Kef',
        'Mahdia',
        'Manouba',
        'Medenine',
        'Monastir',
        'Nabeul',
        'Sfax',
        'Sidi Bouzid',
        'Siliana',
        'Sousse',
        'Tataouine',
        'Tozeur',
        'Tunis',
        'Zaghouan',
    ];

    /**
     * Get all governorates in standard English format.
     *
     * @return list<string>
     */
    public static function all(): array
    {
        return self::ALL;
    }

    /**
     * Check if a given city matches one of the 24 Tunisian governorates.
     * Supports case-insensitivity and standard French accented variants (e.g., Béja, Gabès, Médenine).
     */
    public static function isValid(?string $city): bool
    {
        if ($city === null || trim($city) === '') {
            return false;
        }

        $normalizedInput = self::normalize(trim($city));

        foreach (self::ALL as $governorate) {
            if (strcasecmp(self::normalize($governorate), $normalizedInput) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Normalize accented characters for comparison.
     */
    public static function normalize(string $value): string
    {
        $accents = [
            'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
            'É' => 'E', 'È' => 'E', 'Ê' => 'E', 'Ë' => 'E',
            'à' => 'a', 'â' => 'a', 'ä' => 'a',
            'À' => 'A', 'Â' => 'A', 'Ä' => 'A',
            'î' => 'i', 'ï' => 'i',
            'Î' => 'I', 'Ï' => 'I',
            'ô' => 'o', 'ö' => 'o',
            'Ô' => 'O', 'Ö' => 'O',
            'ù' => 'u', 'û' => 'u', 'ü' => 'u',
            'Ù' => 'U', 'Û' => 'U', 'Ü' => 'U',
            'ç' => 'c', 'Ç' => 'C',
        ];

        return strtr($value, $accents);
    }
}
