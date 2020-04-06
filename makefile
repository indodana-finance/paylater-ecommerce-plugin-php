# Opencart v1
# ----------------------------------
opencartv1_build_dir=.build/opencartv1/upload
opencartv1_dir = ./opencartv1

opencartv1-install-dependencies:
	cd ./opencartv1/ && composer install

opencartv1-build:
	./build-opencartv1

opencartv1-test:
	$(opencartv1_dir)/vendor/bin/

opencartv1-serve: opencartv1-build
	php5.6 -S localhost:6101 -t $(opencartv1_build_dir)

opencartv1-log-tail:
	tail -f ./$(opencartv1_build_dir)/system/library/indodana/log/indodana.log



# Opencart v2
# ----------------------------------

opencartv2_build_dir=.build/opencartv2/upload
opencartv2_dir = ./opencartv2

opencartv2-install-dependencies:
	cd ./opencartv2/ && composer install

opencartv2-build:
	./build-opencartv2

opencartv2-test:
	$(opencartv2_dir)/vendor/bin/

opencartv2-serve: opencartv2-build
	php5.6 -S localhost:6102 -t $(opencartv2_build_dir)

opencartv2-log-tail:
	tail -f ./$(opencartv2_build_dir)/system/library/indodana/log/indodana.log



# Woocommerce
# ----------------------------------
woocommerce_build_dir=.build/woocommerce/upload
woocommerce_dir = ./woocommerce

woocommerce-install-dependencies:
	cd ./woocommerce/ && composer install

woocommerce-build:
	./build-woocommerce

woocommerce-test:
	$(woocommerce)/vendor/bin/

woocommerce-serve: woocommerce-build
	php7.2 -S localhost:6201 -t $(woocommerce_build_dir)

woocommerce-log-tail:
	tail -f ./$(woocommerce_build_dir)/wp-content/plugins/indodana-payment/library/Indodana/Payment/log/info.log



# Magento 1
# ----------------------------------
magento1_build_dir=.build/magento1/upload
magento1_dir = ./magento1

magento1-install-dependencies:
	cd ./magento1/ && composer install

magento1-build:
	./build-magento1

magento1-test:
	$(magento1_dir)/vendor/bin/

magento1-serve: magento1-build
	php5.6 -S localhost:6301 -t $(magento1_build_dir)

magento1-log-tail:
	tail -f ./$(magento1_build_dir)/lib/Indodana/Payment/log/info.log



