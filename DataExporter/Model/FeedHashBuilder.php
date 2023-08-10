<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\DataExporter\Model;

use Magento\DataExporter\Model\Indexer\FeedIndexMetadata;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Build hash for feed record
 */
class FeedHashBuilder
{
    private SerializerInterface $serializer;
    private ResourceConnection $resourceConnection;

    /**
     * @param SerializerInterface $serializer
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        SerializerInterface $serializer,
        ResourceConnection $resourceConnection
    ) {
        $this->serializer = $serializer;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * Hash row data
     *
     * @param array $row
     * @param FeedIndexMetadata $metadata
     * @return string
     */
    public function buildHash(array $row, FeedIndexMetadata $metadata) : string
    {
        return sha1($this->serializer->serialize($this->sanitizeRow($row, $metadata)));
    }

    /**
     * Sanitize row
     *
     * @param array $row
     * @param FeedIndexMetadata $metadata
     * @return array
     */
    private function sanitizeRow(array $row, FeedIndexMetadata $metadata): array
    {
        foreach (array_keys($row) as $key) {
            if (\in_array($key, $metadata->getExcludeFromHashFields(), true)) {
                unset($row[$key]);
            }
        }
        return $row;
    }

    /**
     * Build identifier from feed item
     *
     * @param array $feedItem
     * @param FeedIndexMetadata $metadata
     * @return string
     */
    public function buildIdentifierFromFeedItem(array $feedItem, FeedIndexMetadata $metadata): string
    {
        $identifier = [];
        foreach ($metadata->getFeedIdentifierMappingFields() as $field) {
            $this->addValue($identifier, $feedItem[$field] ? (string)$feedItem[$field] : '');
        }
        return $this->convertToString($identifier);
    }

    /**
     * Build identifier from feed table row
     *
     * @param array $row
     * @param FeedIndexMetadata $metadata
     * @return string
     */
    public function buildIdentifierFromFeedTableRow(array $row, FeedIndexMetadata $metadata): string
    {
        $identifier = [];
        foreach (array_keys($metadata->getFeedIdentifierMappingFields()) as $columnName) {
            $this->addValue($identifier, $row[$columnName] ? (string)$row[$columnName] : '');
        }
        return $this->convertToString($identifier);
    }

    /**
     * Add value
     *
     * @param array $identifier
     * @param string $value
     * @return void
     */
    private function addValue(array &$identifier, string $value): void
    {
        $identifier[] = $this->resourceConnection->getConnection()->quote($value);
    }

    /**
     * Convert to string
     *
     * @param array $identifier
     * @return string
     */
    private function convertToString(array $identifier): string
    {
        return implode(',', $identifier);
    }
}