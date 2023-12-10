<?php

use Jobs\FetchOrders;
use Lib\Animals\Dog;
use Lib\Person;

$person = new Person("Joey");
$person->speak();

$dog = new Dog();

$ordersJob = new FetchOrders();