# Composer Curl Plugin

## Development

Run composer install

```
composer install
```

Make composer home

```
php misc/composer.php global install
```

Run tcpdump in other console

```
tcpdump -nn 'host 87.98.253.214 and (tcp[tcpflags] & 255 == tcp-syn)'
```


Install symfony by composer with composer-curl-plugin

```
php misc/composer.php install --dry-run -vvv
```
