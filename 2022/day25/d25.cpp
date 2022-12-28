#include <cmath>
#include <iostream>
#include <string>

class Snafu {
  friend std::ostream &operator<<(std::ostream &os, const Snafu &s);

private:
  long long x;

public:
  Snafu(const long long x = 0) : x(x) {}
  Snafu(const std::string &s) {
    x = 0;
    size_t length = s.length();
    for (size_t i = 0; i < length; i++) {
      int t{};
      switch (s[length - 1 - i]) {
      case '2':
        t = 2;
        break;
      case '1':
        t = 1;
        break;
      case '0':
        t = 0;
        break;
      case '-':
        t = -1;
        break;
      case '=':
        t = -2;
        break;
      }
      x += t * pow(5, i);
    }
  }

  Snafu operator+=(const Snafu &s) {
    x += s.x;
    return *this;
  }

  std::string toString() const {
    long long x = this->x;
    char d{};
    std::string str{};
    while (x > 0) {
      x += 2;
      switch (x % 5) {
      case 4:
        d = '2';
        break;
      case 3:
        d = '1';
        break;
      case 2:
        d = '0';
        break;
      case 1:
        d = '-';
        break;
      case 0:
        d = '=';
        break;
      }
      str = d + str;
      x /= 5;
    }
    return str;
  }
};

std::istream &operator>>(std::istream &is, Snafu &s) {
  std::string line;
  std::getline(is, line);
  s = Snafu(line);
  return is;
}

std::ostream &operator<<(std::ostream &os, const Snafu &s) {
  os << s.toString();
  return os;
}

int main() {
  Snafu res{}, n{};

  while (std::cin >> n) {
    res += n;
  }
  std::cout << "Result 1: " << res << std::endl;
}
