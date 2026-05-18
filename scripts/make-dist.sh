#!/usr/bin/env bash

set -euo pipefail
shopt -s nullglob

. "$(dirname "$0")/_util.sh"

ASSET_DIR=${ASSET_DIR:-"$PWD/assets"}

for pkg_dir in packages/*/; do
    pkg_dir="${pkg_dir%/}"
    pkg="${pkg_dir##*/}"

    if [ ! -f "$pkg_dir/.distignore" ]; then
        echo -e "\e[1;33mNotice:\e[0m No .distignore found for $pkg, skipping"
        continue
    fi

    pkg_version=$(cat "$pkg_dir/package.json" | jq -r '.version')
    pkg_type=$(cat "$pkg_dir/composer.json" | jq -r '.type' | sed 's/wordpress-//')

    composer -d "$pkg_dir" install -q --no-dev

    rm -f "$ASSET_DIR/dist/$pkg"*.zip

    cp LICENSE-GPL "$pkg_dir/license.txt"

    _wp i18n make-pot "$pkg_dir" "$pkg_dir/languages/$pkg.pot"

    _wp dist-archive "$pkg_dir" "$ASSET_DIR/dist" --force --create-target-dir --filename-format="{name}"

    mv "$ASSET_DIR/dist/$pkg.zip" "$ASSET_DIR/dist/$pkg.$pkg_version.zip"

    rm "$pkg_dir"/{license.txt,composer.lock}
done
