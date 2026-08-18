<?php

namespace App\Application\Contact\Persister;

use App\Entity\Contact;
use Doctrine\ORM\EntityManagerInterface;

final readonly class ContactPersister
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function persist(Contact $contact): void
    {
        $this->entityManager->persist($contact);
    }
}
