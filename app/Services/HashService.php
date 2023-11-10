<?php

namespace App\Services;

use App\Models\Hash;
use InvalidArgumentException;

class HashService
{
    public function store(array $data): array
    {
        $this->validateData($data);

        $externalId = $data['data']['externalId'];
        $context = $data['data']['context'];

        $hash = sha1($externalId . $context);

        Hash::create([
            'data' => $data,
            'hash' => $hash,
        ]);

        return ['hash' => $hash];
    }

    public function read($hash): array
    {
        $data = Hash::where('hash', $hash)->get();

        if ($data->isEmpty()) {
            throw new InvalidArgumentException('Not found');
        }

        if ($data->count() > 1) {
            $collisions = $data->pluck('data');
            return ['item' => $data[0]->data, 'collisions' => $collisions];
        }

        return ['item' => $data[0]->data];
    }

private function validateData(array $data): void
{
    $validator = validator($data, [
        'data' => 'required|array',
        'data.externalId' => 'required|string',
        'data.context' => 'required|string',
    ]);

    if ($validator->fails()) {
        throw new InvalidArgumentException($validator->errors()->first());
    }
}
}
