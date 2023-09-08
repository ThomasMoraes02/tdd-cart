<?php

use Cart\Infra\EncoderArgon2ID;
use Cart\Model\ValueObjects\Email;
use Cart\Infra\Factories\UserFactory;
use Cart\Infra\Persistance\UserRepositoryMysql;

require_once __DIR__ . "/vendor/autoload.php";

$pdo = new PDO('sqlite:database/db.sqlite');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->exec("CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY, name TEXT, email TEXT, password LONGTEXT)");

$userFactory = new UserFactory(new EncoderArgon2ID());
$user = $userFactory->create('Thomas Moraes', 'thomas@gmail.com', '123456');

$pdo->beginTransaction();
$userRepository = new UserRepositoryMysql($pdo, $userFactory);
$userRepository->save($user);

$user = $userRepository->findByEmail(new Email('thomas@gmail.com'));

$pdo->commit();

var_dump($user);