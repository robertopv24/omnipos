<?php

namespace OmniPOS\Core;

class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function set(string $key, $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, $default = null)
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }

    public static function destroy(): void
    {
        self::start();
        session_destroy();
        $_SESSION = [];
    }

    public static function regenerate(): void
    {
        self::start();
        session_regenerate_id(true);
    }

    /**
     * Almacena un mensaje flash que se eliminará después de ser leído.
     */
    public static function flash(string $key, $value): void
    {
        self::start();
        if (!isset($_SESSION['_flash'])) {
            $_SESSION['_flash'] = [];
        }
        $_SESSION['_flash'][$key] = $value;
    }

    /**
     * Obtiene y elimina un mensaje flash.
     */
    public static function getFlash(string $key, $default = null)
    {
        self::start();
        $value = $_SESSION['_flash'][$key] ?? $default;
        unset($_SESSION['_flash'][$key]);
        return $value;
    }

    /**
     * Verifica si existe un mensaje flash.
     */
    public static function hasFlash(string $key): bool
    {
        self::start();
        return isset($_SESSION['_flash'][$key]);
    }
}
