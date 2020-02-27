# Opencart v1
# ----------------------------------
opencart_v1_build_dir=.build/opencartv1/dev/opencartv1/upload
opencart_v1_dir = ./opencartv1

opencart-v1-install-dependencies:
	cd ./opencartv1/ && composer install

opencart-v1-build:
	./cli/svctl build opencartv1 dev

opencart-v1-test:
	$(opencart_v1_dir)/vendor/bin/

opencart-v1-serve: opencart-v1-build
	php5.6 -S localhost:6011 -t $(opencart_v1_build_dir)

opencart-v1-log-tail:
	tail -f ./$(opencart_v1_build_dir)/system/library/indodana/log/indodana.log



# Woocommerce
# ----------------------------------
woocommerce_build_dir=.build/woocommerce/dev/woocommerce/upload
woocommerce_dir = ./woocommerce

woocommerce-install-dependencies:
	cd ./woocommerce/ && composer install

woocommerce-build:
	./cli/svctl build woocommerce dev

woocommerce-test:
	$(woocommerce)/vendor/bin/

woocommerce-serve: woocommerce-build
	php7.2 -S localhost:6021 -t $(woocommerce_build_dir)

woocommerce-log-tail:
	tail -f ./$(woocommerce_build_dir)/wp-content/plugins/indodana-payment/library/Indodana/Payment/log/info.log



# Magento 1
# ----------------------------------
magento1_build_dir=.build/magento1/dev/magento1/upload
magento1_dir = ./magento1

magento1-install-dependencies:
	cd ./magento1/ && composer install

magento1-build:
	./cli/svctl build magento1 dev

magento1-test:
	$(magento1_dir)/vendor/bin/

magento1-serve: magento1-build
	php5.6 -S localhost:6031 -t $(magento1_build_dir)

magento1-log-tail:
	tail -f ./$(magento1_build_dir)/lib/Indodana/Payment/log/info.log



.PHONEY: opencart-v1-install-dependencies opencart-v1-build opencart-v1-serve opencart-v1-log-tail
