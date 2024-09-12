<?php

namespace RingleSoft\JasminClient\Enums;

enum SmppPduEnum: String
{
case BIND_TRANSMITTER = 'bind_transmitter';
case BIND_TRANSCEIVER = 'bind_transceiver';
case BIND_RECEIVER = 'bind_receiver';
case UNBIND = 'unbind';
case SUBMIT_SM = 'submit_sm';
case DELIVER_SM = 'deliver_sm';
case ENQUIRE_LINK = 'enquire_link';
}
