<?php

namespace RingleSoft\JasminClient\Enums;

enum DlrLevelsEnum: int
{
    /**
     * This level indicates that the delivery receipt will be generated at the SMSC level only
     */
    case SMSC = 1;

    /**
     * The delivery receipt is generated when the message reaches the recipient's device (terminal).
     */
    case TERMINAL = 2;

    /**
     * Delivery receipts will be generated both at the SMSC level and when the message is delivered to the terminal
     */
    case BOTH = 3;
}
