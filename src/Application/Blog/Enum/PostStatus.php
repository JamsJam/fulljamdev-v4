<?php
namespace App\Application\Blog\Enum;

enum PostStatus: int
{
    case Draft = 0;
    case Review = 1;
    case Published = 2;
    case Archived = 3;

    /**
     * Retourne le label lisible pour chaque statut
     */
    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Brouillon',
            self::Review => 'En relecture',
            self::Published => 'Publié',
            self::Archived => 'Archivé',
        };
    }
}
