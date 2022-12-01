SRC=$(wildcard *.cpp)
BIN=$(SRC:.cpp=)
GXX=g++
CXXFLAGS=-std=c++11 -Wall -Wextra -Werror -pedantic -O3 -g

run: $(BIN)
	./$(BIN) < input.txt

test: $(BIN)
	./$(BIN) < test.txt

%: %.cpp
	$(GXX) $(CXXFLAGS) $< -o $@

clean:
	rm -f $(BIN)

bash:
	./d1.sh < input.txt

php:
	php d1.php < input.txt
