<?php

namespace App\DataFixtures;

use App\Entity\Reservation\Appointment;
use App\Entity\Reservation\Summary;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SummaryFixtures extends Fixture implements DependentFixtureInterface
{
    private const CONTENTS = [
        '<h2>Compte rendu du rendez-vous</h2><p>Nous avons clarifié les objectifs du projet et validé les priorités.</p><h3>Décisions prises</h3><ul><li>Préparer une première proposition fonctionnelle.</li><li>Centraliser les contenus existants.</li><li>Planifier un point de suivi.</li></ul><p><strong>Prochaine étape :</strong> envoyer la synthèse et le calendrier prévisionnel.</p>',
        '<h2>Synthèse de l’échange</h2><p>La réunion a permis de présenter les contraintes techniques et les attentes métier.</p><blockquote>La simplicité d’utilisation reste le principal critère de réussite.</blockquote><h3>Actions</h3><ol><li>Finaliser le périmètre.</li><li>Partager les accès nécessaires.</li><li>Valider la date du prochain atelier.</li></ol>',
        '<h2>Atelier technique</h2><p>Les principaux flux ont été étudiés avec l’équipe.</p><ul><li><strong>Architecture :</strong> découpage validé.</li><li><strong>Données :</strong> reprise à confirmer.</li><li><strong>Déploiement :</strong> environnement de test à préparer.</li></ul><p>Aucun point bloquant identifié à ce stade.</p>',
    ];

    public function load(ObjectManager $manager): void
    {
        $now = new \DateTimeImmutable();
        $summaryIndex = 0;

        foreach (AppointmentFixtures::references() as [$planningIndex, $appointmentIndex]) {
            $appointment = $this->getReference(
                AppointmentFixtures::reference($planningIndex, $appointmentIndex),
                Appointment::class,
            );

            if ($appointment->getStartAt() >= $now || 0 !== $appointmentIndex % 2) {
                continue;
            }

            $summary = (new Summary())
                ->setAppointment($appointment)
                ->setContent(self::CONTENTS[$summaryIndex % count(self::CONTENTS)])
                ->setInternalNotes(0 === $summaryIndex % 3 ? 'Vérifier les éléments confidentiels avant l’envoi.' : null)
                ->setTranscription("Intervenant : Bonjour, merci pour votre disponibilité.\nInvité : Merci, nous pouvons commencer.\nIntervenant : Reprenons les objectifs et les prochaines actions.")
                ->setRecordingLink(0 === $summaryIndex % 2 ? sprintf('https://recordings.example.test/appointment/%d', $summaryIndex + 1) : null);

            $manager->persist($summary);
            ++$summaryIndex;
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [AppointmentFixtures::class];
    }
}
