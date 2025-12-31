<?php

declare(strict_types=1);

namespace Gomu\Auth\Passport;

final class UserProfile
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $loginableId = null,
        public readonly ?string $loginableType = null,
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'loginable_id' => $this->loginableId,
            'loginable_type' => $this->loginableType,
        ];
    }
}