<?php

/** @var string	$customerName */
/** @var int	$invoiceNumber */
/** @var string	$errorMessage */

?>

<h3>There was a problem processing your payment</h3>
<p>Hello <?= $customerName ?>,</p>
<br/>
<br/>
<p>There was a problem processing payment for Invoice #<?= $invoiceNumber ?>. Please contact us via email or phone to correct the issue.</p>
<br/>
<br/>
<p>Our billing system will try the charge again the following day. The payment processor replied with: <?= $errorMessage ?></p>
<p>Please note: A 1.5% finance charge will be applied to all past due invoices</p>