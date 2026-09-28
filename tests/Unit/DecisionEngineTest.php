<?php

declare(strict_types=1);

namespace CarMoneyLab\Tests\Unit;

use CarMoneyLab\Domain\DecisionEngine;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class DecisionEngineTest extends TestCase
{
    private DecisionEngine $engine;

    protected function setUp(): void
    {
        $this->engine = new DecisionEngine(['approve_max' => 60.0, 'review_max' => 85.0], 400000);
    }

    #[DataProvider('ltvValues')]
    public function testDecidesByLtv(float $ltv, string $expected): void
    {
        self::assertSame($expected, $this->engine->decide($ltv, 96000));
    }

    /** @return array<string,array{float,string}> */
    public static function ltvValues(): array
    {
        return [
            'низкий LTV' => [28.5, DecisionEngine::APPROVE],
            'середина зелёной зоны' => [45.0, DecisionEngine::APPROVE],
            'серая зона' => [72.3, DecisionEngine::REVIEW],
            'верхняя граница серой зоны' => [85.0, DecisionEngine::REVIEW],
            'сразу за верхней границей' => [85.01, DecisionEngine::REJECT],
            'высокий LTV' => [120.0, DecisionEngine::REJECT],
        ];
    }

    // REQ-MILEAGE-01: правило пробега (400 000 км включительно — approve,
    // 400 001 и выше — понижение approve -> review; reject не понижается).

    public function testApprovesLowLtvWithMileageJustBelowThreshold(): void
    {
        self::assertSame(DecisionEngine::APPROVE, $this->engine->decide(50.0, 399999));
    }

    public function testApprovesLowLtvWithMileageExactlyAtThreshold(): void
    {
        self::assertSame(DecisionEngine::APPROVE, $this->engine->decide(50.0, 400000));
    }

    public function testDowngradesApproveToReviewWhenMileageJustAboveThreshold(): void
    {
        self::assertSame(DecisionEngine::REVIEW, $this->engine->decide(50.0, 400001));
    }

    public function testKeepsReviewWhenMileageAboveThresholdAndLtvInReviewBand(): void
    {
        self::assertSame(DecisionEngine::REVIEW, $this->engine->decide(75.0, 400001));
    }

    public function testKeepsRejectWhenMileageAboveThresholdAndLtvAboveReviewMax(): void
    {
        self::assertSame(DecisionEngine::REJECT, $this->engine->decide(95.0, 400001));
    }
}
