#include <stdio.h>
#include <stdlib.h>

#define LINE_BUFFER_SIZE 150

#define push(stack, c) (stack.stack[stack.size++] = (c))
#define pop(stack) (stack.stack[--stack.size])

typedef struct stack {
  int size;
  char stack[LINE_BUFFER_SIZE];
} stack_t;

static int getLineScore(char *str) {
  int score = 0;
  stack_t stack = {0};

  while (*str) {
    switch (*str) {
      case '(':
      case '[':
      case '{':
      case '<':
        push(stack, *str);
        break;
      case ')':
        if (pop(stack) != '(')
          score += 3;
        break;
      case ']':
        if (pop(stack) != '[')
          score += 57;
        break;
      case '}':
        if (pop(stack) != '{')
          score += 1197;
        break;
      case '>':
        if (pop(stack) != '<')
          score += 25137;
        break;
    }
    str++;
  }

  return score;
}

int main() {
  char line[LINE_BUFFER_SIZE + 2];
  int score = 0;
  while (fgets(line, LINE_BUFFER_SIZE + 2, stdin))
    score += getLineScore(line);

  printf("Result: %d\n", score);
  return 0;
}
