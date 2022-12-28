#include <iostream>
#include <set>

using namespace std;

class Knot {
private:
  friend class Rope;
  int x;
  int y;
  Knot *next;
  set<pair<int, int>> visited;

  Knot(Knot *next = nullptr) : x(0), y(0), next(next) {
    visited.insert({x, y});
  }
  void move(int h, int v, int distance = 1);
  void pullTowards(Knot &other);
  bool isTouching(const Knot &other) const;

public:
  int getVisitedCount() const { return visited.size(); }
};

void Knot::move(int h, int v, int distance) {
  do {
    visited.insert({x += h, y += v});
    if (nullptr != next)
      pullTowards(*next);
  } while (--distance > 0);
}

void Knot::pullTowards(Knot &other) {
  if (!isTouching(other)) {
    int h = x < other.x ? -1 : x > other.x ? 1 : 0;
    int v = y < other.y ? -1 : y > other.y ? 1 : 0;
    other.move(h, v);
  }
}

bool Knot::isTouching(const Knot &other) const {
  return abs(x - other.x) <= 1 && abs(y - other.y) <= 1;
}

class Rope {
  Knot *head;
  Knot *tail;

public:
  Rope(int length);
  ~Rope();
  void move(char direction, int distance);
  Knot *getTail() const { return tail; }
};

Rope::Rope(int length) {
  tail = head = new Knot();
  for (int i = 1; i < length; i++) {
    head = new Knot(head);
  }
}

Rope::~Rope() {
  while (head) {
    Knot *tmp = head;
    head = head->next;
    delete tmp;
  }
}

void Rope::move(const char direction, const int distance) {
  switch (direction) {
  case 'U':
    head->move(0, -1, distance);
    break;
  case 'D':
    head->move(0, 1, distance);
    break;
  case 'L':
    head->move(-1, 0, distance);
    break;
  case 'R':
    head->move(1, 0, distance);
    break;
  }
}

int main() {
  int distance{};
  char direction{};

  Rope smallRope(2);
  Rope longRope(10);
  while (cin >> direction >> distance) {
    smallRope.move(direction, distance);
    longRope.move(direction, distance);
  }

  cout << "Result 1: " << smallRope.getTail()->getVisitedCount() << endl;
  cout << "Result 2: " << longRope.getTail()->getVisitedCount() << endl;
}
