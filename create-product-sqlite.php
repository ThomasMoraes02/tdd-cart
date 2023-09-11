<?php

use Cart\Model\Product\Product;
use Cart\Infra\Persistance\ProductRepositoryMysql;

require_once __DIR__ . "/vendor/autoload.php";

$pdo = new PDO('sqlite:database/db.sqlite');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->exec("CREATE TABLE IF NOT EXISTS products (id INTEGER PRIMARY KEY, name TEXT, price FLOAT, quantity INTEGER)");

$product = new Product('Notebook Dell G15', 5200, 5);

$pdo->beginTransaction();

$productRepository = new ProductRepositoryMysql($pdo);
$productRepository->save($product);

$product = $productRepository->findById(1);

$pdo->commit();

var_dump($product);