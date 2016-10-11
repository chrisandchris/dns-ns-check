# DNS NS Check Tool

This tool provides a simple way to check dns results of different hostnames.

## How to use

There is a demo available, see 

### Requirements

- PHP >5.6
    - PHPs `exec` must be allowed
    - `exec` must have access to `host`
- Composer
- Working internet connection :)

## Installation

```
# clone the repo
git clone git@github.com:chrisandchris/dns-ns-check.git ./somwhere
cd ./somewhere
composer install
cd ./web
php -S localhost:8000
# now browse to localhost:8000 in your favourite browser
```

### Usage

Everyone with knowledge of the DN-system absolutely knows that to do:

1. Fill in the domain names to check
2. Fill in the nameserver to check against
3. Click the "Are you ready?" message
4. See logs, result status and results for status
