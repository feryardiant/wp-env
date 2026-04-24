#!/usr/bin/env bash

set -euo pipefail
shopt -s nullglob

. "$(dirname "$0")/_util.sh"

for pkg_dir in packages/*/; do
    pkg_dir="${pkg_dir%/}"
    pkg="${pkg_dir##*/}"

    _wp i18n make-pot "$pkg_dir" "$pkg_dir/languages/$pkg.pot"
done
