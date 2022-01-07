# Jobs

Shipwise uses a queue system to manage jobs. The overall idea is to put things 
into the queue so they become easier to manage. This will assist with when we 
are growing into other verticals or need to add more jobs.

**Take the [Unix philosophy](https://en.wikipedia.org/wiki/Unix_philosophy#Origin)** when you are creating jobs. Make each program do
one thing well.

## General Job Workflow

Each block in green is a separate job. This is typically how jobs will flow 
through the system with webhooks for orders.

![General Job Workflow](General%20Job%20Workflow.png "General Job Workflow")


#### Notification Job

Send a notification to the customer about an urgent message. Should not be used
for invoices or anything like that. We want a response from them.

Useful for things like: Order Issue, Inventory Low, etc.

```php
<?php

class NotificationJob {
    // Specific message to user about what this notification is about
    public string $message;
    
    // Optional - order number that will be linked to if there is one
    public ?string $customer_reference;
    
    // Customer ID that we use to lookup users to notify
    public string $customer_id;
    
    // General reason is put into a sentance 'We are notifying you about {$reason_general}'
    public string $reason_general;
    
    // Reason specific is a detailed message that you want to tell the user. What they should do about it
    public string $reason_specific;
    
}    
```

![Notification Flow](notification.png)