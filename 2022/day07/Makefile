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
	php d7.php < input.txt

bash:
	./d7p1.sh < input.txt
	./d7p2.sh < input.txt
