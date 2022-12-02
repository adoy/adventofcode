CC=gcc
CFLAGS=-Wall -Wextra -Werror -O3

CSRC=$(wildcard *.c)
CBIN=$(CSRC:.c=c)

run: $(CBIN)
	./$(CBIN) < input.txt

%c: %.c
	$(CC) $(CFLAGS) -o $@ $<

test: $(CBIN)
	./$(CBIN) < test.txt

clean:
	rm -f $(CBIN)

php:
	php d2.php < input.txt

bash:
	./d2p1.sh < input.txt
	./d2p2.sh < input.txt
