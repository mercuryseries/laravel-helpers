Before using Laravel I was a big fan of Ruby on Rails and the feature that I loved the most about Ruby on Rails was the ability to be able to create a helper method (in a module) and being able to call it like a global method.

```ruby
Module BooksHelper
  def format_price(book)
    if book.free?
      content_tag(:strong, 'Free!')
    else
      number_to_currency(book.price)
    end
   end
end
```

```ruby
<p><%= format_price(book) %>
```

Pretty cool isn't it?

Now as you might guess I want to have the same thing in Laravel. 

Oh I should have said I have the same thing now with Laravel :)

Here are the steps if you also want it:

PS: Of course you can create a folder with many files helpers or just one global file helper and autoload it with Composer but I rather prefer to create a class and it's much more clear when testing. So everyone may not like it!

* Create a ```Helpers``` directory in your ```app``` folder.
* Add some classes with helpers methods (PS: with the current code all the methods needs to be static, but you can easily change it if you want)

```php
<?php

namespace App\Helpers;

use App\Book;

class BooksHelper
{
    public static function formatPrice(Book $book)
    {
        if($book->isFree()) {
            return '<strong>Free!</strong>';
        } else {
            return sprintf('$%f', number_format($book->price, 2, '.', ''));
        }
    }
}
```


```php
<?php

namespace App\Helpers;

class PagesHelper
{
    public static function setActive($route)
    {
        // your code goes here
    }
}
```

* Create a ```HelpersServiceProvider``` and register it in ```config/app.php```

```
php artisan make:provider HelpersServiceProvider
```

```php
<?php

namespace App\Providers;

use ReflectionClass;
use View;
use Illuminate\Support\ServiceProvider;

class HelpersServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadHelpersFrom(app_path('Helpers'));
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    public static function loadHelpersFrom($directory)
    {
        $helpers = static::findAllHelpersIn($directory);

        foreach ($helpers as $helper) {
            static::registerMethods($helper);
        }
    }

    public static function findAllHelpersIn($directory)
    {
        return array_diff(scandir($directory), array('..', '.'));
    }

    public static function registerMethods($helper)
    {
        $helperClassName = substr($helper, 0, -4);
        $reflector = new ReflectionClass('App\\Helpers\\' . $helperClassName);
        $methods = $reflector->getMethods();

        foreach ($methods as $method) {
            $methodHelper = function(...$params) use ($method) {
                $method->class::{$method->name}(...$params);
            };

            View::share($method->name, $methodHelper);
        }
    }
}
```

In ```config/app.php```, add:

```php
App\Providers\HelpersServiceProvider::class,
```

* Have fun now as me by using your helper methods

```php
{{ $formatPrice($book) }}
{{ $setActive('home') }}
```

You can also call it with:
```php
{{ App\Helpers\BooksHelper::formatPrice($book) }}
```
But that is exactly what I want to avoid.

Cheers!