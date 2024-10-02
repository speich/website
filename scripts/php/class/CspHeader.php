<?php

namespace speich;

/**
 * Class CspHeader
 * Check header with https://cspscanner.com
 * or https://csp-evaluator.withgoogle.com/
 */
class CspHeader
{
    private static array $policies = [
        'default-src' => "'self'",
        'script-src' => "'self'",
        'img-src' => "'self'",
        'style-src' => "'self'",
        'base-uri' => "'self'",
        'form-action' => "'self'",
        'frame-ancestors' => "'none'",
        'frame-src' => "'none'",
        'child-src' => "'none'",
        'object-src' => "'none'",
        'worker-src' => "'none'"
    ];
    public string $nonceStyle;
    public string $nonceScript;

    public function __construct()
    {
        $this->nonceScript = $this->getNonce();
        $this->nonceStyle = $this->getNonce();
    }

    /**
     * @param string $directive
     * @param string $value
     */
    public function set(string $directive, string $value): void
    {
        self::$policies[$directive] = $value;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        $str = '';
        foreach (self::$policies as $directive => $value) {
            $str .= $directive.' '.$value.';';
        }

        return 'Content-Security-Policy: '.$str;
    }

    private function getNonce(): string
    {
        return bin2hex(string: openssl_random_pseudo_bytes(32));
    }
}