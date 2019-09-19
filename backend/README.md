# API - Bulletpoint
[![Build Status](https://travis-ci.org/klapuch/bulletpoint.svg?branch=master)](https://travis-ci.org/klapuch/bulletpoint)
[![codecov](https://codecov.io/gh/klapuch/bulletpoint/branch/master/graph/badge.svg)](https://codecov.io/gh/klapuch/bulletpoint)
[![PHPStan](https://img.shields.io/badge/PHPStan-enabled-brightgreen.svg?style=flat)](https://github.com/phpstan/phpstan)

## Local Installation

Run docker environment, then exec into php image and run:
- `make init`

then exec into database image and run:
- `test_import`

go gack to php image and run tests via:
- `make tests`
