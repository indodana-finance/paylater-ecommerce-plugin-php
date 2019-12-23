build-opencart-v1:
	./cli/svctl build opencartv1 dev

serve-opencart-v1: build-opencart-v1
	php5.6 -S localhost:8001 -t .build/opencartv1/dev/opencartv1/upload/

.PHONEY: build-opencart-v1 serve-opencart-v1
