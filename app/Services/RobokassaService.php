<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class RobokassaService
{
    private string $merchantLogin;
    private string $password1;
    private string $password2;
    private string $hashType;
    private bool $isTest;
    private string $baseUrl;

    public function __construct()
    {
        $this->merchantLogin = config('robokassa.merchant_login', '');
        $this->password1 = config('robokassa.password1', '');
        $this->password2 = config('robokassa.password2', '');
        $this->hashType = config('robokassa.hash_type', 'md5');
        $this->isTest = config('robokassa.is_test', false);
        
        // URL для тестового или продакшн режима
        if ($this->isTest) {
            $this->baseUrl = 'https://auth.robokassa.ru/Merchant/Index.aspx';
        } else {
            $this->baseUrl = 'https://auth.robokassa.ru/Merchant/Index.aspx';
        }
    }

    /**
     * Генерация подписи для оплаты
     * Формат: MerchantLogin:OutSum:InvId:Password1
     */
    public function generatePaymentSignature(float $amount, int $invoiceId, ?string $description = null): string
    {
        $signatureString = sprintf(
            '%s:%s:%s:%s',
            $this->merchantLogin,
            number_format($amount, 2, '.', ''),
            $invoiceId,
            $this->password1
        );

        return $this->hash($signatureString);
    }

    /**
     * Генерация подписи для уведомлений (Result URL)
     * Формат: OutSum:InvId:Password2
     */
    public function generateResultSignature(float $amount, int $invoiceId): string
    {
        $signatureString = sprintf(
            '%s:%s:%s',
            number_format($amount, 2, '.', ''),
            $invoiceId,
            $this->password2
        );

        return $this->hash($signatureString);
    }

    /**
     * Проверка подписи от Робокассы (для уведомлений)
     */
    public function verifyResultSignature(float $amount, int $invoiceId, string $signature): bool
    {
        $expectedSignature = $this->generateResultSignature($amount, $invoiceId);
        return strtoupper($signature) === strtoupper($expectedSignature);
    }

    /**
     * Создание URL для перенаправления на оплату
     */
    public function getPaymentUrl(float $amount, int $invoiceId, string $description, array $additionalParams = []): string
    {
        $params = array_merge([
            'MerchantLogin' => $this->merchantLogin,
            'OutSum' => number_format($amount, 2, '.', ''),
            'InvId' => $invoiceId,
            'Description' => $description,
            'SignatureValue' => $this->generatePaymentSignature($amount, $invoiceId, $description),
            'IsTest' => $this->isTest ? 1 : 0,
            'Encoding' => 'utf-8',
        ], $additionalParams);

        return $this->baseUrl . '?' . http_build_query($params);
    }

    /**
     * Хеширование строки в зависимости от настроек
     */
    private function hash(string $string): string
    {
        switch (strtolower($this->hashType)) {
            case 'sha256':
            case 'sha-256':
                return hash('sha256', $string);
            case 'sha512':
            case 'sha-512':
                return hash('sha512', $string);
            case 'md5':
            default:
                return md5($string);
        }
    }

    /**
     * Проверка конфигурации
     */
    public function isConfigured(): bool
    {
        return !empty($this->merchantLogin) && 
               !empty($this->password1) && 
               !empty($this->password2);
    }
}
