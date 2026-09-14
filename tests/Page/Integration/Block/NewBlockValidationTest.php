<?php

namespace App\Tests\Page\Integration\Block;

use App\Application\Page\Block\Library\Pricing\Main\PricingCardDTO;
use App\Application\Page\Block\Library\Pricing\Main\PricingDTO;
use App\Application\Page\Block\Library\Pricing\Main\PricingPeriod;
use App\Application\Page\Block\Library\Testimonials\Main\TestimonialDTO;
use App\Twig\Components\Page\Block\Pricing;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class NewBlockValidationTest extends KernelTestCase
{
    public function testItValidatesTestimonialRatingAndLength(): void
    {
        self::bootKernel();
        $testimonial = new TestimonialDTO();
        $testimonial->rating = 6;
        $testimonial->comment = str_repeat('a', 1201);
        self::assertGreaterThan(0, self::getContainer()->get(ValidatorInterface::class)->validate($testimonial)->count());
    }

    public function testPricingUsesCentsAndSupportsEveryPeriod(): void
    {
        self::assertCount(4, PricingPeriod::cases());
        $card = new PricingCardDTO();
        $card->price = 4999;
        self::assertSame(4999, $card->price);
        self::assertSame("49,99\u{00A0}€", (new Pricing())->formatPrice($card->price));
    }

    public function testPricingAcceptsOnlyOneFeaturedOffer(): void
    {
        self::bootKernel();
        $first = new PricingCardDTO();
        $first->featured = true;
        $second = new PricingCardDTO();
        $second->featured = true;
        $pricing = new PricingDTO();
        $pricing->cards = [$first, $second];

        self::assertGreaterThan(0, self::getContainer()->get(ValidatorInterface::class)->validate($pricing)->count());
    }
}
