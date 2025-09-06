<?php

namespace App\Application;

class ServiceLocator
{
    private array $namespaces = [
        "Application\\Blog\\Services\\",
        // "Application\\CV\\Services\\",
        // "Application\\Guestbook\\Services\\",
    ];

    public function resolve(string $serviceName): object
    {
        foreach ($this->namespaces as $ns) {
            $fqcn = $ns . $serviceName;
            if (class_exists($fqcn)) {
                return new $fqcn(); 
            }
        }

        throw new \RuntimeException("Service {$serviceName} introuvable.");
    }
}