#!/usr/bin/env bash

set -euo pipefail

. "$(dirname "$0")/_util.sh"

declare -A plugins_map

                  # Blocksy Plugin Contact        Woo
                  # Comp.   Check  Form7  JetPack Comm.
plugins_map['5.9']='2.0.86  0.2.0  5.7.7  11.2.2  7.5.2'
plugins_map['6.0']='2.0.86  0.2.3  5.7.7  12.0.1  7.7.3'
plugins_map['6.1']='2.0.86  0.2.3  5.7.7  12.5.1  7.9.2'
plugins_map['6.2']='2.0.86  0.2.3  5.8.7  12.7.1  8.2.5'
plugins_map['6.3']='2.0.86  1.9.0  5.9.8  13.2.1  8.7.3'
plugins_map['6.4']='2.0.86  1.9.0  5.9.8  13.6.1  9.0.4'
plugins_map['6.5']='2.1.38  1.9.0  5.9.8  13.9.1  9.4.5'
plugins_map['6.6']='2.1.38  1.9.0  6.0.6  14.4.1  9.8.7'
plugins_map['6.7']='2.1.38  1.9.0  6.1.5  15.1.1  10.3.8'
plugins_map['6.8']='2.1.38  1.9.0  6.1.5  15.7.1  10.6.2'
plugins_map['6.9']='2.1.38  1.9.0  6.1.5  15.7.1  10.6.2'

declare -A themes_map

                 # Blocksy
themes_map['5.9']='2.0.86'
themes_map['6.0']='2.0.86'
themes_map['6.1']='2.0.86'
themes_map['6.2']='2.0.86'
themes_map['6.3']='2.0.86'
themes_map['6.4']='2.0.86'
themes_map['6.5']='2.1.38'
themes_map['6.6']='2.1.38'
themes_map['6.7']='2.1.38'
themes_map['6.8']='2.1.38'
themes_map['6.9']='2.1.38'

if [[ -f "$PWD/.env" ]]; then
    . "$PWD/.env"
fi

WP_VERSION=${WP_VERSION:-'5.9'}
# Reduce to major.minor for map lookup
wp_version_key=$(echo "${WP_VERSION}" | awk -F. '{printf "%s.%s", $1, $2}')

wp_plugins=(${plugins_map[${wp_version_key}]:-})
wp_themes=(${themes_map[${wp_version_key}]:-})

if ((${#wp_themes[@]} == 0 )); then
    echo -e "\e[1;31mError:\e[0m Unsupported WordPress version ${WP_VERSION}."
    exit 1
fi

declare -A plugin_supports

plugin_supports['blocksy-companion']="${wp_plugins[0]:-2.0.86}"
plugin_supports['plugin-check']="${wp_plugins[1]:-0.2.0}"
plugin_supports['contact-form-7']="${wp_plugins[2]:-5.7.7}"
plugin_supports['jetpack']="${wp_plugins[3]:-11.2.2}"
plugin_supports['woocommerce']="${wp_plugins[4]:-7.5.2}"

declare -A theme_supports

theme_supports['blocksy']="${wp_themes[0]:-2.0.86}"

ASSET_DIR=${ASSET_DIR:-"$PWD/assets"}
INSTALL_DIR=${INSTALL_DIR:-"$PWD/docker/volumes/wordpress"}

if [[ ! -d "${ASSET_DIR}" ]]; then
    echo -e "\e[1;31mError:\e[0m Unable to continue installation."
    echo -e "       Asset directory '\e[33m${ASSET_DIR}\e[0m' is missing."
    exit 1
fi

SITE_URL=${SITE_URL:-'http://localhost'}

if [[ ${WP_RESET:-0} -eq 1 ]]; then
    e_start "Reset WordPress Core"
    rm -rf "$INSTALL_DIR"
    e_end
fi

if [[ ! -d "${INSTALL_DIR}" ]]; then
    e_start 'Download WordPress Core'
    _wp core download --version=${WP_VERSION}
    e_end
fi

if [[ ! -f "${INSTALL_DIR}/wp-config.php" ]]; then
    e_start 'Configure WordPress Core'
    _wp config create \
        --dbhost=${DB_HOST:-127.0.0.1:3306} --dbname=${DB_NAME:-wordpress} \
        --dbuser=${DB_USER:-sampleuser} --dbpass=${DB_PASS:-samplepass}
    e_end
fi

if _wp core is-installed --url="${SITE_URL}" --allow-root; then
  echo -e "\e[1;36mInfo:\e[0m WordPress is already installed."
else
    e_start 'Install WordPress Core'
    _wp core install \
        --url="${SITE_URL}" --title="${SITE_TITLE:-'WordPress Local'}" \
        --admin_user=${SITE_ADMIN_USER:-admin} \
        --admin_password=${SITE_ADMIN_PASS:-secret} \
        --admin_email=${SITE_ADMIN_EMAIL:-'admin@example.com'} \
        --skip-email --allow-root
    e_end

    e_start 'Set up default options'
    _wp option update permalink_structure "/%postname%/"
    _wp option update timezone_string "${SITE_TIMEZONE:-Asia/Jakarta}"
    e_end

    if [[ ! -f "$INSTALL_DIR/favicon.ico" ]]; then
        cp "$ASSET_DIR/favicon.ico" "$INSTALL_DIR/favicon.ico"
    fi
fi

installed_plugins=()

if [[ -n "${SITE_PLUGINS:-}" ]]; then
    e_start 'Set up default Plugins'
    SITE_PLUGINS=${SITE_PLUGINS:-''}
    plugins=()

    for plugin in ${SITE_PLUGINS//,/ }; do
        if _wp plugin is-installed "$plugin"; then
            echo -e "\e[1;36mNotice:\e[0m '$plugin' is already installed."
            continue
        fi

        plugin_version="${plugin_supports[$plugin]:-}"
        if [[ "$plugin_version" == "none" ]]; then
            echo -e "\e[1;36mNotice:\e[0m Skipping '$plugin' - incompatible with WordPress ${WP_VERSION}"
            continue
        fi

        if [[ -n "$plugin_version" ]]; then
            echo -e "\e[1;36mInfo:\e[0m Installing '$plugin' (v$plugin_version)"
            _wp plugin install "$plugin" --version="$plugin_version" --quiet
            installed_plugins+=("$plugin")

            continue
        fi

        plugins+=("$plugin")
    done

    if ((${#plugins[@]} != 0 )); then
        echo -e "\e[1;36mInfo:\e[0m Installing ${plugins[@]} (latest)"
        _wp plugin install ${plugins[@]} --quiet

        installed_plugins+=("${plugins[@]}")
    fi

    if ((${#installed_plugins[@]} != 0 )); then
        _wp plugin activate ${installed_plugins[@]}
    fi
    e_end
fi

if _wp plugin is-active woocommerce; then
    e_start "Set up WooCommerce"
    _wp option update woocommerce_store_address "${WC_STORE_ADDRESS:-'Jl. Example No. 123'}"
    _wp option update woocommerce_store_city "${WC_STORE_CITY:-'Batang'}"
    _wp option update woocommerce_default_country "${WC_DEFAULT_COUNTRY:-'ID:JT'}"
    _wp option update woocommerce_currency "${WC_CURRENCY:-'IDR'}"
    _wp option update woocommerce_store_postcode "${WC_STORE_POSTCODE:-'12345'}"

    _wp option update woocommerce_weight_unit "${WC_WEIGHT_UNIT:-kg}"
    _wp option update woocommerce_dimension_unit "${WC_DIMENSION_UNIT:-cm}"
    _wp option update woocommerce_price_thousand_sep "${WC_PRICE_THOUSAND_SEP:-.}"
    _wp option update woocommerce_price_decimal_sep "${WC_PRICE_DECIMAL_SEP:-,}"
    _wp option update woocommerce_price_num_decimals "${WC_PRICE_DECIMAL_NUM:-0}"

    # Skip the onboarding profile
    _wp option update woocommerce_onboarding_profile '{"skipped":true}' --format=json

    # Mark the task list as complete
    _wp option update woocommerce_task_list_complete yes
    e_end
fi

if [[ -n "${SITE_THEMES:-}" ]]; then
    e_start 'Set up default themes'
    themes=()

    for theme in ${SITE_THEMES//,/ }; do
        if _wp theme is-installed "$theme"; then
            echo " - $theme is already installed."
            continue
        fi

        theme_version="${theme_supports[$theme]:-}"
        if [[ "$theme_version" == "none" ]]; then
            echo -e "\e[1;36mNotice:\e[0m Skipping '$plugin' - incompatible with WordPress ${WP_VERSION}"
            continue
        fi

        if [[ -n "$theme_version" ]]; then
            _wp theme install "$theme" --version=$theme_version

            continue
        fi

        themes+=("$theme")
    done

    if ((${#themes[@]} != 0 )); then
        _wp theme install ${themes[@]}
    fi

    SITE_DEFAULT_THEME=${SITE_DEFAULT_THEME:-}

    if [[ -n "$SITE_DEFAULT_THEME" ]] && _wp theme is-installed "$SITE_DEFAULT_THEME"; then
        _wp theme activate $SITE_DEFAULT_THEME
    fi
    e_end
fi

if [[ ${MULTISITE_ENABLED:-0} -eq 1 ]]; then
    e_start "Set up MultiSite"

    if _wp core is-installed --network; then
        echo -e "\e[1;36mNotice:\e[0m Multisite is already installed."
    else
        _wp core multisite-convert

        # https://developer.wordpress.org/advanced-administration/server/web-server/httpd/#multisite
        cat "$ASSET_DIR/.htaccess.multisite" > "$INSTALL_DIR/.htaccess"
        echo 'Update .htaccess.'
    fi

    if ((${#installed_plugins[@]} != 0 )); then
        _wp plugin activate ${installed_plugins[@]} --network
    fi

    if [[ -n "$SITE_DEFAULT_THEME" ]] && _wp theme is-installed "$SITE_DEFAULT_THEME"; then
        _wp theme enable $SITE_DEFAULT_THEME --network
    fi

    e_end
fi

e_start 'Cleanup'
if _wp plugin is-installed hello; then
    _wp plugin uninstall hello
fi
e_end

e_start 'Verify Installation'
_wp core version --extra
echo "Site URL: ${SITE_URL}"
e_end
