<?php
declare(strict_types=1);

namespace App\Model;

use App\Contract\ArrayConvertible;
use InvalidArgumentException;

final readonly class User implements ArrayConvertible
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public ?string $phone = null
    ){
        if($id < 1){
            throw new InvalidArgumentException('User ID must be a positive integer.');
        }

        if(trim($name) === ''){
            throw new InvalidArgumentException('User name cannot be empty.');
        }

        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            throw new InvalidArgumentException('Invalid email address.');
        }
    }

    public function displayName(): string
    {
        return $this->name;
    }

    public function hasPhone(): bool
    {
        return $this->phone !== null;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone
        ];
    }
}