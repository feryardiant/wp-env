# Changelog

All notable changes to this project will be documented in this file. See [commit-and-tag-version](https://github.com/absolute-version/commit-and-tag-version) for commit guidelines.

## [0.0.6](https://github.com/feryardiant/wp-env/compare/v0.0.5...v0.0.6) (2026-05-17)

## [0.0.5](https://github.com/feryardiant/wp-env/compare/v0.0.4...v0.0.5) (2026-05-17)


### Bug Fixes

* **ci:** verify that release check is working ([a851dab](https://github.com/feryardiant/wp-env/commit/a851dab3ad1287e68325c3d592f73d85ac5905bc))

## [0.0.4](https://github.com/feryardiant/wp-env/compare/v0.0.3...v0.0.4) (2026-05-17)


### Bug Fixes

* **ci:** make sure `main` workflow always creates an artifact ([70c6751](https://github.com/feryardiant/wp-env/commit/70c6751cd965255d27fa1d78a938787babc30a80))

## [0.0.3](https://github.com/feryardiant/wp-env/compare/v0.0.2...v0.0.3) (2026-05-17)


### Bug Fixes

* **ci:** don't throw an error when there's no release available ([9f7486b](https://github.com/feryardiant/wp-env/commit/9f7486b309810cd00ff573418ed84c6956b6715f))

## [0.0.2](https://github.com/feryardiant/wp-env/compare/v0.0.1...v0.0.2) (2026-05-17)


### Features

* add ability to install more `plugins` and `themes` without activating them ([#14](https://github.com/feryardiant/wp-env/issues/14)) ([4e2df49](https://github.com/feryardiant/wp-env/commit/4e2df493132c5226c58a62c87d2d2f1c7cf3a275))
* **ci:** automate distribution through github releases ([#21](https://github.com/feryardiant/wp-env/issues/21)) ([82ef0af](https://github.com/feryardiant/wp-env/commit/82ef0af59bf75a81d254545c32f09ea6d4125ee9))
* **custom-theme:** rearrange `custom-theme` assets ([#13](https://github.com/feryardiant/wp-env/issues/13)) ([6b08e07](https://github.com/feryardiant/wp-env/commit/6b08e07233fed0db90a05edee9b85132e5a7188f))
* ensure `woocommerce` options are applicable for multisite setup ([#16](https://github.com/feryardiant/wp-env/issues/16)) ([75a5efd](https://github.com/feryardiant/wp-env/commit/75a5efd86901310bee1540324a8fb7ce4a38fe86))
* improve what's missing in [#9](https://github.com/feryardiant/wp-env/issues/9) ([#11](https://github.com/feryardiant/wp-env/issues/11)) ([d851b9c](https://github.com/feryardiant/wp-env/commit/d851b9c1d0be8a52f16a1e2521cffe5e65294535))
* rearrange default media assets ([#12](https://github.com/feryardiant/wp-env/issues/12)) ([4d8325e](https://github.com/feryardiant/wp-env/commit/4d8325ee2715ce60e886354a38cbd819b4f48181))


### Bug Fixes

* don't install dev-dependencies during `make-dist.sh` ([5035b05](https://github.com/feryardiant/wp-env/commit/5035b058a632cf5077737c98924b9e1648ffda41))
* make sure local `wp-cli` could work properly ([#19](https://github.com/feryardiant/wp-env/issues/19)) ([33fa3a4](https://github.com/feryardiant/wp-env/commit/33fa3a40e688f5f0ccaa49e89781f9675ee7a6f7))
* make sure to run `make-pot` during `make-dist` ([#20](https://github.com/feryardiant/wp-env/issues/20)) ([55c8980](https://github.com/feryardiant/wp-env/commit/55c8980a04b86b3bf3fd21617ca5c20e12034ec9))

## 0.0.1 (2026-05-04)


### Features

* **dev:** add config to remove plugins on initiation ([#7](https://github.com/feryardiant/wp-env/issues/7)) ([8dde8c2](https://github.com/feryardiant/wp-env/commit/8dde8c2f71a84f9ea702a72c3d290874f0dbccf5))
* **dev:** skip `pot` generation on un-distributable packages ([ffbbcdc](https://github.com/feryardiant/wp-env/commit/ffbbcdcfa6c59e304172d7dff8531b37fa970b2c))
* init `commit-and-tag-version` to manage releases ([#8](https://github.com/feryardiant/wp-env/issues/8)) ([87c4aa4](https://github.com/feryardiant/wp-env/commit/87c4aa4d78d6c27414fac5ff7a34ce0cf0db83b2))
* initialize `mu-plugins` ([#3](https://github.com/feryardiant/wp-env/issues/3)) ([f6729dd](https://github.com/feryardiant/wp-env/commit/f6729dda5172f6c42056655ffd676e3b0196dde6))


### Bug Fixes

* improve multisite setup ([#4](https://github.com/feryardiant/wp-env/issues/4)) ([f1f4b5f](https://github.com/feryardiant/wp-env/commit/f1f4b5fcf1006c4ce783c676a99255c1d8e34a67))
