# Composer Curl Plugin

## Install

Fix composer config

```console
$ composer config -g repositories.ngyuki/composer-curl-plugin vcs https://github.com/ngyuki/composer-curl-plugin.git
```

Install plugin 

```
$ composer global require ngyuki/composer-curl-plugin:dev-master  
```

## Example

Install symfony

```console
$ composer require symfony/symfony:\*
```

Update without plugin

```console
$ rm -fr ~/.composer/cache/
$ composer update --dry-run --profile --no-plugins
 :
Memory usage: 92.4MB (peak: 111.41MB), time: 139.99s
```

Update with plugin

```console
$ rm -fr ~/.composer/cache/
$ composer update --dry-run --profile
 :
Memory usage: 92.61MB (peak: 111.62MB), time: 67.21s
```

## Development

Clone Repository

```console
$ git clone https://github.com/ngyuki/composer-curl-plugin.git
$ cd composer-curl-plugin
```

Run composer install

```console
$ composer install
```

Make composer home

```console
$ php misc/composer.php global install
```

Run tcpdump in other console

```console
$ sudo tcpdump -nn 'host 87.98.253.214 and (tcp[tcpflags] & 255 == tcp-syn)'
```

Install symfony

```console
$ php misc/composer.php require symfony/symfony:\*
```

Update without plugin

```console
$ rm -fr cache/*
$ php misc/composer.php update --dry-run --profile --no-plugins -vvv
```

Update with plugin

```console
$ rm -fr cache/*
$ php misc/composer.php update --dry-run --profile -vvv
```
