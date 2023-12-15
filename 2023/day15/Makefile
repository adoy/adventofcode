run: php

.PHONY: clean
clean: ;

test: $(BIN)
	php -dxdebug.mode=off -dopcache.jit_buffer_size=5M d15.php < test.txt

php:
	php -dxdebug.mode=off -dopcache.jit_buffer_size=5M d15.php < input.txt
