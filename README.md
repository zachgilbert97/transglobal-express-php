# Transglobal Express PHP SDK
An **unofficial** PHP SDK for the Transglobal Express API. This publicly available package is not endorsed, developed, or maintained by Transglobal Express.

> Note: this package is still under development. You're welcome to fork and PR, but it's recommended that you use this package with caution.

## Table of contents
- [API documentation](#api-documentation)
- [Requirements](#requirements)
- [Installation](#installation)
- [Getting started](#getting-started)
- [Examples](#examples)
  - [Get Quote](#get-quote)
  - [Get Quote Minimal](#get-quote-minimal)
  - [Book Shipment](#book-shipment)
  - [Get Countries](#get-countries)
- [Response statuses and error handling](#response-statuses-and-error-handling)
- [Logging](#logging)
- [Contributing](#contributing)

## API documentation
Details of API endpoints are available at Transglobal Express:
- <a href="https://services3.transglobalexpress.co.uk/Documentation/">https://services3.transglobalexpress.co.uk/Documentation/</a> (documentation)
- <a href="https://services3.transglobalexpress.co.uk/">https://services3.transglobalexpress.co.uk/</a> (live)
- <a href="https://staging2.services3.transglobalexpress.co.uk/">https://staging2.services3.transglobalexpress.co.uk/</a> (staging)

> This package is developed based on the requesting and responses in JSON, not XML.

## Requirements
- PHP 8.3 or later
- Guzzle 7.15.4 or later (installed automatically)

## Installation
Via composer:

```shell
composer require zachgilbert/transglobal-express-php
```

## Getting started
Register an account with Transglobal Express to receive **staging** API credentials, and build and test your integration against staging first. Once you're ready to go live, request live credentials from Transglobal Express support.

> The API password is **not** the password you use to log in to the Transglobal Express website.

Pick the environment explicitly when creating the config, then create a client:

```php
use ZachGilbert\TransglobalExpress\Client;
use ZachGilbert\TransglobalExpress\Config;

$config = Config::staging(apiKey: 'your-api-key', password: 'your-api-password');
// $config = Config::live(apiKey: 'your-api-key', password: 'your-api-password');

$client = Client::make($config);
```

The client adds your credentials to every request. It exposes one resource per area of the API:

| Resource | Method | Endpoint |
| --- | --- | --- |
| `$client->quote()` | `get(Shipment, ?Insurance)` | `POST /Quote/V2/GetQuote` |
| `$client->quote()` | `getMinimal(Shipment)` | `POST /Quote/V2/GetQuoteMinimal` |
| `$client->shipment()` | `book(Shipment\|QuoteSelection, BookDetails)` | `POST /Book/V2/BookShipment` |
| `$client->countries()` | `get()` | `POST /Country/V2/GetCountries` |

Requests are built from DTOs in `ZachGilbert\TransglobalExpress\Dto`. Every field is optional in PHP, and unset (`null`) fields are left out of the request. It's up to you to supply the fields the API marks as mandatory for your shipment. Fields with a fixed set of values (item types, currencies, label sizes, etc.) accept either an enum from `ZachGilbert\TransglobalExpress\Enums` or the raw string.

A few conventions from the API apply to every request:
- Weights are in **kilograms**; lengths, widths and heights are in **centimetres**.
- Monetary values in responses are always in **GBP**. `ConsignmentCurrency` only applies to customs values.
- Countries can be given as a `countryId` or an ISO `countryCode`. If both are given, `countryId` wins. See [Get Countries](#get-countries).

## Examples

### Get Quote
Quote a shipment to see the available services and prices. Use the returned `QuoteID` and a `ServiceID` to book.

A full quote needs the full shipment details, because some surcharges depend on them (e.g. residential delivery or remote-area checks). For shipments with non-document items going to or from a country outside the EU, each package also needs its `CommodityDetails`.

```php
use ZachGilbert\TransglobalExpress\Dto\Address;
use ZachGilbert\TransglobalExpress\Dto\CommodityLine;
use ZachGilbert\TransglobalExpress\Dto\Consignment;
use ZachGilbert\TransglobalExpress\Dto\ConsignmentCurrency;
use ZachGilbert\TransglobalExpress\Dto\Country;
use ZachGilbert\TransglobalExpress\Dto\Insurance;
use ZachGilbert\TransglobalExpress\Dto\Package;
use ZachGilbert\TransglobalExpress\Dto\Shipment;
use ZachGilbert\TransglobalExpress\Enums\Currency;
use ZachGilbert\TransglobalExpress\Enums\ItemType;

$shipment = new Shipment(
    consignment: new Consignment(
        itemType: ItemType::Parcel,
        itemsAreStackable: true, // A surcharge is invoiced later if the carrier finds the items aren't stackable
        consignmentSummary: 'Cotton t-shirts',
        consignmentValue: 125.00,
        consignmentCurrency: new ConsignmentCurrency(Currency::GBP),
        packages: [
            new Package(
                weight: 2.5,  // kg
                length: 30.0, // cm
                width: 20.0,
                height: 10.0,
                commodityDetails: [
                    new CommodityLine(
                        commodityCode: '6109100010',
                        commodityDescription: 'Cotton t-shirts',
                        countryOfOrigin: new Country(countryCode: 'GB'),
                        numberOfUnits: 10,
                        unitValue: 12.50,
                        unitWeight: 0.25,
                    ),
                ],
            ),
        ],
    ),
    collectionAddress: new Address(
        forename: 'Jane',
        surname: 'Doe',
        emailAddress: 'jane@example.com',
        telephoneNumber: '01510000000',
        companyName: 'Example Ltd',
        addressLineOne: '1 High Street',
        city: 'Bromborough',
        postcode: 'CH62 3NX',
        country: new Country(countryCode: 'GB'),
        eoriNumber: 'GB123456789000',
    ),
    deliveryAddress: new Address(
        forename: 'John',
        surname: 'Smith',
        emailAddress: 'john@example.com',
        telephoneNumber: '2125550100',
        addressLineOne: '10 Wall Street',
        city: 'New York',
        county: 'NY', // Two-letter state code, required for the US and Canada
        postcode: '10005',
        country: new Country(countryCode: 'US'),
        isAddressResidential: true,
    ),
);

// Optional: leave out the second argument to quote without insurance
$insurance = new Insurance(
    coverValue: 125.00,
    excessValue: 0.00,
    goodsAreNew: true,
    goodsAreFragile: false,
);

$quote = $client->quote()->get($shipment, $insurance);

foreach ($quote->serviceResults ?? [] as $service) {
    printf(
        "#%d %s (%s): £%.2f, %s days\n",
        $service->serviceId,
        $service->serviceName,
        $service->carrierName,
        $service->totalCost?->totalCostGrossWithoutCollection,
        $service->transitTimeEstimate,
    );
}

$quoteId = $quote->quoteId; // Keep this, along with your chosen service ID, to book the shipment
```

For B2C shipments to an EU country with a total consignment value below €150, add `ProductIdentifiers` to each `CommodityLine`. Until 1 November 2026, leaving them out only produces a warning; from that date, booking fails without them. Identifiers sent with a quote are stored with it and reused when booking by quote ID.

```php
use ZachGilbert\TransglobalExpress\Dto\ProductIdentifiers;

new CommodityLine(
    // ...
    productIdentifiers: new ProductIdentifiers(
        merchantProductIdentifier: 'TSHIRT-M-BLUE',        // M-PID
        nonStandardisedProductIdentifier: 'SKU-48213',     // NS-PID
        standardisedProductIdentifier: '5012345678900',    // S-PID, e.g. a GTIN/EAN
        // noStandardisedIdentifierAvailable: true,        // Instead of the S-PID, when there isn't one
    ),
);
```

Each `ServiceQuoteResult` also includes:
- `servicePriceBreakdown`: the price elements in the total, e.g. `FRT` (freight), `COL` (collection), `RES` (residential), `STK` (non-stackable).
- `optionalExtras`: extras you can add when booking, e.g. `INS` (transit insurance).
- `expectedLabels`: the labels to expect when booking, with their role (`Primary` or `Collection`) and available sizes (`A4` or `Thermal`).
- `collectionOptions`: for warehouse services (`isWarehouseService`), the collection options and their charges. Pass the chosen `collectionOptionId` when booking.

> A quote stores the shipment details, **not** the price; prices are calculated when you book. Quotes aren't meant for long-term storage and may not be kept for more than 10 days, so quote shortly before booking.

### Get Quote Minimal
Get an estimate of the available services with as little information as possible: the item type, package weights and dimensions, and the city, postcode and country of each address.

```php
use ZachGilbert\TransglobalExpress\Dto\Address;
use ZachGilbert\TransglobalExpress\Dto\Consignment;
use ZachGilbert\TransglobalExpress\Dto\Country;
use ZachGilbert\TransglobalExpress\Dto\Package;
use ZachGilbert\TransglobalExpress\Dto\Shipment;
use ZachGilbert\TransglobalExpress\Enums\ItemType;

$estimate = $client->quote()->getMinimal(new Shipment(
    consignment: new Consignment(
        itemType: ItemType::Parcel,
        packages: [new Package(weight: 5.0, length: 30.0, width: 20.0, height: 10.0)],
    ),
    collectionAddress: new Address(city: 'Bromborough', postcode: 'CH62 3NX', country: new Country(countryCode: 'GB')),
    deliveryAddress: new Address(city: 'New York', postcode: '10005', country: new Country(countryCode: 'US')),
));

foreach ($estimate->serviceResults ?? [] as $service) {
    echo "{$service->serviceName}: £{$service->totalCost?->totalCostGrossWithoutCollection}\n";
}
```

You can pass the same `Shipment` you'd use for a full quote. Only the fields listed above are sent; anything else (and any empty part of the shipment) is left out.

> Minimal quotes can't be booked. The `QuoteID` is always `0`, so book by sending the full shipment details (see [Book Shipment](#book-shipment)) with the chosen `ServiceID`. Surcharges that depend on address details, such as residential delivery, may appear under `optionalExtras` rather than in the totals. Use [Get Quote](#get-quote) where possible for accurate prices.

### Book Shipment
Book a shipment by referencing an existing quote **or** by sending the full shipment details. Pass one or the other, never both.

**From a quote**, using the `QuoteID` and a `ServiceID` from [Get Quote](#get-quote):

```php
use ZachGilbert\TransglobalExpress\Dto\BookAccessory;
use ZachGilbert\TransglobalExpress\Dto\BookDetails;
use ZachGilbert\TransglobalExpress\Dto\Collection;
use ZachGilbert\TransglobalExpress\Dto\QuoteSelection;

$booking = $client->shipment()->book(
    new QuoteSelection(quoteId: $quoteId),
    new BookDetails(
        serviceId: 53,
        yourReference: 'ORDER-1001', // Searchable in your account, and printed on labels by some carriers
        deliveryInstructions: 'Leave with reception', // Carrier-specific, up to 64 characters
        collection: new Collection(
            collectionDate: '2026-11-02', // Y-m-d
            readyFrom: '12:30',           // H:i, defaults to 09:00
        ),
        bookAccessories: [new BookAccessory(code: 'SIG')], // Optional extras, e.g. signature on delivery
    ),
);
```

**With the full shipment details**, using the same `Shipment` as in [Get Quote](#get-quote):

```php
$booking = $client->shipment()->book($shipment, new BookDetails(serviceId: 53));
```

Leave out `collection` if you don't need a collection booked. For warehouse services, set `collectionOptionId` on the `Collection` to one of the service's `collectionOptions`. `BookDetails` also accepts `iossNumber`, `shippingCharges` and `includePreferredOriginNoticeOnPackingList` for the customs invoice, plus an `Insurance` DTO.

A successful booking returns the order reference, invoice, labels and documents:

```php
echo $booking->orderReference; // e.g. TP-0445469
echo $booking->trackingUrl;
echo $booking->orderInvoice?->totalGross; // GBP

foreach ($booking->labels ?? [] as $index => $label) {
    // $label->labelRole is "Primary" (to the destination) or "Collection" (to the depot, for warehouse services)
    file_put_contents("label-{$booking->orderReference}-{$index}.pdf", base64_decode($label->labelContent));
}

foreach ($booking->documents ?? [] as $document) {
    // e.g. PackingList; $document->downloadUrl always returns the latest version
    file_put_contents("{$document->documentType}-{$booking->orderReference}.pdf", base64_decode($document->content));
}
```

Labels and documents are currently always PDFs. A label's `downloadUrl` returns the latest version if the booking is later changed.

> Bookings are charged when they're made. Credit accounts can book up to their credit limit; card accounts need pre-paid credit, which is deducted at booking. Not having enough credit makes the booking fail. On staging, you can top up simulated credit from your dashboard.

If a booking fails (see [error handling](#response-statuses-and-error-handling)), Transglobal Express customer services won't act on it automatically. Fix the problem in the notifications and resubmit. If the response includes a `FailReference`, quote it when contacting support.

> Tracking pages on staging are IP-restricted, and carriers don't provide simulated tracking data.

### Get Countries
List the countries you can ship to and from with the API:

```php
$countries = $client->countries()->get();

foreach ($countries->countries ?? [] as $country) {
    echo "{$country->countryId}: {$country->title} ({$country->countryCode})\n";
}
```

Transglobal Express recommends using the `countryId` from this list rather than the ISO `countryCode`. One ISO code can cover several destinations, e.g. both "Spain (Mainland)" and "Canary Islands" use `ES`:

```php
use ZachGilbert\TransglobalExpress\Dto\Country;

$spainMainland = new Country(countryId: 205);
```

The list rarely changes, so consider caching it.

## Response statuses and error handling
Every response has a `status`: a `ZachGilbert\TransglobalExpress\Enums\ResponseStatus` (`SUCCESS`, `PARTIAL`, `FAIL` or `ERROR`), or the raw string if the API returns a status the package doesn't know yet. It also has a list of `notifications`, each with a `message` and `severity` (e.g. `ERROR` or `WARNING`).

Requests that fail throw an exception. All of them extend `ZachGilbert\TransglobalExpress\Exceptions\ApiException`, so catching that one class catches everything:

| Exception | Thrown when |
| --- | --- |
| `NetworkException` | The API couldn't be reached (connection refused, timeout, DNS, etc.) |
| `UnauthenticatedException` | HTTP 401 |
| `MethodNotAllowedException` | HTTP 405. `responseBody()['allowed_methods']` lists the allowed methods |
| `ValidationException` | HTTP 422 |
| `ApiException` | Any other HTTP error, a `FAIL` status in the response body, or a response that isn't valid JSON |

```php
use ZachGilbert\TransglobalExpress\Enums\ResponseStatus;
use ZachGilbert\TransglobalExpress\Exceptions\ApiException;
use ZachGilbert\TransglobalExpress\Exceptions\NetworkException;

try {
    $booking = $client->shipment()->book($shipment, $bookDetails);
} catch (NetworkException $exception) {
    // Safe to retry later
} catch (ApiException $exception) {
    // For a FAIL status, the message lists the notifications, e.g. "[FAIL] Postcode is invalid"
    $exception->getMessage();
    $exception->statusCode();
    $exception->responseBody(); // The decoded response, or ['raw_body' => '...'] if it wasn't JSON
}

// A PARTIAL status doesn't throw, so check the notifications for warnings
if ($booking->status === ResponseStatus::PARTIAL) {
    foreach ($booking->notifications ?? [] as $notification) {
        error_log("{$notification->severity}: {$notification->message}");
    }
}
```

## Logging
Pass any [PSR-3](https://www.php-fig.org/psr/psr-3/) logger (Monolog, Laravel's `Log` facade, etc.) to log every API call:

```php
use ZachGilbert\TransglobalExpress\Client;

$client = Client::make($config, $logger);

// In Laravel, e.g. with a dedicated channel:
$client = Client::make($config, Log::channel('transglobal'));
```

Each call writes two entries, linked by a `request_id`:

1. **A one-line summary**, logged at a level that matches the outcome:

   | Outcome | Level |
   | --- | --- |
   | `SUCCESS` or `PARTIAL` | `info` |
   | `FAIL` status, or an HTTP 4xx response | `warning` |
   | HTTP 5xx response, or a network failure | `error` |

   ```
   Transglobal Express Live POST /Book/V2/BookShipment responded 200 (842 ms)
   ```

   Its context includes the environment, method, endpoint, duration, HTTP and response statuses, plus the order reference, quote ID and notification messages when present. Network failures include the `exception`.

2. **The request and response bodies** at `debug` level, for troubleshooting.

Bodies are redacted before they're logged:
- The API password is always replaced with `[REDACTED]`, and the API key is masked to its last four characters (e.g. `****1a2b`).
- Base64 label and document content is replaced with its size, e.g. `[base64, 48.2 KB]`.
- Response bodies that aren't JSON (e.g. an HTML error page) are logged as text, cut off after 2,000 characters.

Use `LoggingOptions` to change what's logged:

```php
use ZachGilbert\TransglobalExpress\LoggingOptions;

$client = Client::make($config, $logger, new LoggingOptions(
    logBodies: false, // Only log the summaries, e.g. in production
    redactKeys: ['EmailAddress', 'TelephoneNumber', 'MobileNumber'], // Redact personal data at any depth
));
```

> Request and response bodies contain personal data, such as names, addresses and contact details. If you keep logs for auditing, use `redactKeys` or turn off `logBodies` to match your data retention policy.

## Contributing
Suggestions, fixes, and improvements are welcome! If you'd like to make a change to this package, please fork the repository and open a new PR when you're ready.

Make sure to outline the reason for the change and the pass results of any unit tests. To run the tests:

```shell
composer install
vendor/bin/pest
```
