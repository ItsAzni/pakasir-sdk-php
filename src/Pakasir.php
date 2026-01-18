<?php

declare(strict_types=1);

namespace ItsAzni\Pakasir;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use ItsAzni\Pakasir\Exceptions\PakasirException;

/**
 * Pakasir SDK - PHP SDK for Pakasir Payment Link
 *
 * @author ItsAzni
 * @link https://pakasir.com
 */
class Pakasir
{
    private const BASE_URL = 'https://app.pakasir.com';

    /** @var Client */
    private $client;

    /** @var string */
    private $slug;

    /** @var string */
    private $apiKey;

    /**
     * Initialize Pakasir SDK
     *
     * @param string $slug Your project slug from Pakasir dashboard
     * @param string $apiKey Your API key from Pakasir dashboard
     * @throws PakasirException
     */
    public function __construct(string $slug, string $apiKey)
    {
        if (empty($slug) || empty($apiKey)) {
            throw new PakasirException('Slug and API key are required');
        }

        $this->slug = $slug;
        $this->apiKey = $apiKey;
        $this->client = new Client([
            'base_uri' => self::BASE_URL,
            'timeout' => 30,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);
    }

    /**
     * Create a new payment transaction
     *
     * @param string $method Payment method (e.g., 'qris', 'bni_va', PaymentMethod::QRIS)
     * @param string $orderId Your internal transaction or invoice ID
     * @param int $amount The transaction amount
     * @param string|null $redirect Optional redirect URL after payment success
     * @return PaymentPayload
     * @throws PakasirException
     */
    public function createPayment(
        string $method,
        string $orderId,
        int $amount,
        ?string $redirect = null
    ): PaymentPayload {
        $payload = [
            'project' => $this->slug,
            'order_id' => $orderId,
            'amount' => $amount,
            'api_key' => $this->apiKey,
        ];

        if ($redirect !== null) {
            $payload['redirect'] = $redirect;
        }

        try {
            $response = $this->client->post("/api/transactioncreate/{$method}", [
                'json' => $payload,
            ]);

            $body = json_decode($response->getBody()->getContents(), true);

            if (!isset($body['payment'])) {
                throw new PakasirException('Invalid response from Pakasir API');
            }

            $paymentData = $body['payment'];

            $paymentUrl = $this->getPaymentUrl($orderId, $amount, $redirect);
            $paymentData['payment_url'] = $paymentUrl;
            $paymentData['redirect_url'] = $redirect;
            $paymentData['status'] = $paymentData['status'] ?? 'pending';

            return PaymentPayload::fromArray($paymentData);
        } catch (GuzzleException $e) {
            throw new PakasirException('Failed to create payment: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Get payment transaction details
     *
     * @param string $orderId The order ID
     * @param int $amount The transaction amount
     * @return PaymentPayload
     * @throws PakasirException
     */
    public function detailPayment(string $orderId, int $amount): PaymentPayload
    {
        try {
            $response = $this->client->get('/api/transactiondetail', [
                'query' => [
                    'project' => $this->slug,
                    'order_id' => $orderId,
                    'amount' => $amount,
                    'api_key' => $this->apiKey,
                ],
            ]);

            $body = json_decode($response->getBody()->getContents(), true);

            if (!isset($body['transaction'])) {
                throw new PakasirException('Invalid response from Pakasir API');
            }

            $transactionData = $body['transaction'];

            $paymentUrl = $this->getPaymentUrl($orderId, $amount);
            $transactionData['payment_url'] = $paymentUrl;
            $transactionData['fee'] = $transactionData['fee'] ?? 0;
            $transactionData['total_payment'] = $transactionData['total_payment'] ?? $amount;

            return PaymentPayload::fromArray($transactionData);
        } catch (GuzzleException $e) {
            throw new PakasirException('Failed to get payment detail: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Generate payment URL for redirect
     *
     * @param string $orderId The order ID
     * @param int $amount The transaction amount
     * @param string|null $redirect Optional redirect URL after payment
     * @param bool $qrisOnly Force QRIS payment method display
     * @return string
     */
    public function getPaymentUrl(
        string $orderId,
        int $amount,
        ?string $redirect = null,
        bool $qrisOnly = false
    ): string {
        $url = self::BASE_URL . "/pay/{$this->slug}/{$amount}?order_id=" . urlencode($orderId);

        if ($redirect !== null) {
            $url .= '&redirect=' . urlencode($redirect);
        }

        if ($qrisOnly) {
            $url .= '&qris_only=1';
        }

        return $url;
    }

    /**
     * Generate PayPal payment URL for redirect
     *
     * @param string $orderId The order ID
     * @param int $amount The transaction amount (minimum Rp10,000)
     * @return string
     */
    public function getPaypalUrl(string $orderId, int $amount): string
    {
        return self::BASE_URL . "/paypal/{$this->slug}/{$amount}?order_id=" . urlencode($orderId);
    }

    /**
     * Get current project slug
     *
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }
}
