# Workflows

## Introduction
Introducing an application that simplifies workflow creation with sections, images, and a WYSIWYG editor. Users can easily build custom workflows and make content visually appealing with the powerful editor. Streamline your workflow creation process with this user-friendly application.

## Usage
In local environment, using Laragon with "auto virtual hosts", visit [http://workflows.test](http://workflows.test).

## Testing
SQLite is used for testing.

## Technologies
- Laravel 10
- PHP 8.0
- Mysql 5.7
<!-- - jQuery
- Bootstrap -->
- Composer:
    - [Fortify](https://laravel.com/docs/10.x/fortify)
    - Dev:
        - [Debugbar](https://github.com/barryvdh/laravel-debugbar)
- NPM:
    - SASS
- Other:
    - [CKEditor](https://ckeditor.com/)

## Models
- Section:
    - It has other sections, images and wysiwygs in a specified order.
    - When a section includes a **section type 0** (a workflow), it has to be a link, don't include the section itself.
    - types:
        - 0: Workflow itself.
        - 1: Normal section.
        - 2: Section that has to be rendered at a smaller size.
- Wysiwyg:
    - Headers.
    - Paragraphs.
    - Lists.
    - Links.
    - Tables.
    - Code blocks.

## Attributions for Third-Party Resources
- [Wind icons created by Freepik - Flaticon](https://www.flaticon.com/free-icons/wind)