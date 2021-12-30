#Introduction to Shipwise
This is meant to provide an overview of how Shipwise is used from a customer perspective. This will contain the inner
workings of how Shipwise works and **MUST NOT BE SHARED WITH EXTERNAL SOURCES**.

##Problems Shipwise Solves
* Connects ecommerce systems to one management platform
* Connect to 3rd party logistics (3PL) companies (shipping companies)
* Downloads tracking info from 3PLs and sends back upstream to ecommerce system

##How does a small client use Shipwise?

A customer like The Elegant Farmer uses Shipwise to print shipping labels. They have a WooCommerce store that is 
connected in Shipwise. Each morning they print UPS labels and packing slips so they can ship the orders out. 
When the orders are printed the ecommerce system will receive the tracking numbers.

Shipwise is used by them as a management platform.

##How does a customer that uses a 3PL work?
Shipwise will be contacted by a 3PL to connect with a customer's ecommerce site to start importing orders. We use the 
appropriate API to download orders and normalize the data. When the data is normalized the 3PL applies business logic 
to each order. They call this cartonization. Cartonization just tells what box and other packaging material is necessary
to fulfill a single order.

For example: Talia di Napoli uses Coldco Logistics as their 3PL. Coldco contracts Shipwise to make sure all necessary 
orders come into their system. Coldco applies business rules to the orders to allow a streamlined operation on the 
fulfillment side of things.

Shipwise acts as the integration platform for Coldco Logistics and is the conduit between orders being placed and order 
fulfillment.

##How does a client use Shipwise?
A client will create an account on Shipwise. After creating an account they will:

* Connect to an ecommerce store(WooCommerce, Shopify, Amazon... etc)
* Connect to a 3PL (optional)
* Apply business rules to orders (optional)

Clients usually only need to configure an integration between ecommerce systems and fulfillment centers. 
Business logic and rules (aka Behaviors) will be added to Shipwise which will allow more control. Customers currently
only use Shipwise to login to see if there are issues with any orders being imported into the 3PL's system 
(shown as WMS Error status in orders).

##How does a 3PL use Shipwise?
A 3PL will provide us with a customer contact and connection that we need to configure. In the future the process will
look like this:

* 3PL will use their Shipwise login to create a new customer
* 3PL will invite their customer via email.
* Customer will authorize ecommerce connection (3PL can do this with entering in API keys)
* Subscription will be added to 3PL's account for Shipwise to bill
* 3PL will configure behaviors (business rules)
* Integration can be enabled/paused by 3PL or customer

The 3PL currently relies on CGSmith to create the integration and apply the behaviors which live in a separate PHP 
project. This is all vanilla PHP. These behaviors can do a few business rules:

* Check transit time on an order
* Add packaging items based on order data
* Route order to specific 3PL warehouse depending on state
* Add special SKUs within certain dates (Valentine Day Special Pizza for example)

#Miscellaneous Questions

> Why do I need another ecommerce service for tracking numbers?

Shipwise just uploads tracking to WooCommerce, Shopify, or any other ecommerce platform. Without Shipwise the tracking 
done in Shipwise or at the 3PL would need to be uploaded manually.

> What is a Shipwise Carrier on the user flow diagram?

This is internal only. Within the database we can set customer settings. For example, when Elegant Farmer creates a 
shipping label we create a shipping label under their account using their API credentials for UPS - this is all stored 
in customer_meta.

Shipwise Carrier will become more useful in the future. We are negotiating rates with FedEx, UPS, and other carriers 
and will offer those rates with markup to our customers. If a customer uses our rates we will then bill them via credit 
card for their label that is created. This is yet to be developed.