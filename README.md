# Workflows

## Introduction
Introducing an application that simplifies workflow creation with sections, images, and WYSIWYG editors. Users can easily build custom workflows and make content visually appealing with the powerful editor. Streamline your workflow creation process with this user-friendly application.

## Usage
In local environment, using Laragon with "auto virtual hosts", visit [http://workflows.test](http://workflows.test).

## Testing
SQLite is used for testing.

## Technologies
- Laravel 10
- PHP 8.0
- Mysql 5.7
- jQuery
- jQueryUI
- Composer:
    - [Fortify](https://laravel.com/docs/10.x/fortify)
    - [spatie/flysystem-dropbox](https://github.com/spatie/flysystem-dropbox)
    - Dev:
        - [Debugbar](https://github.com/barryvdh/laravel-debugbar)
- NPM:
    - SASS
- Other:
    - [CKEditor5](https://ckeditor.com/)
    - [Prismjs](https://prismjs.com/download.html#themes=prism-okaidia&languages=markup+css+clike+javascript+c+csharp+cpp+diff+java+markup-templating+php+python+ruby+typescript)

## Models
- Workflow:
    - Extends Section.
    - It can only have sections, not images or editors.
- Section:
    - It has other sections, images and wysiwygs in a specified order.
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
- Images:
    - Retrieve an image from the storage using the name attribute.

## Dropbox
[Dropbox](https://www.dropbox.com/home) is used to store the user images.
- [API documentation.](https://www.dropbox.com/developers/documentation/http/documentation)
- [To make it works.](https://github.com/spatie/flysystem-dropbox/issues/86)
- [How to get credentials.](https://gist.github.com/phuze/755dd1f58fba6849fbf7478e77e2896a)
- To get DROPBOX_ACCESS_CODE:
    - https://www.dropbox.com/oauth2/authorize?client_id=<YOUR_APP_KEY>&response_type=code&token_access_type=offline
- To get DROPBOX_REFRESH_TOKEN:
    - curl https://api.dropbox.com/oauth2/token -d code=<ACCESS_CODE> -d grant_type=authorization_code -u <APP_KEY>:<APP_SECRET>

## Attributions for Third-Party Resources
- [Logo/favicon by Freepik - Flaticon](https://www.flaticon.com/free-icons/wind)
- [Loading Spinner by loading.io](https://loading.io/)