<?php

declare(strict_types=1);

namespace PixelPerfect\DiscountLinesHyvaCheckout\Test\Unit;

use PHPUnit\Framework\TestCase;

class LayoutTest extends TestCase
{
    private const MODULE_DIR = __DIR__ . '/../../src';

    public function testLayoutOverridesTheCoreDiscountTemplate(): void
    {
        $xml = simplexml_load_file(self::MODULE_DIR . '/view/frontend/layout/hyva_checkout_components.xml');
        $blocks = $xml->xpath('//referenceBlock[@name="price-summary.total-segment.discount"]');

        self::assertCount(1, $blocks);
        self::assertSame(
            'PixelPerfect_DiscountLinesHyvaCheckout::price-summary/total-segments/discount.phtml',
            (string)$blocks[0]['template']
        );
        self::assertFileExists(self::MODULE_DIR . '/view/frontend/templates/price-summary/total-segments/discount.phtml');
    }

    public function testModuleLoadsAfterBaseModuleAndHyvaCheckout(): void
    {
        $xml = simplexml_load_file(self::MODULE_DIR . '/etc/module.xml');
        $modules = array_map('strval', $xml->xpath('//sequence/module/@name'));

        self::assertContains('PixelPerfect_DiscountLines', $modules);
        self::assertContains('Hyva_Checkout', $modules);
    }

    public function testStylesheetAndScriptBlockAreRegisteredOnTheCheckoutPage(): void
    {
        $xml = simplexml_load_file(self::MODULE_DIR . '/view/frontend/layout/hyva_checkout_index_index.xml');

        self::assertCount(1, $xml->xpath('//css[@src="PixelPerfect_DiscountLines::css/discount-lines.css"]'));
        self::assertFileExists(self::MODULE_DIR . '/view/frontend/templates/js/remove-coupon.phtml');
    }

    public function testGermanTranslationCoversEveryString(): void
    {
        $english = array_column(array_map(static fn (string $l): array => str_getcsv($l, ',', '"', ''), file(self::MODULE_DIR . '/i18n/en_US.csv')), 0);
        $german = array_column(array_map(static fn (string $l): array => str_getcsv($l, ',', '"', ''), file(self::MODULE_DIR . '/i18n/de_DE.csv')), 0);

        self::assertSame($english, $german);
    }
}
