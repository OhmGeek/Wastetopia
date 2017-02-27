# Controller
## Explained:
All pages on the site will have a Model, View and Control

The controller class for a page contains its logic

These files will be executed by public/index to genereate the pages, when a request is incoming.

## Authenticated Pages:

If you want a particular page to check for authentication, use the Authenticator class in
the 'auth' subdirectory. Documentation can be found in the folder regarding this.

Furthermore, auth allows for API based login, the Token_Controller can be configured (with index.php)
to be used in an API.

## Using Twig
A quick and simple guide to using Twig:

First, create a Loader Filesystem linking to the views folder (in the particular directory you need).

```php
$loader = new Twig_Loader_Filesystem(__DIR__ . '/../../view');
```
The directory must not have a trailing slash.

Then, create the twig object:

```php
$twig = new Twig_Environment($loader);
```
Now this is created, we want to load in a template to render. Do this by putting the filename of the template (which should be found inside the folder listed above):

```php
$template = $twig->loadTemplate('template.html');
```

From here, render the template. You will need to pass an array as a parameter that contains the data to be written to the view.
e.g. 

```php
$content = $template->render($data);
```

