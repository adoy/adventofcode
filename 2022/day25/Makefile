SRC=$(wildcard *.cpp)
BIN=$(SRC:.cpp=)
	GXX=g++
	CXXFLAGS=-Wall -Wextra -Werror -pedantic -O3 -g

run: $(BIN)
	./$(BIN) < input.txt

test: $(BIN)
	./$(BIN) < test.txt

%: %.cpp
	$(GXX) $(CXXFLAGS) $< -o $@

clean:
	rm -f $(BIN)

php:
	php d25.php < input.txt
