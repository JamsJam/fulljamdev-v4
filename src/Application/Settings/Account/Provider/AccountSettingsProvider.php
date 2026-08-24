<?php

namespace App\Application\Settings\Account\Provider;

use App\Application\Settings\Account\Dto\AccountSettingsDto;
use App\Application\Settings\Storage\YamlSettingsStorage;

final readonly class AccountSettingsProvider
{
    public function __construct(private YamlSettingsStorage $storage)
    {
    }

    public function provide(): AccountSettingsDto
    {
        $configuration = $this->storage->read();
        $account = is_array($configuration['account'] ?? null) ? $configuration['account'] : [];
        $dto = new AccountSettingsDto();
        $dto->firstName = $this->stringValue($account, 'first_name');
        $dto->lastName = $this->stringValue($account, 'last_name');
        $dto->email = $this->stringValue($account, 'email');
        $dto->phoneNumber = $this->stringValue($account, 'phone_number');
        $dto->company = $this->stringValue($account, 'company');
        $dto->jobTitle = $this->stringValue($account, 'job_title');

        return $dto;
    }

    /** @param array<string, mixed> $values */
    private function stringValue(array $values, string $key): string
    {
        return is_string($values[$key] ?? null) ? $values[$key] : '';
    }
}
