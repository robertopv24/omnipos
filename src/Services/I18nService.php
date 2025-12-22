<?php

namespace OmniPOS\Services;

class I18nService
{
    protected static array $translations = [];
    protected static string $locale = 'es';

    public static function setLocale(string $locale): void
    {
        self::$locale = $locale;
        self::loadTranslations();
    }

    public static function getLocale(): string
    {
        return self::$locale;
    }

    protected static function loadTranslations(): void
    {
        $path = __DIR__ . "/../I18n/" . self::$locale . ".json";
        if (file_exists($path)) {
            $content = file_get_contents($path);
            self::$translations = json_decode($content, true) ?: [];
        }
    }

    public static function translate(string $key, array $params = []): string
    {
        if (empty(self::$translations)) {
            self::loadTranslations();
        }

        $text = self::$translations[$key] ?? $key;

        foreach ($params as $k => $v) {
            $text = str_replace(":$k", $v, $text);
        }

        return $text;
    }
}
