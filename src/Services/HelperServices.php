<?php

namespace App\Services;

class HelperServices
{
    /**
     * @param mixed $string
     * 
     * @return [string]
     */
    public static function transliteration(string $string): string
    {
        $converter = array(
            'а' => 'a',    'б' => 'b',    'в' => 'v',    'г' => 'g',    'д' => 'd',
            'е' => 'e',    'ё' => 'e',    'ж' => 'zh',   'з' => 'z',    'и' => 'i',
            'й' => 'y',    'к' => 'k',    'л' => 'l',    'м' => 'm',    'н' => 'n',
            'о' => 'o',    'п' => 'p',    'р' => 'r',    'с' => 's',    'т' => 't',
            'у' => 'u',    'ф' => 'f',    'х' => 'h',    'ц' => 'c',    'ч' => 'ch',
            'ш' => 'sh',   'щ' => 'sch',  'ь' => '',     'ы' => 'y',    'ъ' => '',
            'э' => 'e',    'ю' => 'yu',   'я' => 'ya',
        );

        $slug = mb_strtolower($string);
        // Убираем все кроме букв
        #$slug = preg_replace('/\PL/u', '', $slug);

        $slug = strtr($slug, $converter);
        $slug = str_replace(' ', '-', $slug);
        $slug = mb_ereg_replace('[^-a-z]', '', $slug);
        $slug = mb_ereg_replace('[-]+', '-', $slug);
        $slug = trim($slug, '-');

        return $slug;
    }
}
