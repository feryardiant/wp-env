e_start() {
    if [[ -n "${CI:-}" ]]; then
        echo -e "::group::$*\e[0m"
    else
        echo -e "> \e[1;33m$*\e[0m"
    fi
}

e_end() {
    if [[ -n "${CI:-}" ]]; then
        echo '::endgroup::'
    else
        echo ''
    fi
}

_wp() {
    if command -v wp > /dev/null 2>&1; then
        wp "$@" --color
    else
        vendor/bin/wp "$@" --color
    fi
}
