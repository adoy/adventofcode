#include <stdio.h>
#include <stdlib.h>

#define MAX_N 5000

typedef struct number {
  long long v;
  struct number *next;
} number_t;

number_t *readInput() {
  long long n;
  number_t *list = NULL;
  number_t *current, *prev = NULL;

  while (scanf("%lld", &n) != EOF) {
    current = (number_t *)malloc(sizeof(number_t));
    if (list == NULL)
      list = current;
    current->v = n;
    current->next = NULL;
    if (prev != NULL)
      prev->next = current;
    prev = current;
  }

  return list;
}

void freeList(number_t *list) {
  number_t *current = list;
  while (current != NULL) {
    number_t *next = current->next;
    free(current);
    current = next;
  }
}

int findPosition(number_t **numbers, int count, number_t *num) {
  for (int i = 0; i < count; i++) {
    if (numbers[i] == num)
      return i;
  }

  return -1;
}

int findPositionByValue(number_t **numbers, int count, long long v) {
  for (int i = 0; i < count; i++) {
    if (numbers[i]->v == v)
      return i;
  }

  return -1;
}

// Modulo operation that ensure that the modulo is positive
#define modulo(a, b) (((a % b) + b) % b)

void decrypt(number_t **numbers, int count, number_t *num) {
  while (num != NULL) {
    int pos = findPosition(numbers, count, num);
    int newPos = modulo((pos + num->v - 1), (count - 1)) + 1;
    int inc = (pos < newPos) ? 1 : -1;
    for (int i = pos; i != newPos; i += inc) {
      number_t *tmp = numbers[i];
      numbers[i] = numbers[i + inc];
      numbers[i + inc] = tmp;
    }

    num = num->next;
  }
}

long long part1(number_t *initialNumbers) {
  int count = 0;
  number_t *n = initialNumbers;
  number_t *numbers[MAX_N];

  while (n != NULL) {
    numbers[count++] = n;
    n = n->next;
  }

  decrypt(numbers, count, initialNumbers);

  long long r = 0;
  int z = findPositionByValue(numbers, count, 0);
  for (int i = 1; i <= 3; i++) {
    r += numbers[(z + i * 1000) % count]->v;
  }

  return r;
}

long long part2(number_t *initialNumbers) {
  int count = 0;
  number_t *n = initialNumbers;
  number_t *numbers[MAX_N];

  while (n != NULL) {
    numbers[count++] = n;
    n->v *= 811589153;
    n = n->next;
  }

  for (int i = 0; i < 10; i++) {
    decrypt(numbers, count, initialNumbers);
  }

  long long r = 0;
  int z = findPositionByValue(numbers, count, 0);
  for (int i = 1; i <= 3; i++) {
    r += numbers[(z + i * 1000) % count]->v;
  }

  return r;
}

int main() {
  number_t *initialNumbers = readInput();
  long long r1, r2;
  r1 = part1(initialNumbers);
  r2 = part2(initialNumbers);
  printf("Result 1: %lld\nResult 2: %lld\n", r1, r2);
  freeList(initialNumbers);
}
