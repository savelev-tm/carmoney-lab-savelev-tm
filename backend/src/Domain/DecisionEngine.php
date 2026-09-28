<?php

declare(strict_types=1);

namespace CarMoneyLab\Domain;

/**
 * Решение по заявке на основании LTV и пробега.
 *
 *   LTV <= approve_max              -> approve, если пробег <= review_mileage_threshold_km
 *   approve_max < LTV <= review_max -> review
 *   LTV > review_max                -> reject
 *
 * Правило пробега понижает только approve: review и reject не меняются.
 */
final class DecisionEngine
{
    public const APPROVE = 'approve';
    public const REVIEW = 'review';
    public const REJECT = 'reject';

    private float $approveMax;
    private float $reviewMax;
    private int $reviewMileageThreshold;

    /** @param array{approve_max:float,review_max:float} $thresholds */
    public function __construct(array $thresholds, int $reviewMileageThreshold)
    {
        $this->approveMax = $thresholds['approve_max'];
        $this->reviewMax = $thresholds['review_max'];
        $this->reviewMileageThreshold = $reviewMileageThreshold;
    }

    public function decide(float $ltv, int $mileage): string
    {
        if ($ltv < $this->approveMax) {
            if ($mileage > $this->reviewMileageThreshold) {
                return self::REVIEW;
            }
            return self::APPROVE;
        }

        if ($ltv <= $this->reviewMax) {
            return self::REVIEW;
        }

        return self::REJECT;
    }
}
