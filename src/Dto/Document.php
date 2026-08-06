<?php

namespace ZachGilbert\TransglobalExpress\Dto;

use ZachGilbert\TransglobalExpress\Concerns\Arrayable;

final class Document implements Arrayable {
    /**
     * @param string|null $documentType
     * @param string|null $format
     * @param string|null $content Base64
     * @param string|null $downloadUrl
     */
    public function __construct(
        readonly public ?string $documentType = null,
        readonly public ?string $format = null,
        readonly public ?string $content = null,
        readonly public ?string $downloadUrl = null,
    ) {}

    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_filter([
            'DocumentType' => $this->documentType,
            'Format' => $this->format,
            'Content' => $this->content,
            'DownloadURL' => !is_null($this->downloadUrl)
                ? filter_var($this->downloadUrl, FILTER_SANITIZE_URL)
                : null,
        ], static fn ($value): bool => $value !== null);
    }

    /**
     * @param array $data
     *
     * @return static
     */
    public static function fromArray(array $data): static
    {
        return new self(
            documentType: $data['DocumentType'] ?? null,
            format: $data['Format'] ?? null,
            content: $data['Content'] ?? null,
            downloadUrl: $data['DownloadURL'] ?? null,
        );
    }
}
