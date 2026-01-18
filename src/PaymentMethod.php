<?php

declare(strict_types=1);

namespace ItsAzni\Pakasir;

/**
 * Available payment methods for Pakasir
 */
class PaymentMethod
{
    public const QRIS = 'qris';
    public const BNI_VA = 'bni_va';
    public const BRI_VA = 'bri_va';
    public const CIMB_NIAGA_VA = 'cimb_niaga_va';
    public const SAMPOERNA_VA = 'sampoerna_va';
    public const BNC_VA = 'bnc_va';
    public const MAYBANK_VA = 'maybank_va';
    public const PERMATA_VA = 'permata_va';
    public const ATM_BERSAMA_VA = 'atm_bersama_va';
    public const ARTHA_GRAHA_VA = 'artha_graha_va';
    public const PAYPAL = 'paypal';

    /**
     * Get all available payment methods
     *
     * @return array<string>
     */
    public static function all(): array
    {
        return [
            self::QRIS,
            self::BNI_VA,
            self::BRI_VA,
            self::CIMB_NIAGA_VA,
            self::SAMPOERNA_VA,
            self::BNC_VA,
            self::MAYBANK_VA,
            self::PERMATA_VA,
            self::ATM_BERSAMA_VA,
            self::ARTHA_GRAHA_VA,
            self::PAYPAL,
        ];
    }

    /**
     * Check if a payment method is valid
     *
     * @param string $method
     * @return bool
     */
    public static function isValid(string $method): bool
    {
        return in_array($method, self::all(), true);
    }
}
