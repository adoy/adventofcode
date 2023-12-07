run: php

.PHONY: clean
clean: ;

test: $(BIN)
	php -dxdebug.mode=off -dopcache.jit_buffer_size=5M d7p1.php < test.txt
	php -dxdebug.mode=off -dopcache.jit_buffer_size=5M d7p2.php < test.txt

php:
	php -dxdebug.mode=off -dopcache.jit_buffer_size=5M d7p1.php < input.txt
	php -dxdebug.mode=off -dopcache.jit_buffer_size=5M d7p2.php < input.txt
