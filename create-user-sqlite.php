<?php

use Cart\Model\User;
use Cart\Model\ValueObjects\Email;
use Cart\Persistance\UserRepositoryMysql;

require_once __DIR__ . "/vendor/autoload.php";

$pdo = new PDO('sqlite:database/db.sqlite');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->exec("CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY, name TEXT, email TEXT)");

$user = new User('Thomas Moraes', new Email('thomas@gmail.com'));

$pdo->beginTransaction();
$userRepository = new UserRepositoryMysql($pdo);
$userRepository->save($user);

$user = $userRepository->findByEmail(new Email('thomas@gmail.com'));

$pdo->commit();

var_dump($user);