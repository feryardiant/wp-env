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

    composer -d "$pkg_dir" install

    rm -f "$ASSET_DIR/dist/$pkg.*.zip"

    cp LICENSE-GPL "$pkg_dir/license.txt"

    _wp dist-archive "$pkg_dir" "$ASSET_DIR/dist" --force --create-target-dir

    rm "$pkg_dir/license.txt"
done
