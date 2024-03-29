<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture(
    'Magento_CatalogDataExporter::Test/_files/second_website_with_store_and_store_view_rollback.php'
);
Resolver::getInstance()->requireDataFixture(
    'Magento/GroupedProduct/_files/product_grouped_in_multiple_websites_rollback.php'
);
