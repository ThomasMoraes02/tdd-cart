<?php 
namespace Cart\Model;

interface Encoder
{
    public function encode(string $password): string;

    public function decode(string $password, string $hash): bool;
}