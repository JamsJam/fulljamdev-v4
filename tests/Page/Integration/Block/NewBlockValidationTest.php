<?php

namespace App\Tests\Page\Integration\Block;

use App\Application\Page\Block\Library\Pricing\Main\PricingCardDTO;
use App\Application\Page\Block\Library\Pricing\Main\PricingDTO;
use App\Application\Page\Block\Library\Pricing\Main\PricingPeriod;
use App\Application\Page\Block\Library\Testimonials\Main\TestimonialDTO;
use App\Twig\Components\Page\Block\Pricing;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Twig\Environment;

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

    public function testPricingCardRendersItsOptionalCtaAndCompactFeatures(): void
    {
        self::bootKernel();
        $card = new PricingCardDTO();
        $card->title = 'Audit SEO';
        $card->description = 'Audit rapide et corrections prioritaires.';
        $card->price = 40000;
        $card->features = ['Audit technique', 'Structure', 'Backlinks', 'Correctifs'];
        $card->cta->label = 'Auditer mon site';
        $card->cta->href = '/contact';
        $pricing = new PricingDTO();
        $pricing->title->content = 'Tarifs';
        $pricing->text->content = 'Des offres adaptées.';
        $pricing->cards = [$card];

        $html = self::getContainer()->get(Environment::class)->render('components/page/block/pricing/Pricing.html.twig', [
            'data' => $pricing,
            'blockId' => 1,
            'anchorId' => null,
            'this' => new Pricing(),
        ]);

        self::assertStringContainsString('class="button button--primary pricing__cta"', $html);
        self::assertStringContainsString('href="/contact"', $html);
        self::assertStringContainsString('pricing__features--compact', $html);
        self::assertStringContainsString('Voir tout ce qui est inclus', $html);
    }
}
