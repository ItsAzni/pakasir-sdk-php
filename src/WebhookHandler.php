<?php

declare(strict_types=1);

namespace ItsAzni\Pakasir;

use ItsAzni\Pakasir\Exceptions\PakasirException;

/**
 * Webhook handler for Pakasir payment notifications
 */
class WebhookHandler
{
    /** @var Pakasir|null */
    private $pakasir;

    /**
     * @param Pakasir|null $pakasir Optional Pakasir instance for verification
     */
    public function __construct(?Pakasir $pakasir = null)
    {
        $this->pakasir = $pakasir;
    }

    /**
     * Handle incoming webhook request
     *
     * @param string|array $body Request body (JSON string or array)
     * @return WebhookPayload
     */
    public function handle($body): WebhookPayload
    {
        if (is_string($body)) {
            $data = json_decode($body, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new PakasirException('Invalid JSON payload');
            }
        } else {
            $data = $body;
        }

        if (!isset($data['order_id'], $data['amount'], $data['project'], $data['status'], $data['payment_method'], $data['completed_at'])) {
            throw new PakasirException('Invalid webhook payload');
        }

        return WebhookPayload::fromArray($data);
    }

    /**
     * Handle and verify webhook with Pakasir API
     *
     * Parses the webhook and verifies the transaction
     * status with Pakasir API to ensure authenticity.
     *
     * @param string|array $body Request body
     * @return PaymentPayload Verified payment details from API
     * @throws PakasirException
     */
    public function handleAndVerify($body): PaymentPayload
    {
        if ($this->pakasir === null) {
            throw new PakasirException('Pakasir instance required for verification');
        }

        $webhook = $this->handle($body);

        // Verify project matches
        if ($webhook->project !== $this->pakasir->getSlug()) {
            throw new PakasirException('Project mismatch');
        }

        // Verify with API
        return $this->pakasir->detailPayment($webhook->order_id, $webhook->amount);
    }
}
