<?php

namespace ZachGilbert\TransglobalExpress\Responses;

use ZachGilbert\TransglobalExpress\Concerns\Response;
use ZachGilbert\TransglobalExpress\Dto\Country;
use ZachGilbert\TransglobalExpress\Dto\Notification;
use ZachGilbert\TransglobalExpress\Enums\ResponseStatus;

final class GetCountriesResponse implements Response
{
    /**
     * @param ResponseStatus|string|null $status
     * @param array<Notification>|null   $notifications
     * @param array<Country>|null        $countries
     */
    public function __construct(
        public readonly ResponseStatus|string|null $status = null,
        public readonly ?array $notifications = null,
        public readonly ?array $countries = null,
    ) {}

    /**
     * @param array $data
     *
     * @return static
     */
    public static function fromArray(array $data): static
    {
        return new self(
            status: isset($data['Status'])
                ? (ResponseStatus::tryFrom($data['Status']) ?? $data['Status'])
                : null,
            notifications: isset($data['Notifications'])
                ? array_map(
                    fn ($notification): Notification => Notification::fromArray($notification),
                    $data['Notifications'],
                )
                : null,
            countries: isset($data['Countries'])
                ? array_map(
                    fn ($country): Country => Country::fromArray($country),
                    $data['Countries'],
                )
                : null,
        );
    }
}
