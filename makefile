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

.PHONEY: opencart-v1-install-dependencies opencart-v1-build opencart-v1-serve opencart-v1-log-tail

