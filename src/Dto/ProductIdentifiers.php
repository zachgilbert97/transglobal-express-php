<?php

namespace ZachGilbert\TransglobalExpress\Dto;

use ZachGilbert\TransglobalExpress\Concerns\Arrayable;
use ZachGilbert\TransglobalExpress\Concerns\HasSerializableProperties;
use ZachGilbert\TransglobalExpress\Traits\SerializesProperties;

/**
 * Product identifiers required for B2C shipments to an EU country with a total consignment value below €150.
 *
 * From 1 November 2026, booking such a shipment without them fails. Identifiers sent with a quote are
 * stored with it and reused when booking by quote ID.
 */
final class ProductIdentifiers implements Arrayable, HasSerializableProperties
{
    use SerializesProperties;

    /**
     * @param string|null $merchantProductIdentifier         M-PID, up to 100 characters
     * @param string|null $nonStandardisedProductIdentifier  NS-PID, up to 100 characters
     * @param string|null $standardisedProductIdentifier     S-PID, up to 100 characters. Not required when
     *                                                       $noStandardisedIdentifierAvailable is true
     * @param bool|null   $noStandardisedIdentifierAvailable When true, "No" is sent to the carrier in place of the S-PID
     */
    public function __construct(
        readonly public ?string $merchantProductIdentifier = null,
        readonly public ?string $nonStandardisedProductIdentifier = null,
        readonly public ?string $standardisedProductIdentifier = null,
        readonly public ?bool $noStandardisedIdentifierAvailable = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function properties(): array
    {
        return [
            'MerchantProductIdentifier' => $this->merchantProductIdentifier,
            'NonStandardisedProductIdentifier' => $this->nonStandardisedProductIdentifier,
            'StandardisedProductIdentifier' => $this->standardisedProductIdentifier,
            'NoStandardisedIdentifierAvailable' => $this->noStandardisedIdentifierAvailable,
        ];
    }

    /**
     * @param array $data
     *
     * @return static
     */
    public static function fromArray(array $data): static
    {
        return new self(
            merchantProductIdentifier: $data['MerchantProductIdentifier'] ?? null,
            nonStandardisedProductIdentifier: $data['NonStandardisedProductIdentifier'] ?? null,
            standardisedProductIdentifier: $data['StandardisedProductIdentifier'] ?? null,
            noStandardisedIdentifierAvailable: $data['NoStandardisedIdentifierAvailable'] ?? null,
        );
    }
}
