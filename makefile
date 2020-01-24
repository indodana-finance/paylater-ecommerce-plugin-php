# Opencart v1
# ----------------------------------
opencart_v1_build_dir=.build/opencartv1/dev/opencartv1/upload
opencartv1_dir = ./opencartv1

opencart-v1-install-dependencies:
	cd ./opencartv1/
	composer install
	cd ..

opencart-v1-build:
	./cli/svctl build opencartv1 dev

opencart-v1-test:
	$(opencartv1_dir)/vendor/bin/

opencart-v1-serve: opencart-v1-build
	php5.6 -S localhost:8001 -t $(opencart_v1_build_dir)

opencart-v1-log-tail:
	tail -f ./$(opencart_v1_build_dir)/system/library/indodana/log/indodana.log



# Woocommerce
# ----------------------------------
woocommerce_build_dir=.build/woocommerce/dev/woocommerce/upload
woocommerce_dir = ./woocommerce

woocommerce-install-dependencies:
	cd ./woocommerce/
	composer install
	cd ..

woocommerce-build:
	./cli/svctl build woocommerce dev

woocommerce-test:
	$(woocommerce)/vendor/bin/

woocommerce-serve: woocommerce-build
	php -S localhost:8001 -t $(woocommerce_build_dir)

woocommerce-log-tail:
	tail -f ./$(woocommerce_build_dir)/wp-content/plugins/indodana-payment/library/Indodana/Payment/log/info.log



.PHONEY: opencart-v1-install-dependencies opencart-v1-build opencart-v1-serve opencart-v1-log-tail

