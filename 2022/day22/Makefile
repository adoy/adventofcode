run: php

.PHONY: clean
clean: ;

test: $(BIN)
	php d22p1.php < test.txt
	php d22p2.php 4 < test.txt

php:
	php d22p1.php < input.txt
	php d22p2.php 50 < input.txt
