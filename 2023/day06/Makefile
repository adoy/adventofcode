run: php

.PHONY: clean
clean: ;

test: $(BIN)
	php -dxdebug.mode=off -dopcache.jit_buffer_size=5M d6.php < test.txt

php:
	php -dxdebug.mode=off -dopcache.jit_buffer_size=5M d6.php < input.txt
