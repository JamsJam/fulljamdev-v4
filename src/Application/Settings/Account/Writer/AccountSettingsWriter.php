<?php

namespace App\Application\Settings\Account\Writer;

use App\Application\Settings\Account\Dto\AccountSettingsDto;
use App\Application\Settings\Storage\YamlSettingsStorage;

final readonly class AccountSettingsWriter
{
    public function __construct(private YamlSettingsStorage $storage)
    {
    }

    public function write(AccountSettingsDto $dto): void
    {
        $configuration = $this->storage->read();
        $configuration['account'] = [
            'first_name' => $dto->firstName,
            'last_name' => $dto->lastName,
            'email' => $dto->email,
            'phone_number' => $dto->phoneNumber,
            'company' => $dto->company,
            'job_title' => $dto->jobTitle,
        ];

        $this->storage->write($configuration);
    }
}
