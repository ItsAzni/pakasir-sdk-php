<?php

declare(strict_types=1);

namespace ItsAzni\Pakasir;

/**
 * Webhook payload from Pakasir
 *
 * Sent when a customer successfully completes a payment
 */
class WebhookPayload
{
    /** @var int */
    public $amount;

    /** @var string */
    public $order_id;

    /** @var string */
    public $project;

    /** @var string */
    public $status;

    /** @var string */
    public $payment_method;

    /** @var string */
    public $completed_at;

    /**
     * @param int $amount
     * @param string $order_id
     * @param string $project
     * @param string $status
     * @param string $payment_method
     * @param string $completed_at
     */
    public function __construct(
        int $amount,
        string $order_id,
        string $project,
        string $status,
        string $payment_method,
        string $completed_at
    ) {
        $this->amount = $amount;
        $this->order_id = $order_id;
        $this->project = $project;
        $this->status = $status;
        $this->payment_method = $payment_method;
        $this->completed_at = $completed_at;
    }

    /**
     * Create WebhookPayload from array
     *
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (int) ($data['amount'] ?? 0),
            $data['order_id'] ?? '',
            $data['project'] ?? '',
            $data['status'] ?? '',
            $data['payment_method'] ?? '',
            $data['completed_at'] ?? ''
        );
    }

    /**
     * Check if payment is completed
     *
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Convert to array
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'amount' => $this->amount,
            'order_id' => $this->order_id,
            'project' => $this->project,
            'status' => $this->status,
            'payment_method' => $this->payment_method,
            'completed_at' => $this->completed_at,
        ];
    }
}
