<?php

class HttpDlr
{
    private string $id; // UUID
    private string $smscId;
    private string $messageStatus;
    private int $level;
    private string $connector;
    private ?string $submittedDdate;
    private ?string $doneDate;
    private ?int $submittedCount;
    private ?int $deliveredCunt;
    private ?int $error;
    private ?string $text;

    public function __construct(string $id, string $smscId, string $messageStatus, int $level, string $connector, ?string $submittedDdate, ?string $doneDate, ?int $submittedCount, ?int $deliveredCunt, ?int $error, ?string $text) {
        $this->id = $id;
        $this->smscId = $smscId;
        $this->messageStatus = $messageStatus;
        $this->level = $level;
        $this->connector = $connector;
        $this->submittedDdate = $submittedDdate;
        $this->doneDate = $doneDate;
        $this->submittedCount = $submittedCount;
        $this->deliveredCunt = $deliveredCunt;
        $this->error = $error;
        $this->text = $text;
    }


}
