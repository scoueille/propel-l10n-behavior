# Propel l10n Behavior

[![Build Status](https://img.shields.io/scrutinizer/build/g/gossi/propel-l10n-behavior.svg?style=flat-square)](https://travis-ci.org/gossi/propel-l10n-behavior)
[![Latest Stable Version](https://img.shields.io/packagist/v/gossi/propel-l10n-behavior.svg?style=flat-square)](https://packagist.org/packages/gossi/propel-l10n-behavior)

**propel-l10n-behavior** is an extension to Propel's own i18n behavior. Basically, it puts an API in front of the i18n behavior and lets you use Propel's default API but with localized content. You provide the localization you want to use once (globally) and you are ready to go.

## Installation

Install via composer:

```bash
composer require gossi/propel-l10n-behavior
```

Or insert the following in your composer.json file:

```json
{
  "require": {
    "gossi/propel-l10n-behavior": "~0"
  }
}
```

## Locale and Dependencies

When working with locales, you should know about the locales and dependencies you can define for propel-l10n-behavior. There are three mechanisms:

- Locale (That's the default locale, when retrieving localized content from a Propel object)
- Dependencies (This is a dependency chain, when a field is not available in the default locale)
- Fallback (If nothing is found, try the fallback locale)

### Set Default locale

Default locale is set to `en` but of course you can change this:

```php
PropelL10n::setLocale('de');

echo PropelL10n::getLocale(); // de
```

The default locale can be overwritten per object, e.g. you have a `Book` model, this is how it works:

```php
$book = new Book();
$book->setLocale('ja');
```

Now, the locale for book is Japanese, while for all others it stays German (as seen in the example above). You can reset this object-related overwrite by setting the locale to `null`:

```php
$book->setLocale(null);
```

The object default locale can be overwritten per call, e.g.

```php
PropelL10n::setLocale('it');
$book = BookQuery::create() // locale: it
	->setLocale('ja') // locale: ja
	->findOneByTitle('Herr der Ringe', null, 'de') // locale: de
;
$book->setLocale('ja');
$book->setTitle('Lord of the Rings', 'en');
```

### Set Fallback locale

Fallback locale also is defaulted to `en`, yet you can change that:

```php
PropelL10n::setFallback('de');

echo PropelL10n::getFallback(); // de
```

### Working with Dependencies

There are some API to work with dependencies:

```php
// get and set one dependency
PropelL10n::addDependency('de-CH', 'de');
echo PropelL10n::getDependency('de-CH'); // de

// get and set all dependencies
PropelL10n::setDependencies([
	'de'	=> 'en',
	'de-CH' => 'de',
	'de-AT' => 'de'
]);
print_r(PropelL10n::getDependencies()); // outputs the array from above

// check if dependencies exist
PropelL10n::hasDependency('de'); // true
PropelL10n::hasDepdedency('ja'); // false

// count dependencies for a given locale
PropelL10n::setDependencies(['de' => 'en', 'de-CH' => 'de']);
PropelL10n::countDependencies('de-CH'); // 2
```

### Retrieving a localized Field

Whenever you retrieve a localized field, the behavior will use the following algorithm to find the contents for the field in the most recent locale:

1. Set default locale as locale
2. Try to get the field in the set locale
3. If empty, check if the locale has a dependency and if yes a. if the primary language of the dependency and the current locale are different, work down the language-tag-chain of the current locale b. set dependency as new locale, continue with step 2
4. If no dependency is set for that locale, work down the language-tag-chain
5. If primary language is empty, use fallback as new locale and continue with step 2
6. Last step: giving up, return null

Language-tag-chain:

Given the following language-tag: `de-DE-1996` it consists of three subtag. When working down the language-tag-chain it means, the last subtag is dropped and it will be tried to getting the content of a field for the remaining language-tag until there is only the primary language left.

## Usage

### In your schema.xml

The usage in your schema.xml is very similar to the [i18n](http://Propelorm.org/documentation/behaviors/i18n.html) behavior.

```xml
<table name="book">
    <column name="id" type="INTEGER" primaryKey="true" required="true"
            autoIncrement="true"/>
    <column name="title" type="VARCHAR" size="255"/>
    <column name="author" type="VARCHAR" size="255"/>

    <behavior name="l10n">
        <parameter name="i18n_columns" value="title"/>
    </behavior>
</table>
```

The parameters are equal to the [i18n parameters](http://Propelorm.org/documentation/behaviors/i18n.html#parameters), except `default_locale` doesn't exist.

### Using the API

There are three things you need to do once for your app and you are ready to go and use Propel as if there weren't any l10n/i18n behaviors used at all.

1. Set the default locale
2. Set a fallback locale
3. Set your dependencies

Example:

```php
PropelL10n::setLocale('de'); // this is mostly the language a user decided to use
PropelL10n::setFallback('en'); // that's the default language of your app
PropelL10n::setDependencies([ // that's the locales you have available
	'de'	=> 'en'
	'de-CH' => 'de',
	'de-AT' => 'de',
	'ja' => 'en'
]);
```

And you are ready to go.

#### Retrieving from Objects

```php
$book = new Book();
$book->setTitle('Lord of the Rings');
$book->setLocale('de');
$book->setTitle('Herr der Ringe');
$book->setLocale(null);

echo $book->getTitle(); // Lord of the Rings - using the default locale (`en`)
echo $book->getTitle('de-DE'); // Herr der Ringe
$book->setLocale('de-DE');
echo $book->getTitle(); // Herr der Ringe
echo $book->getTitle('ja-JP'); // Lord of the Rings - using fallback locale (`en`)
```

#### Querying the Database

You can use your well known methods to query the database:

```php
// default locale: de

// contains all books with a german title starting with 'Harry Potter'
$books = BookQuery::create()
	->filterByTitle('Harry Potter%')
	->find();
$books = ...; 

// the book lord of the rings as searched with an english title name
$book = BookQuery::create()
	->findOneByTitle('Lord of the Rings', null, 'en');
$book = ...; 

// all harry potter books searched with the japanese title
$books = BookQuery::create()
	->setLocale('ja') 				// overwrites query default locale
	->findByTitle('Harī Pottā%');
$books = ...;

// find lord of the rings with the japanese title, overwrite locale with the filter method
$book = BookQuery::create()
	->findOneByTitle('Yubiwa Monogatari', null, 'ja');
$book = ...;
```

## Best Practices

**Use the shortest locale possible!** Only use a longer language-tag, when it is necessary to be more specific. If there is no need, just go with `de` instead of `de-DE` (which is kind of redundant anyway). However, go with `de` and `de-CH` as there might be content available which is different for people in germany or switzerland (e.g. a contact address, one for germany the other for switzerland).

## Performance

I'm pretty sure this is a performance nightmare. Only Propel API methods are used, means no manual queries so far. Performance optimization can begin after Propel will merge the `data-mapper` branch into `master`. Suggestions are welcome, please post the to the issue tracker.

## References

There's a lot material about localization, language-tags. Sometimes it is about finding the right subtag, which can be complicated enough. Here are some good references:

- [Language Tags](http://www.w3.org/International/articles/language-tags/)
- [Choosing a Language Tag](http://www.w3.org/International/questions/qa-choosing-language-tags)
- [IANA Language Subtag Registry](https://www.iana.org/assignments/language-subtag-registry/language-subtag-registry)
- [Language subtag lookup tool](https://r12a.github.io/apps/subtags/)

Related Specifications:

- [RCP 5646 - Tags for Identifying Languages](https://tools.ietf.org/html/rfc5646)
- [BCP 47 - Tags for Identifying Languages](https://www.rfc-editor.org/bcp/bcp47.txt)
