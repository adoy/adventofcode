SRC=$(wildcard *.cpp)
BIN=$(SRC:.cpp=)
GXX=g++
CXXFLAGS=-std=c++11 -Wall -Wextra -Werror -pedantic -O3 -g

run: $(BIN)
	./$(BIN) 2000000 4000000 < input.txt

test: $(BIN)
	./$(BIN) 10 20 < test.txt

%: %.cpp
	$(GXX) $(CXXFLAGS) $< -o $@

clean:
	rm -f $(BIN)

php:
	php -dxdebug.mode=off -dopcache.jit_buffer_size=5M d15.php 2000000 4000000 < input.txt

