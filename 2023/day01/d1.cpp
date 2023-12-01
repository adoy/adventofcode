#include <iostream>
#include <string>
#include <vector>

using namespace std;

int main() {
  string line;
  unsigned int res = 0;

  vector<pair<string, unsigned int>> v{
      {"1", 1},   {"2", 2},     {"3", 3},     {"4", 4},    {"5", 5},
      {"6", 6},   {"7", 7},     {"8", 8},     {"9", 9},    {"0", 0},
      {"one", 1}, {"two", 2},   {"three", 3}, {"four", 4}, {"five", 5},
      {"six", 6}, {"seven", 7}, {"eight", 8}, {"nine", 9}, {"zero", 0}};

  while (getline(cin, line)) {
    unsigned int first = 0;
    unsigned int last = 0;
    size_t firstPos = string::npos;
    size_t lastPos = string::npos;
    for (auto &p : v) {
      if (line.find(p.first) != string::npos) {
        if (line.find(p.first) < firstPos || firstPos == string::npos) {
          first = p.second;
          firstPos = line.find(p.first);
        }
      }
      if (line.rfind(p.first) != string::npos) {
        if (line.rfind(p.first) > lastPos || lastPos == string::npos) {
          last = p.second;
          lastPos = line.rfind(p.first);
        }
      }
    }
    res += (first * 10 + last);
  }
  cout << "Resultat: " << res << endl;
}
