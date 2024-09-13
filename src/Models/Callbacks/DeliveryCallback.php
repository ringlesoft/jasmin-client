<?php

namespace RingleSoft\JasminClient\Models\Callbacks;

class DeliveryCallback
{
    /**
     * Internal Jasmin’s gateway message id used for tracking messages
     * @var string
     */
    private string $id; // UUID

    /**
     * Message id returned from the SMS-C
     * @var string
     */
    public ?string $smscId = null;

    /**
     * Delivery status
     * @var string
     */
    public string $messageStatus;

    /**
     * This is a static value indicating the dlr-level originally requested
     * @var int
     */
    public int $level;

    /**
     * The SMPP Connector used to send the message
     * @var string
     */
    public string $connector;

    /**
     * The time and date at which the short message was submitted
     * @var string|null
     */
    public ?string $submittedDate;

    /**
     * The time and date at which the short message reached it’s final state
     * @var string|null
     */
    public ?string $doneDate;

    /**
     * Number of short messages originally submitted. This is only relevant when the original message was submitted to a distribution list.The value is padded with leading zeros if necessary
     * @var int|null
     */
    public ?int $submittedCount;

    /**
     * Number of short messages delivered. This is only relevant where the original message was submitted to a distribution list.The value is padded with leading zeros if necessary
     * @var int|null
     */
    public ?int $deliveredCount;

    /**
     * Where appropriate this may hold a Network specific error code or an SMSC error code for the attempted delivery of the message
     * @var int|null
     */
    public ?int $error;

    /**
     * The first 20 characters of the short message
     * @var string|null
     */
    public ?string $text;

    public function __construct(string $id, string $messageStatus, int $level, string $connector, ?string $smscId,  ?string $submittedDate, ?string $doneDate, ?int $submittedCount, ?int $deliveredCunt, ?int $error, ?string $text) {
        $this->id = $id;
        $this->smscId = $smscId;
        $this->messageStatus = $messageStatus;
        $this->level = $level;
        $this->connector = $connector;
        $this->submittedDate = $submittedDate;
        $this->doneDate = $doneDate;
        $this->submittedCount = $submittedCount;
        $this->deliveredCount = $deliveredCunt;
        $this->error = $error;
        $this->text = $text;
    }

    public static function rules(): array
    {
        return [
            'id' => 'required|uuid',
            'message_status' => 'required',
            'level' => 'required|integer|in:1,2,3',
            'connector' => 'required|string',
            'id_smsc' => 'nullable|integer',
            'subdate' => 'nullable',
            'donedate' => 'nullable',
            'sub' => 'nullable',
            'dlvrd' => 'nullable',
            'err' => 'nullable',
            'text' => 'nullable|string|max:25',
        ];
    }


    public function getMessageId(): string
    {
        return $this->id;
    }
}
