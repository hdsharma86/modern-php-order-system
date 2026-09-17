# Modern PHP Order System

A small, framework-independent order management project built with modern PHP.

The project demonstrates type-safe domain modelling, value objects, enums, custom exceptions, repository abstraction, business-rule enforcement, and unit testing with PHPUnit.

## Project Purpose

This project was created to practise and demonstrate modern PHP concepts through a realistic order-processing domain.

It focuses on writing clean business logic without depending on a framework such as Laravel or Symfony.

## Requirements

- PHP 8.3 or later
- Composer
- PHPUnit 12

## Concepts Demonstrated

- Strict typing
- Scalar, object, and return type declarations
- Constructor property promotion
- Named arguments
- Readonly classes and properties
- Value objects
- PHP enums
- Match expressions
- Custom domain exceptions
- Repository pattern
- Business-rule validation
- PHPUnit unit testing
- Arrange, Act, Assert testing pattern
- Test isolation

## Project Structure

```text
modern-php-order-system/
├── src/
│   ├── Enum/
│   │   └── OrderStatus.php
│   ├── Exception/
│   │   └── InvalidOrderStateException.php
│   ├── Model/
│   │   ├── Money.php
│   │   ├── Order.php
│   │   ├── OrderItem.php
│   │   └── User.php
│   └── Repository/
├── Tests/
│   └── Unit/
│       └── OrderTest.php
├── docs/
│   └── Modern_PHP_Order_System_Learning_Guide.docx
├── composer.json
├── composer.lock
├── phpunit.xml
├── .gitignore
└── README.md