<?php

declare(strict_types=1);

namespace ItsAzni\Pakasir;

/**
 * Payment response payload from Pakasir API
 */
class PaymentPayload
{
    /** @var string */
    public $project;

    /** @var string */
    public $order_id;

    /** @var int */
    public $amount;

    /** @var int */
    public $fee;

    /** @var string */
    public $status;

    /** @var int */
    public $total_payment;

    /** @var string */
    public $payment_method;

    /** @var string|null */
    public $payment_number;

    /** @var string|null */
    public $payment_url;

    /** @var string|null */
    public $redirect_url;

    /** @var string|null */
    public $expired_at;

    /** @var string|null */
    public $completed_at;

    /**
     * @param string $project
     * @param string $order_id
     * @param int $amount
     * @param int $fee
     * @param string $status
     * @param int $total_payment
     * @param string $payment_method
     * @param string|null $payment_number
     * @param string|null $payment_url
     * @param string|null $redirect_url
     * @param string|null $expired_at
     * @param string|null $completed_at
     */
    public function __construct(
        string $project,
        string $order_id,
        int $amount,
        int $fee,
        string $status,
        int $total_payment,
        string $payment_method,
        ?string $payment_number = null,
        ?string $payment_url = null,
        ?string $redirect_url = null,
        ?string $expired_at = null,
        ?string $completed_at = null
    ) {
        $this->project = $project;
        $this->order_id = $order_id;
        $this->amount = $amount;
        $this->fee = $fee;
        $this->status = $status;
        $this->total_payment = $total_payment;
        $this->payment_method = $payment_method;
        $this->payment_number = $payment_number;
        $this->payment_url = $payment_url;
        $this->redirect_url = $redirect_url;
        $this->expired_at = $expired_at;
        $this->completed_at = $completed_at;
    }

    /**
     * Create PaymentPayload from API response array
     *
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['project'] ?? '',
            $data['order_id'] ?? '',
            (int) ($data['amount'] ?? 0),
            (int) ($data['fee'] ?? 0),
            $data['status'] ?? 'pending',
            (int) ($data['total_payment'] ?? 0),
            $data['payment_method'] ?? '',
            $data['payment_number'] ?? null,
            $data['payment_url'] ?? null,
            $data['redirect_url'] ?? null,
            $data['expired_at'] ?? null,
            $data['completed_at'] ?? null
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
     * Check if payment is pending
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if payment is canceled
     *
     * @return bool
     */
    public function isCanceled(): bool
    {
        return $this->status === 'canceled';
    }

    /**
     * Convert to array
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'project' => $this->project,
            'order_id' => $this->order_id,
            'amount' => $this->amount,
            'fee' => $this->fee,
            'status' => $this->status,
            'total_payment' => $this->total_payment,
            'payment_method' => $this->payment_method,
            'payment_number' => $this->payment_number,
            'payment_url' => $this->payment_url,
            'redirect_url' => $this->redirect_url,
            'expired_at' => $this->expired_at,
            'completed_at' => $this->completed_at,
        ];
    }
}
