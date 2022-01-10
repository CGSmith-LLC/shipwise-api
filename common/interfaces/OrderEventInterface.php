<?php

namespace common\interfaces;

/**
 * OrderEventInterface
 *
 * Uses the event system to tell you what status the order transitions to
 */
class OrderEventInterface
{
    const EVENT_CANCEL = 'cancel';
}