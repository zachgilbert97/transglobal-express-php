<?php

namespace ZachGilbert\TransglobalExpress\Dto;

use ZachGilbert\TransglobalExpress\Concerns\Arrayable;
use ZachGilbert\TransglobalExpress\Concerns\HasSerializableProperties;
use ZachGilbert\TransglobalExpress\Traits\SerializesProperties;

final class Document implements Arrayable, HasSerializableProperties {
    use SerializesProperties;

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
     * @return array<string, mixed>
     */
    public function properties(): array
    {
        return [
            'DocumentType' => $this->documentType,
            'Format' => $this->format,
            'Content' => $this->content,
            'DownloadURL' => !is_null($this->downloadUrl)
                ? filter_var($this->downloadUrl, FILTER_SANITIZE_URL)
                : null,
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
            documentType: $data['DocumentType'] ?? null,
            format: $data['Format'] ?? null,
            content: $data['Content'] ?? null,
            downloadUrl: $data['DownloadURL'] ?? null,
        );
    }
}
