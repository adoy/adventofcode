run: php

.PHONY: clean
clean: ;

test: $(BIN)
	php -dxdebug.mode=off -dopcache.jit_buffer_size=5M d15.php 10 20< test.txt

php:
	php -dxdebug.mode=off -dopcache.jit_buffer_size=5M d15.php 2000000 4000000 < input.txt
