# Composer Curl Plugin

## Install

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
$ rm -fr ~/.composer/cache/repo/
$ composer update --dry-run --profile --no-plugins
 :
Memory usage: 92.4MB (peak: 111.41MB), time: 139.99s
```

Update with plugin

```console
$ rm -fr ~/.composer/cache/repo/
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

Make composer home and install plugin

```console
$ misc/composer.php global require ngyuki/composer-curl-plugin:dev-master
```

Run tcpdump in other console

```console
$ sudo tcpdump -nn 'host 87.98.253.214 and (tcp[tcpflags] & 255 == tcp-syn)'
```

Install symfony

```console
$ misc/composer.php require symfony/symfony:\*
```

Update without plugin

```console
$ rm -fr cache/repo/
$ misc/composer.php update --dry-run --profile --no-plugins -vvv
```

Update with plugin

```console
$ rm -fr cache/repo/
$ misc/composer.php update --dry-run --profile -vvv
```
