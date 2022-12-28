#include <iostream>
#include <vector>

using namespace std;

static char findCommonChar2(const string &s1, const string &s2) {
  for (int i = 0; i < (int)s1.length(); i++) {
    for (int j = 0; j < (int)s2.length(); j++) {
      if (s1[i] == s2[j]) {
        return s1[i];
      }
    }
  }
  return 0;
}

static char findCommonChar3(const string &s1, const string &s2, const string &s3) {
  for (int i = 0; i < (int)s1.length(); i++) {
    for (int j = 0; j < (int)s2.length(); j++) {
      for (int k = 0; k < (int)s3.length(); k++) {
        if (s1[i] == s2[j] && s2[j] == s3[k]) {
          return s1[i];
        }
      }
    }
  }
  return 0;
}

inline static int getCharValue(const char c) { return (c > 'Z') ? c - 'a' + 1 : c - 'A' + 27; }

int main() {
  string line;
  vector<string> lines;
  int res1 = 0, res2 = 0;

  while (getline(cin, line)) {
    lines.push_back(line);
  }
  lines.shrink_to_fit();

  for (const auto &line : lines) {
    string left = line.substr(0, line.size() / 2);
    string right = line.substr(line.size() / 2);
    res1 += getCharValue(findCommonChar2(left, right));
  }

  for (int i = 0; i < (int)lines.size(); i += 3) {
    string s1 = lines[i];
    string s2 = lines[i + 1];
    string s3 = lines[i + 2];
    res2 += getCharValue(findCommonChar3(s1, s2, s3));
  }
  cout << "Result 1: " << res1 << endl << "Result 2: " << res2 << endl;
}
