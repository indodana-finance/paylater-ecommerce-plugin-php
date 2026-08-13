# Indodana Paylater Ecommerce Plugin for PHP

This repository contains plugins for most used ecommerce framework based on PHP. See [here](https://dev.indodana.id/indodana-paylater/integration/e-commerce-plugins) for supported e-commerce frameworks, versions, system requirements, etc.

## Which Magento 2 plugin do I need?

Magento 2.4.4 dropped PHP 7 support, and no PHP version runs both 2.4.3 and 2.4.4 — so
the Magento 2 plugin ships as two separate modules. Pick by your Magento version:

| Your Magento | Plugin directory | PHP | Release asset |
|---|---|---|---|
| 2.3.x, 2.4.0 - 2.4.3 | [`magentov2.4.0`](magentov2.4.0) | 7.3 / 7.4 | `magento_v2.4.0-*.zip` |
| 2.4.4 - 2.4.8 | [`magentov2.4.4`](magentov2.4.4) | **8.1 / 8.2 / 8.3 — PHP 7 not supported** | `magento_v2.4.4-*.zip` |

## Product Specs

- [Features](https://github.com/indodana/paylater-ecommerce-plugin-php/wiki/%5BProduct%5D-Features)
- [Release Guideline](https://github.com/indodana/paylater-ecommerce-plugin-php/wiki/%5BProduct%5D-Release-Guideline)

## Tech Specs

### General

- [Architecture](https://github.com/indodana/paylater-ecommerce-plugin-php/wiki/%5BTech%5D-General-Architecture)

### Development

- [Initial Setup](https://github.com/indodana/paylater-ecommerce-plugin-php/wiki/%5BTech%5D-Initial-Setup)
- [Project Structure](https://github.com/indodana/paylater-ecommerce-plugin-php/wiki/%5BTech%5D-Project-Structure)
- [Coding Convention](https://github.com/indodana/paylater-ecommerce-plugin-php/wiki/%5BTech%5D-Coding-Convention)

### Testing

- [Test Cases](https://github.com/indodana/paylater-ecommerce-plugin-php/wiki/%5BTech%5D-Test-Cases)
- [List of Tested Ecommerce Plugin Versions](https://github.com/indodana/paylater-ecommerce-plugin-php/wiki/%5BTech%5D-List-of-Tested-Ecommerce-Plugin-Versions)

## Plugin Testing

Docker-based test environments for verifying plugin compatibility across multiple platform versions. Each test environment builds the plugin from source before installing.

Currently supported:
- **Magento v2.4.4** — `magentov2.4.4` plugin tested against Magento 2.4.4 through 2.4.8

Planned:
- WooCommerce (woocommerce, woocommercev4, woocommercev5)
- OpenCart (opencartv1, opencartv2, opencartv2.3)
- PrestaShop (prestashopv1)

### Magento v2.4.4 Test Environment

| Profile | Magento     | PHP | Search Engine      | Port |
|---------|-------------|-----|--------------------|------|
| `m244`  | 2.4.4-p13   | 8.1 | Elasticsearch 7.17 | 8244 |
| `m245`  | 2.4.5-p14   | 8.1 | Elasticsearch 7.17 | 8245 |
| `m246`  | 2.4.6-p15   | 8.2 | OpenSearch 2.5      | 8246 |
| `m247`  | 2.4.7-p10   | 8.3 | OpenSearch 2.12     | 8247 |
| `m248`  | 2.4.8-p5    | 8.3 | OpenSearch 2.19     | 8248 |

### Quick Start

```bash
# 1. Configure auth
cp .env.example .env
# Edit .env — set COMPOSER_AUTH with your Adobe marketplace keys

# 2. Test plugin on a specific version (builds from magentov2.4.4 source)
./test-plugin.sh test m247

# 3. Test on all versions
./test-plugin.sh test
```

### Commands

```bash
./test-plugin.sh build               # Build plugin from source
./test-plugin.sh up [version]        # Start stack
./test-plugin.sh down [version]      # Stop stack
./test-plugin.sh install [version]   # Install Magento
./test-plugin.sh plugin [version]    # Build plugin + install into Magento
./test-plugin.sh test [version]      # Full run: build + up + install + plugin
./test-plugin.sh status              # Show running containers
./test-plugin.sh logs [version]      # Tail logs
./test-plugin.sh shell <version>     # Open bash in PHP container
./test-plugin.sh nuke [version]      # Stop and destroy volumes
```

### Services

- **RabbitMQ Management**: http://localhost:15672 (magento/magento)
- **MailHog** (dev email): http://localhost:8025
