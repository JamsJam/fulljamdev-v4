<?php

namespace App\DataFixtures;

use App\Entity\Contact;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ContactFixtures extends Fixture
{
    public const COUNT = 30;
    private const CONTACTS_WITH_APPOINTMENTS = 18;

    private const NAMES = [
        ['Emma', 'Bernard'], ['Lucas', 'Robert'], ['Jade', 'Richard'], ['Hugo', 'Durand'],
        ['Louise', 'Moreau'], ['Arthur', 'Simon'], ['Alice', 'Laurent'], ['Jules', 'Lefebvre'],
        ['Chloé', 'Michel'], ['Louis', 'Garcia'], ['Lina', 'David'], ['Gabriel', 'Bertrand'],
        ['Rose', 'Roux'], ['Raphaël', 'Vincent'], ['Anna', 'Fournier'], ['Léo', 'Morel'],
        ['Mia', 'Girard'], ['Paul', 'André'], ['Julia', 'Mercier'], ['Nathan', 'Dupont'],
        ['Inès', 'Lambert'], ['Adam', 'Bonnet'], ['Léa', 'François'], ['Noah', 'Martinez'],
        ['Agathe', 'Legrand'], ['Tom', 'Garnier'], ['Zoé', 'Faure'], ['Ethan', 'Rousseau'],
        ['Camille', 'Blanc'], ['Sacha', 'Henry'],
    ];

    private const COMPANIES = [
        'Atelier Nova', 'Kanso Studio', 'Nexora', 'Alba Conseil', 'Hexa Labs',
        'Maison Lune', 'Pixel Forge', 'Orbe', 'Studio Horizon', 'Boreal Digital',
    ];

    private const JOB_TITLES = [
        'Directeur produit', 'Designer UX', 'Responsable marketing', 'Consultant',
        'Chef de projet', 'CTO', 'Fondateur', 'Développeur', 'Responsable commercial', 'Product Owner',
    ];

    public function load(ObjectManager $manager): void
    {
        $now = new \DateTimeImmutable();

        foreach (self::NAMES as $index => [$firstName, $lastName]) {
            $hasCompany = 0 !== $index % 4;
            $contact = (new Contact())
                ->setFirstName($firstName)
                ->setLastName($lastName)
                ->setEmail(sprintf('contact.%02d@example.test', $index + 1))
                ->setPhoneNumber(sprintf('+33 6 20 30 %02d %02d', intdiv($index, 10), $index % 10))
                ->setCompany($hasCompany ? self::COMPANIES[$index % count(self::COMPANIES)] : null)
                ->setJobTitle($hasCompany ? self::JOB_TITLES[$index % count(self::JOB_TITLES)] : null)
                ->setSource(['Réservation', 'Formulaire de contact', 'Recommandation'][$index % 3])
                ->setNotes(0 === $index % 5 ? 'Contact à recontacter dans les prochaines semaines.' : null)
                ->setCreatedAt($now)
                ->setUpdatedAt($now);

            $manager->persist($contact);
            $this->addReference(self::reference($index), $contact);
        }

        $manager->flush();
    }

    public static function reference(int $index): string
    {
        return sprintf('contact.%d', $index);
    }

    public static function appointmentReference(int $index): string
    {
        return self::reference($index % self::CONTACTS_WITH_APPOINTMENTS);
    }
}
