CC=gcc
CFLAGS=-Wall -Wextra -Werror -O3

CSRC=$(wildcard *.c)
CBIN=d20

run: $(CBIN)
	./$(CBIN) < input.txt

$(CBIN): $(CSRC)

%c: %.c
	$(CC) $(CFLAGS) -o $@ $<

test: $(CBIN)
	./$(CBIN) < test.txt

clean:
	rm -f $(CBIN)
