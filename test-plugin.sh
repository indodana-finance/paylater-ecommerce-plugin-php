#!/bin/bash
set -e

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
COMPOSE_DIR="${SCRIPT_DIR}/testing/magento"
PLUGIN_SOURCE="${SCRIPT_DIR}/magentov2.4.4/plugin/app/code"
PLUGIN_DIR="/var/www/plugins"

VERSIONS=("m244" "m245" "m246" "m247" "m248")
VERSION_LABELS=("2.4.4-p13" "2.4.5-p14" "2.4.6-p15" "2.4.7-p10" "2.4.8-p5")
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m'

dc() {
    docker compose -f "${COMPOSE_DIR}/docker-compose.yml" --env-file "${SCRIPT_DIR}/.env" "$@"
}

usage() {
    echo "Magento Plugin Tester"
    echo ""
    echo "Usage: $0 <command> [options]"
    echo ""
    echo "Commands:"
    echo "  build               Build plugin from magentov2.4.4 source"
    echo "  up [version]        Start stack (m244|m245|m246|m247|m248|all)"
    echo "  down [version]      Stop stack"
    echo "  install [version]   Install Magento on running containers"
    echo "  plugin [version]    Build plugin + install into Magento"
    echo "  test [version]      Full run: build + up + install Magento + install plugin"
    echo "  status              Show running containers and URLs"
    echo "  logs [version]      Tail logs for a version"
    echo "  shell <version>     Open bash shell in PHP container"
    echo "  nuke [version]      Stop and destroy volumes (full reset)"
    echo ""
    echo "If version omitted, runs against all versions."
    echo ""
    echo "Examples:"
    echo "  $0 test              # Test plugin on all versions"
    echo "  $0 test m247         # Test plugin on 2.4.7 only"
    echo "  $0 plugin m244       # Build + install plugin on running 2.4.4"
    echo "  $0 shell m247        # SSH into 2.4.7 PHP container"
}

get_versions() {
    if [ -n "$1" ] && [ "$1" != "all" ]; then
        echo "$1"
    else
        echo "${VERSIONS[@]}"
    fi
}

get_label() {
    for i in "${!VERSIONS[@]}"; do
        if [ "${VERSIONS[$i]}" = "$1" ]; then
            echo "${VERSION_LABELS[$i]}"
            return
        fi
    done
    echo "$1"
}

check_container() {
    dc ps --format '{{.Name}}' 2>/dev/null | grep -q "php-$1"
}

do_build_plugin() {
    echo -e "${YELLOW}Building plugin from magentov2.4.4 source...${NC}"

    cd "${SCRIPT_DIR}"

    echo -e "  Installing dependencies..."
    (cd magentov2.4.4 && composer install --no-interaction --quiet 2>&1) || true

    echo -e "  Running build..."
    ./build-magentov2.4.4

    local build_output="${SCRIPT_DIR}/.build/magentov2.4.4/plugin/app/code"
    if [ ! -d "${build_output}/Indodana" ]; then
        echo -e "${RED}Build failed — ${build_output}/Indodana not found.${NC}"
        echo -e "${YELLOW}Falling back to source directory...${NC}"
        return 0
    fi

    local plugins_dir="${COMPOSE_DIR}/plugins"
    rm -rf "${plugins_dir}/Indodana"
    cp -r "${build_output}/Indodana" "${plugins_dir}/"

    echo -e "${GREEN}Plugin built and staged to testing/magento/plugins/${NC}"
}

do_up() {
    local profile="${1:-all}"
    echo -e "${YELLOW}Starting stack: ${profile}${NC}"
    dc --profile "$profile" up -d --build
    echo -e "${GREEN}Stack ready.${NC}"
}

do_down() {
    local profile="${1:-all}"
    echo -e "${YELLOW}Stopping stack: ${profile}${NC}"
    dc --profile "$profile" down
}

do_nuke() {
    local profile="${1:-all}"
    echo -e "${RED}Destroying stack + volumes: ${profile}${NC}"
    read -p "Are you sure? (y/N) " confirm
    if [ "$confirm" = "y" ] || [ "$confirm" = "Y" ]; then
        dc --profile "$profile" down -v
        echo -e "${GREEN}Done.${NC}"
    else
        echo "Cancelled."
    fi
}

do_install_magento() {
    local versions
    read -ra versions <<< "$(get_versions "$1")"

    for ver in "${versions[@]}"; do
        local label
        label=$(get_label "$ver")
        if ! check_container "$ver"; then
            echo -e "${RED}[${label}] Container php-${ver} not running. Run '$0 up ${ver}' first.${NC}"
            continue
        fi
        echo -e "${YELLOW}[${label}] Installing Magento...${NC}"
        if dc exec "php-${ver}" install-magento.sh; then
            echo -e "${GREEN}[${label}] Magento installed.${NC}"
        else
            echo -e "${RED}[${label}] Magento install FAILED.${NC}"
        fi
    done
}

do_install_plugin() {
    local versions
    read -ra versions <<< "$(get_versions "$1")"

    do_build_plugin

    local plugins_dir="${COMPOSE_DIR}/plugins"
    local plugins=()
    for dir in "${plugins_dir}"/*/; do
        [ -d "$dir" ] || continue
        dirname=$(basename "$dir")
        [ "$dirname" = ".DS_Store" ] && continue
        plugins+=("$dirname")
    done

    if [ ${#plugins[@]} -eq 0 ]; then
        echo -e "${YELLOW}No built plugins found. Using source mount directly...${NC}"
        plugins=("Indodana")
    fi

    echo -e "${YELLOW}Plugins: ${plugins[*]}${NC}"

    for ver in "${versions[@]}"; do
        local label
        label=$(get_label "$ver")
        if ! check_container "$ver"; then
            echo -e "${RED}[${label}] Container php-${ver} not running. Skipping.${NC}"
            continue
        fi

        echo -e "${YELLOW}[${label}] Installing plugin(s)...${NC}"

        for plugin in "${plugins[@]}"; do
            echo -e "  Copying ${plugin} to app/code/..."
            dc exec "php-${ver}" bash -c "
                cp -r ${PLUGIN_DIR}/${plugin} app/code/ && \
                find app/code/${plugin} -type d \( -name 'tests' -o -name 'test' -o -name 'Tests' -o -name 'Test' \) -exec rm -rf {} + 2>/dev/null || true
                if [ -d app/code/${plugin}/PayLater/vendor ]; then
                    echo '  Cleaning bundled vendor (keeping indodana SDK)...'
                    cd app/code/${plugin}/PayLater/vendor
                    for vendordir in */; do
                        case \"\$vendordir\" in
                            indodana/|composer/|autoload.php) ;;
                            *) rm -rf \"\$vendordir\" ;;
                        esac
                    done
                    cd /var/www/html
                fi
            "
        done

        echo -e "  Installing compatible dependencies..."
        dc exec "php-${ver}" composer require respect/validation:'^1.1' --no-update 2>&1 || true
        dc exec "php-${ver}" composer update respect/validation --with-dependencies 2>&1 || true

        echo -e "  Running setup:upgrade..."
        if dc exec "php-${ver}" php bin/magento setup:upgrade 2>&1; then
            echo -e "  Running di:compile..."
            if dc exec "php-${ver}" php bin/magento setup:di:compile 2>&1; then
                dc exec "php-${ver}" php bin/magento setup:static-content:deploy -f 2>&1
                dc exec "php-${ver}" php bin/magento cache:flush 2>&1
                dc exec "php-${ver}" bash -c "chmod -R 777 /var/www/html/var /var/www/html/generated /var/www/html/pub/static /var/www/html/pub/media" 2>&1
                echo -e "${GREEN}[${label}] Plugin installed OK.${NC}"
            else
                echo -e "${RED}[${label}] DI compile FAILED.${NC}"
            fi
        else
            echo -e "${RED}[${label}] setup:upgrade FAILED.${NC}"
        fi
    done
}

do_test() {
    local versions
    read -ra versions <<< "$(get_versions "$1")"
    local profile="${1:-all}"

    echo "============================================"
    echo "  Plugin Compatibility Test"
    echo "============================================"
    echo ""

    do_up "$profile"

    echo ""
    for ver in "${versions[@]}"; do
        local label
        label=$(get_label "$ver")
        echo -e "${YELLOW}[${label}] Waiting for services to be healthy...${NC}"
        sleep 5
    done

    do_install_magento "$1"
    echo ""
    do_install_plugin "$1"

    echo ""
    echo "============================================"
    echo "  Results"
    echo "============================================"
    for ver in "${versions[@]}"; do
        local label port
        label=$(get_label "$ver")
        port="8${ver:1}"
        if check_container "$ver"; then
            if dc exec "php-${ver}" php bin/magento module:status 2>/dev/null | grep -q "Indodana_PayLater"; then
                echo -e "${GREEN}  [${label}] ✓ Plugin active — http://localhost:${port}${NC}"
            else
                echo -e "${RED}  [${label}] ✗ Plugin not active${NC}"
            fi
        else
            echo -e "${RED}  [${label}] ✗ Container not running${NC}"
        fi
    done
    echo ""
}

do_status() {
    echo "============================================"
    echo "  Magento Plugin Tester — Status"
    echo "============================================"
    for i in "${!VERSIONS[@]}"; do
        local ver="${VERSIONS[$i]}"
        local label="${VERSION_LABELS[$i]}"
        local port="8${ver:1}"
        if check_container "$ver"; then
            echo -e "${GREEN}  [${label}] Running — http://localhost:${port}${NC}"
        else
            echo -e "${RED}  [${label}] Stopped${NC}"
        fi
    done
    echo ""
    echo "  RabbitMQ:  http://localhost:15672"
    echo "  MailHog:   http://localhost:8025"
    echo ""
}

do_logs() {
    local ver="${1:-m247}"
    dc logs -f "php-${ver}" "nginx-${ver}"
}

do_shell() {
    if [ -z "$1" ]; then
        echo "Usage: $0 shell <version>"
        echo "Example: $0 shell m247"
        exit 1
    fi
    dc exec "php-$1" bash
}

# ─── Main ───────────────────────────────────────────────────────
case "${1:-}" in
    build)   do_build_plugin ;;
    up)      do_up "$2" ;;
    down)    do_down "$2" ;;
    install) do_install_magento "$2" ;;
    plugin)  do_install_plugin "$2" ;;
    test)    do_test "$2" ;;
    status)  do_status ;;
    logs)    do_logs "$2" ;;
    shell)   do_shell "$2" ;;
    nuke)    do_nuke "$2" ;;
    *)       usage ;;
esac
