SHELL := /bin/bash

build-svctl: clean-svctl
	# Build svctl
	source .env.sv/bin/activate \
		&& pushd sv/src \
		&& pyinstaller -y main.spec \
		&& popd

	rm -rf sv/src/build/

	mv sv/src/dist sv/

	# Statically link the binary with staticx
	if ! which patchelf > /dev/null; then \
		echo "patchelf not installed. Executing 'sudo apt install patchelf'..." \
		&& sudo apt install -y patchelf; \
	fi

	source .env.sv/bin/activate \
		&& staticx sv/dist/main sv/dist/main-static

	mv sv/dist/main-static sv/dist/main

clean-svctl:
	rm -rf sv/src/build/
	rm -rf sv/src/dist/
	rm -rf sv/dist/

publish-svctl: build-svctl
	# Backup svctl directory
	rm -rf .sv.org
	cp -r sv .sv.org

	# Remove Python source code from the directory
	rm -rf sv/src

	# Publish
	bcl package publish sv

	# Restore the svctl directory
	rm -rf sv
	mv .sv.org sv
