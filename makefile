opencart_v1_dir=.build/opencartv1/dev/opencartv1/upload

build-opencart-v1:
	./cli/svctl build opencartv1 dev

serve-opencart-v1: build-opencart-v1
	php5.6 -S localhost:8001 -t $(opencart_v1_dir)

log-tail-opencart-v1:
	tail -f ./$(opencart_v1_dir)/system/library/indodana/log/indodana.log

.PHONEY: build-opencart-v1 serve-opencart-v1
