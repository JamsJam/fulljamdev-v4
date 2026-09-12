<?php

namespace App\Application\Page\Block\Library\Pricing\Main;

enum PricingPeriod: string
{
    case FIXED = 'fixed';
    case DAILY = 'daily';
    case MONTHLY = 'monthly';
    case YEARLY = 'yearly';

    public function label(): string
    {
        return match ($this) {
            self::FIXED => 'paiement unique',self::DAILY => '/ jour',self::MONTHLY => '/ mois',self::YEARLY => '/ an',
        };
    }
}
