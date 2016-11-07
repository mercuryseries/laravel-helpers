# Dynamic Method Helpers

Before using Laravel I was a big fan of Ruby on Rails and the feature that I loved the most about Ruby on Rails was the ability to create a helper method (in a module) and being able to call it like a global method.

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

```erb
<p><%= format_price(book) %></p>
```

Pretty cool isn't it?

Now as you might guess I want to have the same thing in Laravel. 

Oh I should have said I have the same thing now with Laravel :)

**Disclaimer**: Of course you can create a folder with many files helpers or just one global file helper and autoload it with Composer but I rather prefer to create a class and it's much more clear when testing. So everyone may not like it!

Here are the steps if you also want it:

## Install the package through Composer

```
composer require mercuryseries/laravel-helpers
```

## Register the package service provider in ```config/app.php``` by adding:

```php
MercurySeries\Helpers\HelpersServiceProvider::class,
```

## Create a ```Helpers``` directory in your ```app``` folder.

Now you can create a ```Helpers``` directory in your ```app``` folder.

## Add some classes with your helper methods

PS: All helper methods need to be static.

```php
<?php

// File: app/Helpers/BooksHelper.php

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

// File: app/Helpers/PagesHelper.php

namespace App\Helpers;

class PagesHelper
{
    public static function setActive($route)
    {
        // your code goes here
    }
}
```

## Have fun now as me by using your helper methods

```php
{{ $formatPrice($book) }}
{{ $setActive('home') }}
```

You can also call it with:
```php
{{ App\Helpers\BooksHelper::formatPrice($book) }}
```

Don't forget to change ```App``` accordingly to your application's root namespace. But that is exactly what I want to avoid (that long stuff).

## More Configuration?

The ```Helpers``` folder name and the namespace ```App\Helpers``` can be easily changed via the configuration file. Just publish it and edit it as you want:

```
php artisan vendor:publish --provider="MercurySeries\Helpers\HelpersServiceProvider"
```

Cheers!