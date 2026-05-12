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
plugins_map['6.5']='2.1.41  1.9.0  5.9.8  13.9.1  9.4.5'
plugins_map['6.6']='2.1.41  1.9.0  6.0.6  14.4.1  9.8.7'
plugins_map['6.7']='2.1.41  1.9.0  6.1.5  15.1.1  10.3.8'
plugins_map['6.8']='2.1.41  1.9.0  6.1.5  15.7.1  10.6.2'
plugins_map['6.9']='2.1.41  1.9.0  6.1.5  15.7.1  10.6.2'

declare -A themes_map

                 # Blocksy
themes_map['5.9']='2.0.86'
themes_map['6.0']='2.0.86'
themes_map['6.1']='2.0.86'
themes_map['6.2']='2.0.86'
themes_map['6.3']='2.0.86'
themes_map['6.4']='2.0.86'
themes_map['6.5']='2.1.41'
themes_map['6.6']='2.1.41'
themes_map['6.7']='2.1.41'
themes_map['6.8']='2.1.41'
themes_map['6.9']='2.1.41'

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

SETUP_DIR=${SETUP_DIR:-"$PWD"}
ASSET_DIR=${ASSET_DIR:-"$SETUP_DIR/assets"}
SCRIPTS_DIR=${SCRIPTS_DIR:-"$SETUP_DIR/scripts"}
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

    e_start 'Set up media'
    if [[ ! -f "$INSTALL_DIR/favicon.ico" ]]; then
        cp "$ASSET_DIR/favicon.ico" "$INSTALL_DIR/favicon.ico"
    fi

                     # Post ID   4              5         6        7
    _wp media import $ASSET_DIR/{WordPress-Logo,Acme-Logo,No-Image,Image-Placeholder}.png
    e_end

    e_start 'Set up options'
    _wp option update permalink_structure "/%postname%/"
    _wp option update timezone_string "${SITE_TIMEZONE:-Asia/Jakarta}"
    _wp option update site_icon "4" # The 'WordPress-Logo.png'
    e_end
fi

plugins_to_activate=()

if [[ -n "${SITE_PLUGINS:-}" ]]; then
    e_start 'Set up plugins'
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
            result=$(_wp plugin install "$plugin" --version="$plugin_version" | head -n 1)
            echo -e "\e[1;36mInfo:\e[0m $result"

            plugins_to_activate+=("$plugin")

            continue
        fi

        plugins+=("$plugin")
    done

    unset plugin result

    if [[ -f "$SCRIPTS_DIR/setup-plugins.txt" ]]; then
        while read -r plugin; do
            if [[ -n $plugin ]] && ! _wp plugin is-installed "$plugin"; then
                result=$(_wp plugin install "$plugin" | head -n 1)
                echo -e "\e[1;36mInfo:\e[0m $result"
            fi
        done < "$SCRIPTS_DIR/setup-plugins.txt"

        unset plugin result
    fi

    if ((${#plugins[@]} != 0 )); then
        for plugin in "${plugins[@]}"; do
            result=$(_wp plugin install "$plugin" | head -n 1)
            echo -e "\e[1;36mInfo:\e[0m $result"
        done

        plugins_to_activate+=("${plugins[@]}")

        unset plugin result
    fi

    if ((${#plugins_to_activate[@]} != 0 )); then
        _wp plugin activate ${plugins_to_activate[@]}
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
    e_start 'Set up themes'
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
            result=$(_wp theme install "$theme" --version="$theme_version" | head -n 1)
            echo -e "\e[1;36mInfo:\e[0m $result"

            continue
        fi

        themes+=("$theme")
    done

    unset theme result

    if [[ -f "$SCRIPTS_DIR/setup-themes.txt" ]]; then
        while read -r theme; do
            if [[ -n $theme ]] && ! _wp theme is-installed "$theme"; then
                result=$(_wp theme install "$theme" | head -n 1)
                echo -e "\e[1;36mInfo:\e[0m $result"
            fi
        done < "$SCRIPTS_DIR/setup-themes.txt"

        unset theme result
    fi

    if ((${#themes[@]} != 0 )); then
        for theme in "${themes[@]}"; do
            result=$(_wp theme install "$theme" | head -n 1)
            echo -e "\e[1;36mInfo:\e[0m $result"
        done

        unset theme result
    fi

    SITE_DEFAULT_THEME=${SITE_DEFAULT_THEME:-}

    if [[ -n "$SITE_DEFAULT_THEME" ]] && _wp theme is-installed "$SITE_DEFAULT_THEME"; then
        _wp theme activate $SITE_DEFAULT_THEME
    fi
    e_end
fi

if [[ ${MULTISITE_ENABLED:-0} -eq 1 ]]; then
    e_start "Set up multiSite"

    if _wp core is-installed --network; then
        echo -e "\e[1;36mNotice:\e[0m Multisite is already installed."
    else
        _wp core multisite-convert

        # https://developer.wordpress.org/advanced-administration/server/web-server/httpd/#multisite
        cat "$ASSET_DIR/.htaccess.multisite" > "$INSTALL_DIR/.htaccess"
        echo 'Update .htaccess.'
    fi

    if ((${#plugins_to_activate[@]} != 0 )); then
        _wp plugin activate ${plugins_to_activate[@]} --network
    fi

    if [[ -n "$SITE_DEFAULT_THEME" ]] && _wp theme is-installed "$SITE_DEFAULT_THEME"; then
        _wp theme enable $SITE_DEFAULT_THEME --network
    fi

    e_end
fi

if [[ -n "${TRIM_PLUGINS:-}" ]]; then
    e_start 'Cleanup'
    TRIM_PLUGINS=${TRIM_PLUGINS:-''}
    to_removes=()

    for to_remove in ${TRIM_PLUGINS//,/ }; do
        if _wp plugin is-installed "$to_remove"; then
            to_removes+=("$to_remove")
        fi
    done

    if ((${#to_removes[@]} != 0 )); then
        _wp plugin uninstall ${to_removes[@]}
    fi
    e_end
fi

e_start 'Verify installation'
_wp core version --extra
echo "Site URL: ${SITE_URL}"
e_end
